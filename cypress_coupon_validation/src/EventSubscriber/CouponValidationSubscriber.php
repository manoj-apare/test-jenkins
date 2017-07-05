<?php

/**
 * @file
 * The PHP page that serves all page requests on a Drupal installation.
 *
 * All Drupal code is released under the GNU General Public License.
 * See COPYRIGHT.txt and LICENSE.txt files in the "core" directory.
 */

namespace Drupal\cypress_coupon_validation\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Drupal\commerce_promotion\Entity\Promotion;
use Drupal\commerce_promotion\Entity\Coupon;
use Drupal\commerce_product\Entity\ProductVariation;

/**
 * Class CouponValidationSubscriber.
 *
 * @package Drupal\cypress_coupon_validation
 */
class CouponValidationSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['commerce_order.place.post_transition'] = ['couponOrderValidation'];

    return $events;
  }

  /**
   * Event called when the commerce_order.place.post_transition is dispatched.
   *
   * @param string|\Symfony\Component\EventDispatcher\Event $event
   *   The event to GetResponseEvent.
   *
   * @return string
   *   The coupon data
   */
  public function couponOrderValidation(Event $event) {
    $order_create = $event->getEntity();
    $order_id = $order_create->get('order_id')->getValue()[0]['value'];
    $user_id = $order_create->get('uid')->getValue()[0]['target_id'];
    $order_items = $order_create->getItems();
    foreach ($order_items as $order_item) {
      $product_var_id = $order_item->getPurchasedEntityId();
      $product_variation = ProductVariation::load($product_var_id);
      $pro_title = $product_variation->getTitle();
      $promotion_id = get_promotion_id($pro_title);
      $promotion = Promotion::load($promotion_id);
      if ($promotion) {
        $coupons = $promotion->getCouponIds();
        foreach ($coupons as $coupon) {
          $coupon_id = $coupon;
          $coupon_obj = Coupon::load($coupon_id);
          $promocode = $coupon_obj->getCode();
        }
      }
    }
    // if($order_create->get('coupons')) {
    // $coupon_id = $order_create->get('coupons')->getValue()[0]['target_id'];
    // $coupon = Coupon::load($promotion_id);
    // if (!empty($coupon)) {
    // $coupon_code = $coupon->getCode();
    // }
    // }
    if (!empty($coupon_obj)) {
      $usage_limit = $coupon_obj->getUsageLimit();
      for ($count = 0; $count < $usage_limit; $count++) {
        // Insert into custom table after order complete.
        $query = \Drupal::database()->insert('cypress_store_coupons')
          ->fields([
            'order_id' => $order_id,
            'user_id' => $user_id,
            'promotion_id' => $promotion_id,
            'coupon_code' => $promocode,
          ])->execute();

        return $query;
      }
    }
  }

}
