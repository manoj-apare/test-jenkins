<?php

namespace Drupal\cypress_checkout_flow\Controller;

use Drupal\commerce_store\Entity\Store;
use Drupal\Core\Controller\ControllerBase;
use Drupal\commerce_price\Price;
use Drupal\commerce_promotion\Entity\Coupon;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_promotion\Entity\Promotion;
use Drupal\Core\Url;
use Drupal\cypress_custom_address\CypressOrderProcessor;
use Drupal\user\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\commerce_cart\CartProviderInterface;
use Drupal\commerce_cart\CartManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Controller routines for products in add to cart.
 */
class CypressAddToCartController extends ControllerBase {

  /**
   * The cart manager.
   *
   * @var \Drupal\commerce_cart\CartManagerInterface
   */
  protected $cartManager;

  /**
   * The cart provider.
   *
   * @var \Drupal\commerce_cart\CartProviderInterface
   */
  protected $cartProvider;

  /**
   * Constructs a new CartController object.
   *
   * @param \Drupal\commerce_cart\CartManagerInterface $cart_manager
   *   The cart manager.
   *
   *    \Drupal\commerce_cart\CartProviderInterface $cart_provider
   *   The cart provider.
   */
  public function __construct(CartManagerInterface $cart_manager, CartProviderInterface $cart_provider) {
    $this->cartManager = $cart_manager;
    $this->cartProvider = $cart_provider;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('commerce_cart.cart_manager'),
      $container->get('commerce_cart.cart_provider')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function addToCart($mpn, $promocode) {
    $product_id = $this->getProductId($mpn);
    $product_obj = Product::load($product_id);
    $product_variation_id = $product_obj->get('variations')->target_id;
    $store_id = $product_obj->get('stores')->target_id;
    $variationobj = ProductVariation::load($product_variation_id);
    $store = Store::load($store_id);
    $cart = $this->cartProvider->getCart('default', $store);

    $user = \Drupal::currentUser();
    if ($user->id()) {
      $unifiedcart_service = \Drupal::service('commerce_combine_carts.cart_unifier');
      $unifiedcart_service->combineUserCarts(User::load($user->id()));
    }

    $query = \Drupal::database()->select('commerce_promotion_coupon', 'cp');
    $query->fields('cp', ['code']);
    $query->join('commerce_promotion_field_data', 'cpf', 'cpf.promotion_id = cp.promotion_id');
    $query->condition('cpf.name', $mpn);
    $results = $query->execute()->fetchAll();
    $code = [];
    foreach ($results as $result) {
      $code[] = $result->code;
    }
    $my_sample_promo = $code;
    $promotion_id = '';
    $query = \Drupal::database()
      ->select('commerce_promotion_coupon', 'cpc');
    $query->fields('cpc', ['promotion_id']);
    $query->condition('cpc.code', $promocode);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $promotion_id = $result->promotion_id;
    }
    $promotion = Promotion::load($promotion_id);
    if (!empty($promotion)) {
      $quantity = $promotion->get('offer')->target_plugin_configuration['quantity'];
      $coupons = $promotion->getCouponIds();
      foreach ($coupons as $coupon) {
        $coupon_id = $coupon;
        $coupon_obj = Coupon::load($coupon_id);
      }
      $current_date = date('Y-m-d');
      $limit_promocode = new CypressOrderProcessor();
      $usage_count = $limit_promocode->countCoupon($promotion_id);
      if (in_array($promocode, $my_sample_promo)) {
        if ($promotion->getEndDate() >= $current_date) {
          if ($coupon_obj->getUsageLimit() > $usage_count) {
            if (empty($cart)) {
              $cart = $this->cartProvider->createCart('default', $store);
            }
            $cansample = $product_obj->get('field_can_sample')->value;
            $sample_category = $product_obj->get('field_sample_category')->value;
            if ($cansample == 1) {
              $query = \Drupal::database()
                ->select('commerce_product__field_price_one', 'cp1');
              $query->fields('cp1', ['	field_price_one_value']);
              $query->join('commerce_product__field_price_two', 'cp2', 'cp2.entity_id = cp1.entity_id');
              $query->fields('cp2', ['field_price_two_value']);
              $query->join('commerce_product__field_price_three', 'cp3', 'cp3.entity_id = cp1.entity_id');
              $query->fields('cp3', ['field_price_three_value']);
              $query->join('commerce_product__field_price_four', 'cp4', 'cp4.entity_id = cp1.entity_id');
              $query->fields('cp4', ['field_price_four_value']);
              $query->join('commerce_product__field_price_five', 'cp5', 'cp5.entity_id = cp1.entity_id');
              $query->fields('cp5', ['field_price_five_value']);
              $query->join('commerce_product__field_price_six', 'cp6', 'cp6.entity_id = cp1.entity_id');
              $query->fields('cp6', ['field_price_six_value']);
              $query->condition('cp1.entity_id', $product_id);
              $query->execute()->fetchAll();
              $results = $query->execute()->fetchAll();
              foreach ($results as $result) {
                $mpn_details = $result;
              }

              if ($quantity <= 9) {
                $discount_price_per_unit = round($mpn_details->field_price_one_value, 2);
              }
              elseif ($quantity >= 10 and $quantity <= 24) {
                $discount_price_per_unit = round($mpn_details->field_price_two_value, 2);
              }
              elseif ($quantity >= 25 and $quantity <= 99) {
                $discount_price_per_unit = round($mpn_details->field_price_three_value, 2);
              }
              elseif ($quantity >= 100 and $quantity <= 249) {
                $discount_price_per_unit = round($mpn_details->field_price_four_value, 2);
              }
              elseif ($quantity >= 250 and $quantity <= 999) {
                $discount_price_per_unit = round($mpn_details->field_price_five_value, 2);
              }
              else {
                $discount_price_per_unit = round($mpn_details->field_price_six_value, 2);
              }
              $msrp = (string) $discount_price_per_unit;
              $price = new Price($msrp, 'USD');
              $variationobj->setPrice($price);
              $variationobj->save();
            }

            elseif ($cansample == 2 && $sample_category != 'Kits') {
              $query = \Drupal::database()
                ->select('commerce_product__field_samplemsrp', 'csm');
              $query->fields('csm', ['field_samplemsrp_value']);
              $query->condition('csm.entity_id', $product_id);
              $results = $query->execute()->fetchAll();
              foreach ($results as $result) {
                $discount_price_per_unit = $result->field_samplemsrp_value;
              }
              $msrp = (string) $discount_price_per_unit;
              $price = new Price($msrp, 'USD');
              $variationobj->setPrice($price);
              $variationobj->save();
            }

            elseif ($cansample == 2 && $sample_category == 'Kits') {
              $query = \Drupal::database()
                ->select('commerce_product__field_kit_cost', 'ckc');
              $query->fields('ckc', ['field_kit_cost_value']);
              $query->condition('ckc.entity_id', $product_id);
              $results = $query->execute()->fetchAll();
              foreach ($results as $result) {
                $discount_price_per_unit = $result->field_kit_cost_value;
              }
              $msrp = (string) $discount_price_per_unit;
              $price = new Price($msrp, 'USD');
              $variationobj->setPrice($price);
              $variationobj->save();
            }
            // Process to place order programatically.
            $cart_manager = $this->cartManager->addEntity($cart, $variationobj);
          }
          else {
            drupal_set_message(t('Promocode cannot be added as usage limit execeeded'), 'error');
          }
        }
        else {
          drupal_set_message(t('Promocode cannot be added as end date execeeded'), 'error');
        }
      }
    }
    $response = new RedirectResponse(Url::fromRoute('commerce_cart.page')
      ->toString());
    return $response;

  }

  /**
   * {@inheritdoc}
   */
  public function addToCartFromD7() {
    $query = \Drupal::request()->query;
    $app = $query->get('app');
    $action = $query->get('action');
    $item_id = $query->get('itemID');
    $type = $query->get('type');
    if ($item_id) {
      $query = \Drupal::database()->select('commerce_product__field_mpn_id',
        'mpnid')
        ->fields('mpnid', ['entity_id'])
        ->condition('mpnid.field_mpn_id_value', $item_id);
      $query->join('commerce_product__field_can_sample', 'can_sample', 'mpnid.entity_id = can_sample.entity_id');
      $products = $query->condition('field_can_sample_value', [0, 2], 'NOT IN')
        ->execute()
        ->fetchAll();
      foreach ($products as $product) {
        $product_id = $product->entity_id;
      }
      if (isset($product_id)) {
        $user = \Drupal::currentUser();
        $carts = $this->cartProvider->getCarts($user);

        $user = \Drupal::currentUser();
        if ($user->id()) {
          $unifiedcart_service = \Drupal::service('commerce_combine_carts.cart_unifier');
          $unifiedcart_service->combineUserCarts(User::load($user->id()));
        }

        if (!empty($carts)) {
          $cart = array_shift($carts);
        }
        else {
          $store = Store::load(1);
          $cart = $this->cartProvider->createCart('default', $store);
        }
        $product = Product::load($product_id);
        $product_variation_id = $product->getVariationIds()[0];
        $purchasable_entity = ProductVariation::load($product_variation_id);
        $this->cartManager->addEntity($cart, $purchasable_entity);
        drupal_set_message('Product has been added to cart.', 'status');
      }
      else {
        drupal_set_message('No product found for the ID.', 'error');
      }
    }
    return new RedirectResponse('/cart');
  }

  /**
   * To get the product_id from mpn.
   */
  protected function getProductId($mpn) {
    $query = \Drupal::database()->select('commerce_product_field_data', 'pt');
    $query->fields('pt', ['product_id']);
    $query->condition('pt.title', $mpn);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $product_id = $result->product_id;
    }

    return $product_id;
  }

}
