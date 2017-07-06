<?php

namespace Drupal\cypress_custom_address;

use Drupal\commerce_shipping\PackerManager;

/**
 * Class ShippingProfile.
 *
 * @package Drupal\cypress_custom_address
 */
class ShippingProfile {

  /**
   * Constructs new ShippingProfile object.
   */
  public function __construct(PackerManager $package_manager) {
    $this->packageManager = $package_manager;
  }

  /**
   * Method to get inventory details from Avnet.
   *
   * @param string $order
   *   To get shipements.
   * @param string $shipping_profile
   *   To get shippment profile details.
   *
   * @return string
   *   saving shippments
   */
  public function setShippingProfile($order, $shipping_profile) {
    $shipments = $order->get('shipments')->referencedEntities();
    if (!empty($shipments)) {
      foreach ($shipments as $shipment) {
        $shipment->setShippingProfile($shipping_profile);
        $shipment->save();
      }
    }
    else {
      $shipments = $this->packageManager
        ->packToShipments($order, $shipping_profile, []);
      if (empty($shipments[0][0])) {
        return 'Error occurred during order routing. Please contact administrator.';
      }
      $shipments[0][0]->save();
      $order->shipments = $shipments[0][0];
      $order->save();
    }
  }

}
