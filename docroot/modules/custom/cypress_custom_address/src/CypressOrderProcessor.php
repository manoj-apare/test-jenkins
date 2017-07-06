<?php

namespace Drupal\cypress_custom_address;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\commerce_promotion\Entity\Coupon;
use Drupal\commerce_promotion\Entity\Promotion;
use Drupal\commerce_order\OrderProcessorInterface;
use Drupal\commerce_price\Price;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\yamlform\Entity\YamlFormSubmission;

/**
 * Order processor that modifies the cart.
 *
 * Provides an order processor that modifies the cart according to the business
 * logic for Parts.
 */
class CypressOrderProcessor implements OrderProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function process(OrderInterface $order) {
    // Get current user and user roles.
    $current_user_id = \Drupal::currentUser()->id();
    $user_roles = \Drupal::currentUser()->getRoles();
    $cypress_roles = array(
      '0' => 'sales_rep',
      '1' => 'all_distributors',
      '2' => 'cypress_employees',
    );
    $check_roles = array_intersect($cypress_roles, $user_roles);
    $order->setAdjustments([]);
    foreach ($order->getItems() as $order_item) {
      $product_variation = $order_item->getPurchasedEntity();
      $default_product_variation_id = $order_item->getPurchasedEntityId();
      if (!empty($product_variation)) {
        $product_id = $product_variation->get('product_id')->target_id;
        $product = Product::load($product_id);
        $product_type = $product->get('type')->target_id;
        $variation_ids = $product->getVariationIds();
        $quantity = $order_item->getQuantity();
        $product_title = $product->getTitle();

        $adjustments = $order_item->getAdjustments();
        foreach ($adjustments as $adjustment) {
          $adjust_type = $adjustment->getType();
          if ($adjust_type != 'cypress_promocode_quantity') {
            foreach ($variation_ids as $variation_id) {
              $variation_object = ProductVariation::load($variation_id);
              $get_part_quantity = $variation_object->get('weight')->number;
              $part_quantity = round($get_part_quantity);
              // The Part Quantity for Variant.
              $part_quantity = intval($part_quantity);
              $product_qty = round($quantity);
              // The Quantity to purchase the Part.
              $product_qty = intval($quantity);
              if ($part_quantity >= $product_qty && (!isset($prev_variation_quantity) || $prev_variation_quantity > $part_quantity)) {
                $prev_variation_quantity = $part_quantity;
                // Set new variation id.
                $current_variation_id = $variation_id;
              }
              // If product quantity is more than any part quantity.
              elseif ($part_quantity < $product_qty) {
                $current_variation_id = $variation_id;
              }
            }
            unset($prev_variation_quantity);
            // Show the new variation according to the product quantity.
            if ($current_variation_id != $default_product_variation_id) {
              $variation_object = ProductVariation::load($current_variation_id);
              $variation_price = $variation_object->getPrice();
              $order_item->get('purchased_entity')
                ->setValue(['target_id' => $current_variation_id], TRUE);
              $order_item->setUnitPrice($variation_price);
              $order_item->save();
            }
          }
        }
        // Custom Promocode application.
        if ($product_type == 'part') {
          $this->cypressPromocodeApplication($order_item, $default_product_variation_id, $product_variation);
        }
        /*
         * Cart Rules.
         */
        // Access to CAT_B users purchasing CAT_A products.
        if ($product_type == 'part' && (in_array('authenticated', $user_roles) && !empty($check_roles) ? TRUE : FALSE)) {
          $can_sample = $product->get('field_can_sample')->value;
          $product_price = $order_item->getUnitPrice();
          $product_unit_price = $product_price->getNumber();
          // CAT_A products.
          $item_total_price = $order_item->getTotalPrice()->getNumber();
          $adjustments = $order_item->getAdjustments();
          foreach ($adjustments as $adjustment) {
            $adjustment_type = $adjustment->getType();
            if ($adjustment_type == 'cypress_promocode_quantity') {
              $adjutsment_amount = $adjustment->getAmount()->getNumber();
              $amount = trim($adjutsment_amount, "-");
              $item_quantity = $order_item->getQuantity();
              $total_amount = $amount * $item_quantity;
              $balance_amount = $item_total_price - $total_amount;
              $balance_amount = number_format($balance_amount, 2, '.', '');
              // $balance_amount = number_format($balance_amount, '.', 2);.
              if ($can_sample == 1) {
                if ($product_unit_price < 20 && $quantity <= 10) {
                  $new_adjustment = $product_unit_price;
                }
                elseif ($product_unit_price < 20 && $quantity > 10) {
                  $new_adjustment = ($product_unit_price * 10) / $item_quantity;
                  if ($balance_amount > 0) {
                    if ($new_adjustment > $balance_amount) {
                      $cart_rule_amount = $balance_amount / $item_quantity;
                    }
                    else {
                      $cart_rule_amount = $new_adjustment / $item_quantity;
                    }
                    $adjustments[] = new Adjustment([
                      'type' => 'cypress_cart_rules',
                      'label' => 'Discounted Price - ' . $product_title,
                      'amount' => new Price('-' . $cart_rule_amount, 'USD'),
                    ]);
                    $order_item->setAdjustments($adjustments);
                    $order_item->save();
                  }
                }
              }
            }
          }
          if (empty($adjustments)) {
            if ($can_sample == 1) {
              if ($product_unit_price < 20 && $quantity <= 10) {
                $new_adjustment = $product_unit_price;
              }
              elseif ($product_unit_price < 20 && $quantity > 10) {
                $new_adjustment = ($product_unit_price * 10) / $quantity;
              }
              else {
                continue;
              }
              $adjustments[] = new Adjustment([
                'type' => 'cypress_cart_rules',
                'label' => 'Discounted Price - ' . $product_title,
                'amount' => new Price('-' . $new_adjustment, 'USD'),
              ]);
              $order_item->setAdjustments($adjustments);
              $order_item->save();
            }
          }
        }
      }
    }
  }

  /**
   * To Apply Promocode Adjustments for that specific product.
   *
   * @param object $order_item
   *   The order_item entity.
   * @param int $default_product_variation_id
   *   The purchased entity id of that order_item.
   * @param object $product_variation
   *   Product Variation entity.
   */
  public function cypressPromocodeApplication($order_item, $default_product_variation_id, $product_variation) {
    $product_var = ProductVariation::load($default_product_variation_id);
    $pro_title = $product_var->getTitle();
    $promotion_id = $this->getPromotionId($pro_title);
    $promotion = Promotion::load($promotion_id);
    $current_date = date('Y-m-d');
    // Get the Coupon Code.
    if (!empty($promotion)) {
      $coupons = $promotion->getCouponIds();
      foreach ($coupons as $coupon) {
        $coupon_id = $coupon;
        $coupon_obj = Coupon::load($coupon_id);
        $coupon_code = $coupon_obj->getCode();
      }
      $query = \Drupal::database()->select('yamlform_submission_data', 'ysd');
      $query->fields('ysd', ['sid']);
      $query->condition('ysd.value', $coupon_code);
      $results = $query->execute()->fetchAll();
      foreach ($results as $result) {
        $submission_id = $result->sid;
      }
      $submission = YamlFormSubmission::load($submission_id);
      if (empty($submission)) {
        return;
      }
      $submit_uid = $submission->get('uid')->target_id;
      $current_user = \Drupal::currentUser()->id();
      if ($submit_uid == $current_user) {
        if ($promotion->getEndDate() >= $current_date) {
          $usage_count = $this->countCoupon($promotion_id);
          if ($coupon_obj->getUsageLimit() > $usage_count) {
            $offer = $promotion->get('offer')->target_plugin_id;
            $promocode_amount = $promotion->get('offer')->target_plugin_configuration['amount'];
            $product_id = $promotion->get('offer')->target_plugin_configuration['product_id'];
            $quantity = $promotion->get('offer')->target_plugin_configuration['quantity'];
            if ($offer == 'commerce_promotion_product_quantity_fixed_off') {
              if ($product_variation->getProductId() == $product_id) {
                $unit_price = $order_item->getUnitPrice()->getNumber();
                if ($unit_price >= $promocode_amount) {
                  $price = ($promocode_amount * $quantity) / $quantity;
                  $price = (string) $price;
                }
                else {
                  $price = ($unit_price * $quantity) / $quantity;
                  $price = (string) $price;
                }
                $promocode_quantity_adjustment[] = new Adjustment([
                  'type' => 'cypress_promocode_quantity',
                  'label' => 'Promocode Quantity Discount',
                  'amount' => new Price('-' . $price, 'USD'),
                ]);
                $order_item->setAdjustments($promocode_quantity_adjustment);
                $order_item->setQuantity($quantity);
                $order_item->save();
              }
            }
          }
        }
      }
    }
  }

  /**
   * Get the Promotion id based on product title.
   */
  public function getPromotionId($title) {

    $query = \Drupal::database()->select('commerce_promotion_field_data', 'cp');
    $query->fields('cp', ['promotion_id']);
    $query->condition('cp.name', $title);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $promotion_id = $result->promotion_id;
    }

    return $promotion_id;
  }

  /**
   * Get the count of promocode.
   */
  public function countCoupon($promotion_id) {

    $query = \Drupal::database()->select('cypress_store_coupons', 'csc');
    $query->fields('csc', ['coupon_code']);
    $query->condition('csc.promotion_id', $promotion_id);
    $results = $query->execute()->fetchAll();
    $coupon_code = $results;
    $usage_count = count($coupon_code);

    return $usage_count;
  }

}
