<?php

namespace Drupal\cypress_store_vendor;

/**
 * Class InventoryService.
 *
 * @package Drupal\cypress_store_vendor
 */
class VendorService {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * Method to get inventory count for a product/part.
   *
   * @param string $vendor
   *   Vendor name.
   * @param string $mpn
   *   Product/Part number.
   *
   * @return mixed
   *   Available quantity.
   */
  public function getInventory($vendor, $mpn) {
    $vendor_class_name = constant('Drupal\cypress_store_vendor\Vendor\VendorBase::' . $vendor);
    $vendor_handler = new $vendor_class_name();
    return $vendor_handler->getInventory($mpn);
  }

  /**
   * Method to set order to vendor for fulfillment.
   *
   * @param string $vendor
   *   Vendor name.
   * @param mixed $order
   *   Commerce order.
   * @param mixed $shipment
   *   Shipment details.
   */
  public function submitOrder($vendor, $order, $shipment) {
    $vendor_class_name = constant('Drupal\cypress_store_vendor\Vendor\VendorBase::' . $vendor);
    $vendor_handler = new $vendor_class_name();
    $is_order_shipment_placed = $vendor_handler->submitOrder($order, $shipment);
    if ($is_order_shipment_placed) {
      $shipment->set('state', 'In progress')
        ->save();
    }
  }

  /**
   * Method to get shipping details.
   *
   * @param string $vendor
   *   Vendor name.
   * @param array $params
   *   Additional data.
   *
   * @return mixed
   *   Shipment details.
   */
  public function getShipment($vendor, $params = []) {
    $vendor_class_name = constant('Drupal\cypress_store_vendor\Vendor\VendorBase::' . $vendor);
    $vendor_handler = new $vendor_class_name();
    $shipement_response = $vendor_handler->getShipment($params);
    if (!empty($shipement_response)) {
      return $shipement_response;
    }
  }

  /**
   * Method to tack shipment whose tracking number are available.
   *
   * @param string $vendor
   *   Vendor name.
   * @param string $tracking_code
   *   Tracking code.
   *
   * @return mixed
   *   Tracking details.
   */
  public function trackShipment($vendor, $tracking_code) {
    $vendor = ($vendor == 'FEDX' ? 'FEDEX' : $vendor);
    $vendor_class_name = constant('Drupal\cypress_store_vendor\Vendor\VendorBase::' . $vendor);
    $vendor_handler = new $vendor_class_name();
    $tracking_response = $vendor_handler->trackService($tracking_code);
    if (!empty($tracking_response)) {
      return $tracking_response;
    }

  }

  /**
   * Check For Asian Countries.
   *
   * @param string $country
   *   Country name.
   * @param string $search_by_country_name
   *   Boolean, whether to search by name.
   *
   * @return bool
   *   Is Asian country or not.
   */
  public function isAsianCountry($country, $search_by_country_name = FALSE) {
    $list_of_asian_country = [
      'AE' => 'United Arab Emirates',
      'AF' => 'Afghanistan',
      'AM' => 'Armenia',
      'AZ' => 'Azerbaijan',
      'BD' => 'Bangladesh',
      'BH' => 'Bahrain',
      'BN' => 'Brunei',
      'BT' => 'Bhutan',
      'CC' => 'Cocos [Keeling] Islands',
      'CN' => 'China',
      'CX' => 'Christmas Island',
      'CY' => 'Cyprus',
      'GU' => 'Guam',
      'HK' => 'Hong Kong SAR China',
      'ID' => 'Indonesia',
      'IL' => 'Israel',
      'IN' => 'India',
      'IO' => 'British Indian Ocean Territory',
      'IQ' => 'Iraq',
      'IR' => 'Iran',
      'JO' => 'Jordan',
      'JP' => 'Japan',
      'KG' => 'Kyrgyzstan',
      'KH' => 'Cambodia',
      'KP' => 'North Korea',
      'KR' => 'South Korea',
      'KW' => 'Kuwait',
      'KZ' => 'Kazakhstan',
      'LA' => 'Laos',
      'LB' => 'Lebanon',
      'LK' => 'Sri Lanka',
      'MM' => 'Myanmar [Burma]',
      'MN' => 'Mongolia',
      'MO' => 'Macau SAR China',
      'MV' => 'Maldives',
      'MY' => 'Malaysia',
      'NP' => 'Nepal',
      'OM' => 'Oman',
      'PH' => 'Philippines',
      'PK' => 'Pakistan',
      'PS' => 'Palestinian Territories',
      'QA' => 'Qatar',
      'RU' => 'Russia',
      'SA' => 'Saudi Arabia',
      'SG' => 'Singapore',
      'SY' => 'Syria',
      'TH' => 'Thailand',
      'TJ' => 'Tajikistan',
      'TM' => 'Turkmenistan',
      'TR' => 'Turkey',
      'TW' => 'Taiwan',
      'UZ' => 'Uzbekistan',
      'VN' => 'Vietnam',
      'YE' => 'Yemen',
    ];

    if ($search_by_country_name == FALSE) {
      return array_key_exists($country, $list_of_asian_country);
    }
    elseif ($search_by_country_name == TRUE) {
      return in_array($country, $list_of_asian_country);
    }

  }

}
