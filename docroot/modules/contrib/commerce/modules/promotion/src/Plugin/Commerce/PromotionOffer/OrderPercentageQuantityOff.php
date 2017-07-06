<?php

namespace Drupal\commerce_promotion\Plugin\Commerce\PromotionOffer;

/**
 * Provides an 'Order: Percentage Quantity off' condition.
 *
 * @CommercePromotionOffer(
 *   id = "commerce_promotion_quantity_order_percentage_off",
 *   label = @Translation("Percentage Quantity off on the order total"),
 *   target_entity_type = "commerce_order",
 * )
 */
class OrderPercentageQuantityOff extends PercentageQuantityOffBase {

  /**
   * {@inheritdoc}
   */
  public function execute() {
    $order = $this->getOrder();
    if ($order->getTotalPrice()) {
      $adjustment_amount = $order->getTotalPrice()->multiply($this->getAmount())->multiply($this->getCouponQuantity());
      $adjustment_amount = $this->rounder->round($adjustment_amount);
      $this->applyAdjustment($order, $adjustment_amount);
    }
  }

}
