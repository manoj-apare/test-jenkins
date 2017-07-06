<?php

namespace Drupal\commerce_promotion\Entity;

use Drupal\commerce_order\Entity\OrderInterface;
use Drupal\Core\Entity\ContentEntityInterface;

/**
 * Provides an interface for defining coupon entities.
 */
interface CouponInterface extends ContentEntityInterface {

  /**
   * Gets the parent promotion.
   *
   * @return \Drupal\commerce_promotion\Entity\PromotionInterface|null
   *   The promotion entity, or null.
   */
  public function getPromotion();

  /**
   * Gets the parent promotion ID.
   *
   * @return int|null
   *   The promotion ID, or null.
   */
  public function getPromotionId();

  /**
   * Gets the coupon code.
   *
   * @return string
   *   Code for the coupon.
   */
  public function getCode();

  /**
   * Sets the coupon code.
   *
   * @param string $code
   *   The coupon code.
   *
   * @return $this
   */
  public function setCode($code);

  /**
   * Gets the coupon usage limit.
   *
   * Represents the maximum number of times the coupon can be used.
   * 0 for unlimited.
   *
   * @return int
   *   The promotion usage limit.
   */
  public function getUsageLimit();

  /**
   * Sets the coupon usage limit.
   *
   * @param int $usage_limit
   *   The coupon usage limit.
   *
   * @return $this
   */
  public function setUsageLimit($usage_limit);

  /**
   * Gets the coupon quantity limit.
   *
   * Represents the maximum quantity the coupon can be have.
   *
   * @return int
   *   The promotion usage limit.
   */
  public function getPromocodeQuantity();

  /**
   * Sets the coupon quantity limit.
   *
   * @param int $promocode_quantity
   *   The coupon quantity limit.
   *
   * @return $this
   */
  public function setPromocodeQuantity($promocode_quantity);

  /**
   * Gets whether the coupon is enabled.
   *
   * @return bool
   *   TRUE if the coupon is enabled, FALSE otherwise.
   */
  public function isEnabled();

  /**
   * Sets whether the coupon is enabled.
   *
   * @param bool $enabled
   *   Whether the coupon is enabled.
   *
   * @return $this
   */
  public function setEnabled($enabled);

  /**
   * Checks whether the coupon is available for the given order.
   *
   * Ensures that the parent promotion is available, the coupon
   * is enabled, and the usage limits are respected.
   *
   * @param \Drupal\commerce_order\Entity\OrderInterface $order
   *   The order.
   *
   * @return bool
   *   TRUE if coupon is available, FALSE otherwise.
   */
  public function available(OrderInterface $order);

}
