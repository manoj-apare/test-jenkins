<?php

namespace Drupal\cypress_store_vendor\Vendor;

/**
 * Class AvnetHk.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 *   AvnetHK Vendor.
 */
class AvnetHk extends Avnet {

  /**
   * Avnet HK constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->region = 'HK';
  }

}
