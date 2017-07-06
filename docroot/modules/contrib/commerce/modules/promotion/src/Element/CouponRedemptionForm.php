<?php

namespace Drupal\commerce_promotion\Element;

use Drupal\commerce\Element\CommerceElementTrait;
use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Render\Element\FormElement;
use Drupal\commerce_promotion\Entity\PromotionInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\commerce_order\Adjustment;

/**
 * Provides a form element for redeeming a coupon.
 *
 * Usage example:
 * @code
 * $form['coupon'] = [
 *   '#type' => 'commerce_coupon_redemption_form',
 *   '#title' => t('Coupon code'),
 *   '#default_value' => $coupon_id,
 *   '#order_id' => $order_id,
 * ];
 * @endcode
 * The element value ($form_state->getValue('coupon')) will be the
 * coupon ID. Note that the order is not saved if the element was
 * submitted as a result of the main form being submitted. It is the
 * responsibility of the caller to update the order in that case.
 *
 * @FormElement("commerce_coupon_redemption_form")
 */
class CouponRedemptionForm extends FormElement {

  use CommerceElementTrait;

  /**
   * {@inheritdoc}
   */
  public function getInfo() {
    $class = get_class($this);
    return [
      '#title' => t('Coupon code'),
      '#description' => t('Enter your coupon code here.'),
      '#submit_title' => t('Apply coupon'),
      '#submit_message' => t('Coupon applied'),
      '#remove_title' => t('Remove coupon'),
      // The coupon ID.
      '#default_value' => NULL,
      '#order_id' => NULL,
      '#display_actions' => TRUE,
      '#single_coupon_mode' => TRUE,
      '#process' => [
        [$class, 'processForm'],
      ],
      '#element_validate' => [
        [$class, 'validateForm'],
      ],
      '#theme_wrappers' => ['container'],
    ];
  }

  /**
   * Builds the coupon redemption form.
   *
   * @param array $element
   *   The form element being processed.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   * @param array $complete_form
   *   The complete form structure.
   *
   * @throws \InvalidArgumentException
   *   Thrown when the #order_id property is empty or invalid.
   *
   * @return array
   *   The processed form element.
   */
  public static function processForm(array $element, FormStateInterface $form_state, array &$complete_form) {
    if (empty($element['#order_id'])) {
      throw new \InvalidArgumentException('The commerce_coupon_redemption_form element requires the #order_id property.');
    }
    $order_storage = \Drupal::entityTypeManager()->getStorage('commerce_order');
    $order = $order_storage->load($element['#order_id']);
    if (!$order instanceof OrderInterface) {
      throw new \InvalidArgumentException('The commerce_coupon_redemption #order_id must be a valid order ID.');
    }

    /** @var \Drupal\commerce_promotion\Entity\CouponInterface[] $coupons */
    $coupons = $order->get('coupons')->referencedEntities();
    $has_coupons = !empty($coupons);

    // Determine if 'add coupon' buttons should be displayed. It should be
    // on display_actions mode, if there is no coupons, or if at least one of
    // the coupons is comatible with others.
    $display_actions = !$has_coupons;

    if (!$element['#single_coupon_mode']) {
      /** @var \Drupal\commerce_promotion\Entity\CouponInterface $coupon */
      foreach ((array) $coupons as $coupon) {
        if ($coupon->getPromotion()
            ->getCompatibility() == PromotionInterface::COMPATIBLE_ANY
        ) {
          $display_actions = TRUE;
          continue;
        }
      }
    }
    $display_actions = $element['#display_actions'] && $display_actions;

    $id_prefix = implode('-', $element['#parents']);
    // @todo We cannot use unique IDs, or multiple elements on a page currently.
    // @see https://www.drupal.org/node/2675688
    // $wrapper_id = Html::getUniqueId($id_prefix . '-ajax-wrapper');
    $wrapper_id = $id_prefix . '-ajax-wrapper';

    $element = [
        '#tree' => TRUE,
        '#prefix' => '<div id="' . $wrapper_id . '">',
        '#suffix' => '</div>',
        // Pass the id along to other methods.
        '#wrapper_id' => $wrapper_id,
      ] + $element;
    $element['coupons'] = CouponRedemptionForm::buildAdjustmentsTable($element, $order, $display_actions);
    $element['coupons']['#access'] = $has_coupons && !$element['#single_coupon_mode'];
    $element['code'] = [
      '#type' => 'textfield',
      '#title' => $element['#title'],
      '#description' => $element['#description'],
      '#access' => $display_actions,
    ];
    $element['apply'] = [
      '#type' => 'submit',
      '#value' => $element['#submit_title'],
      '#name' => 'apply_coupon',
      '#limit_validation_errors' => [
        $element['#parents'],
      ],
      '#submit' => [
        [get_called_class(), 'applyCoupon'],
      ],
//      '#ajax' => [
//        'callback' => [get_called_class(), 'ajaxRefresh'],
//        'wrapper' => $element['#wrapper_id'],
//      ],
      '#access' => $display_actions,
    ];
    $element['remove'] = [
      '#type' => 'submit',
      '#value' => $element['#remove_title'],
      '#name' => 'remove_coupon',
//      '#ajax' => [
//        'callback' => [get_called_class(), 'ajaxRefresh'],
//          'wrapper' => $element['#wrapper_id'],
//        ],
      '#weight' => 50,
      '#limit_validation_errors' => [
        $element['#parents'],
      ],
      '#submit' => [
        [get_called_class(), 'removeCoupon'],
      ],
      '#access' => $has_coupons && $element['#single_coupon_mode'],
    ];

    return $element;
  }

  /**
   * Ajax callback.
   */
//  public static function ajaxRefresh(array $form, FormStateInterface $form_state) {
//    $parents = $form_state->getTriggeringElement()['#parents'];
//    array_pop($parents);
//    $coupon_element = NestedArray::getValue($form, $parents);
//    $summary_element = $form['sidebar']['order_summary'];
//
//    $response = new AjaxResponse();
//    // To refresh the coupon
//    $response->addCommand(new InsertCommand(NULL, $coupon_element));
//    // To refresh the order summary
//    $response->addCommand(new InsertCommand('[data-drupal-selector="edit-sidebar-order-summary"]', $summary_element));
//
//    return $response;
//  }

  /**
   * Apply coupon submit callback.
   */
  public static function applyCoupon(array $form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $parents = $triggering_element['#parents'];
    array_pop($parents);
    $element = NestedArray::getValue($form, $parents);

    $entity_type_manager = \Drupal::entityTypeManager();
    $order_storage = $entity_type_manager->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $order_storage->load($element['#order_id']);

    $coupon = $form_state->getValue($parents);
//    foreach($order->getItems() as $order_item) {
//      // get the product id from order_item
//      $purchased_entity = $order_item->getPurchasedEntityId();
//      $pro_var_id = ProductVariation::load($purchased_entity);
//      $product_id = $pro_var_id->getProductId();
//
//      //get the product id from respective promotion
//      $promotion_id = $coupon->getPromotionId();
//      $promotion = Promotion::load($promotion_id);
//      $offer = $promotion->offer->first()->getTargetInstance();
//      if ($offer instanceof ProductFixedOff || $offer instanceof ProductPercentageOff) {
//        $config = $offer->getConfiguration();
//        $offer_product_id = $config['product_id'];
//      }
//
//      if ($product_id == $offer_product_id) {
//       // $order_item->setAdjustments([]);
//        $cart_quantity = $order_item->getQuantity();
//        $coupon_quantity = $coupon->getPromocodeQuantity();
//        if ($cart_quantity > $coupon_quantity) {
//         // $order_item->setQuantity($coupon_quantity);
//          $or_total = $order->getTotalPrice()->getNumber();
//          $amount = $order_item->getUnitPrice()->multiply($coupon_quantity);
//          $dis_amt = $amount->getNumber();
//          $final_amt = $order_item->getUnitPrice()->getNumber();
//          $currency_code = $amount->getCurrencyCode();
//          //$promocode_adjustment = $order_item->getAdjustments();
//          //$price = (int) $final_amt;
//          $promocode_adjustment[] = new Adjustment([
//            'type' => 'promotion',
//            'label' => t('Discount'),
//            'amount' => new Price('-' . $final_amt, $currency_code),
//          ]);
//          $order_item->setAdjustments($promocode_adjustment);
//          $order_item->save();
//        }
//      }
//    }
    $order->get('coupons')->appendItem($coupon);
    $order->save();
    $form_state->setRebuild();
    drupal_set_message($element['#submit_message']);

  }

  /**
   * Remove coupon submit callback.
   */
  public static function removeCoupon(array $form, FormStateInterface $form_state) {
    $triggering_element = $form_state->getTriggeringElement();
    $parents = $triggering_element['#parents'];
    array_pop($parents);
    $element = NestedArray::getValue($form, $parents);

    $entity_type_manager = \Drupal::entityTypeManager();
    $order_storage = $entity_type_manager->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $order_storage->load($element['#order_id']);

    if (!empty($triggering_element['#coupon_code'])) {
      /** @var \Drupal\commerce_promotion\CouponStorageInterface $coupon_storage */
      $coupon_storage = $entity_type_manager->getStorage('commerce_promotion_coupon');
      $coupon_to_delete = $coupon_storage->loadByCode($triggering_element['#coupon_code']);

      // Find $coupon_to_delete id.
      $coupons = $order->get('coupons')->getValue();
      $coupons_ids = array_map(function ($coupon) {
        return $coupon['target_id'];
      }, $coupons);
      $coupon_id = array_search($coupon_to_delete->id(), $coupons_ids);
      $order->get('coupons')->removeItem($coupon_id);
    } else {
      $order->set('coupons', []);
    }
    $order->save();
    //$form_state->setRebuild();
  }

  /**
   * Validates the coupon redemption element.
   *
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  public static function validateForm(array &$element, FormStateInterface $form_state) {

    $triggering_element = $form_state->getTriggeringElement();
    if (isset($triggering_element['#coupon_code'])) {
      return;
    }

    $coupon_parents = array_merge($element['#parents'], ['code']);
    $coupon_code = $form_state->getValue($coupon_parents);
    if (empty($coupon_code)) {
      return;
    }
    $entity_type_manager = \Drupal::entityTypeManager();
    $code_path = implode('][', $coupon_parents);

    $order_storage = $entity_type_manager->getStorage('commerce_order');
    /** @var \Drupal\commerce_order\Entity\OrderInterface $order */
    $order = $order_storage->load($element['#order_id']);
    /** @var \Drupal\commerce_promotion\CouponStorageInterface $coupon_storage */
    $coupon_storage = $entity_type_manager->getStorage('commerce_promotion_coupon');
    $coupon = $coupon_storage->loadByCode($coupon_code);
    if (empty($coupon)) {
      $form_state->setErrorByName($code_path, t('Coupon is invalid'));
      return;
    }
    foreach ($order->get('coupons') as $item) {
      if ($item->target_id == $coupon->id()) {
        $form_state->setErrorByName($code_path, t('Coupon has already been redeemed'));
        return;
      }
    }

    if (!$coupon->available($order)) {
      $form_state->setErrorByName($code_path, t('Coupon is invalid'));
      return;
    }
    if (!$coupon->getPromotion()->applies($order)) {
      $form_state->setErrorByName($code_path, t('Coupon is invalid'));
      return;
    }

    $form_state->setValueForElement($element, $coupon);
  }
  
  /**
   * Adjustments table builder.
   *
   * @param bool $display_actions
   *   TRUE if actions displayed.
   *
   * @return array Render array.
   *   Render array.
   */
  public static function buildAdjustmentsTable(array $element, OrderInterface $order, $display_actions = TRUE) {
    $table = [
      '#type' => 'table',
      '#header' => [t('Label'), t('Amount')],
      '#empty' => t('There are no special offers applied.'),
    ];
    if ($display_actions) {
      $table['#header'][] = t('Remove');
    }

    $adjustments = $order->getAdjustments();
    foreach ($order->getItems() as $orderItem) {
      if ($item_adjustments = $orderItem->getAdjustments()) {
        $adjustments = array_merge($adjustments, $item_adjustments);
      }
    }
    $promotion_ids = array_map(function (Adjustment $adjustment) {
      return $adjustment->getSourceId();
    }, $adjustments);

    /** @var \Drupal\commerce_promotion\Entity\CouponInterface[] $coupons */
    $coupons = $order->get('coupons')->referencedEntities();
    if (empty($coupons) || empty($adjustments)) {
      return $table;
    }

    // Use special format for promotion with coupon.
    $entity_type_manager = \Drupal::entityTypeManager();
    /** @var \Drupal\commerce_promotion\CouponStorageInterface $coupon_storage */
    $coupon_storage = $entity_type_manager->getStorage('commerce_promotion_coupon');

    /** @var \Drupal\commerce_promotion\Entity\CouponInterface $coupon */
    foreach ($coupons as $index => $coupon) {
      $adjustment_index = array_search($coupon->getPromotion()->id(), $promotion_ids);
      $adjustment = $adjustments[$adjustment_index];

      $label = t(':title (code: :code)', [
        ':title' => $coupon->getPromotion()->getName(),
        ':code' => $coupon->get('code')->value
      ]);
      $table[$index]['label'] = [
        '#type' => 'inline_template',
        '#template' => '{{ label }}',
        '#context' => [
          'label' => $label,
        ],
      ];
      $table[$index]['amount'] = [
        '#type' => 'inline_template',
        '#template' => '{{ price|commerce_price_format }}',
        '#context' => [
          'price' => $adjustment->getAmount(),
        ],
      ];

      if ($display_actions) {
        $table[$index]['remove'] = [
          '#type' => 'submit',
          '#value' => $element['#remove_title'],
          '#name' => 'remove_coupon_' . $index,
          '#ajax' => [
            'callback' => [get_called_class(), 'ajaxRefresh'],
            'wrapper' => $element['#wrapper_id'],
          ],
          '#weight' => 50,
          '#limit_validation_errors' => [
            $element['#parents'],
          ],
          '#parents' => ['coupon_redemption', 'coupons', 'remove_coupon_' . $index],

          '#coupon_code' => $coupon->get('code')->value,
          '#submit' => [
            [get_called_class(), 'removeCoupon'],
          ],
        ];
      }
    }
    return $table;
  }

}
