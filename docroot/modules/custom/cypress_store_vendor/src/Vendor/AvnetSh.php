<?php

namespace Drupal\cypress_store_vendor\Vendor;

/**
 * Class AvnetSh.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 *   AvnetSh Vendor.
 */
class AvnetSh extends Avnet {

  /**
   * Avnet SH constructor.
   */
  public function __construct() {
    parent::__construct();
    $this->region = 'SH';
  }

}
