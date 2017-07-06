<?php

namespace Drupal\cypress_store_vendor\Vendor;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_product\Entity\Product;
use Drupal\cypress_store_vendor\CypressStoreVendor;
use Symfony\Component\Yaml\Yaml;

/**
 * Class VendorBase.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 */
class VendorBase {

  /**
   * Vendor class for Avnet SH region.
   */
  const AVNETSH = '\Drupal\cypress_store_vendor\Vendor\AvnetSh';

  /**
   * Vendor class for Avnet SH region.
   */
  const AVNETHK = '\Drupal\cypress_store_vendor\Vendor\AvnetHk';

  /**
   * Vendor class for Digikey.
   */
  const DIGIKEY = '\Drupal\cypress_store_vendor\Vendor\DigiKey';

  /**
   * Vendor class for CML/OM.
   */
  const CML = '\Drupal\cypress_store_vendor\Vendor\Cml';

  /**
   * Vendor class for CML/OM.
   */
  const HH = '\Drupal\cypress_store_vendor\Vendor\HarteHanks';

  /**
   * Vendor class for Fedex.
   */

  const FEDEX = '\Drupal\cypress_store_vendor\Vendor\FedEx';

  /**
   * Vendor shipment method mapping.
   */
  const VENDORSHIPMENTMAP = [
    'avnet' => [
      'USA FedEx 2Day For Sample CAT_A' => 'FEDEX Express Economy 2nd Day Air',
      'USA FedEx 2Day For Sample CAT_B' => 'FEDEX Express Economy 2nd Day Air',
      'USA FedEx Standard Overnight For Sample CAT_A' => 'FEDEX Overnight PM Delivery',
      'USA FedEx Standard Overnight For Sample CAT_B' => 'FEDEX Overnight PM Delivery',
      'FedEx International Economy For Sample CAT_A' => 'FEDEX International Economy',
      'FedEx International Economy For Sample CAT_B' => 'FEDEX International Economy',
      'FedEx International Priority For Sample CAT_A' => 'FEDEX International Priority',
      'FedEx International Priority For Sample CAT_B' => 'FEDEX International Priority',
    ],
    'cml' => [
      'USA FedEx 2Day For Sample CAT_A' => '000002_FEDEX 2DAY_A_2DA',
      'USA FedEx 2Day For Sample CAT_B' => '000002_FEDEX 2DAY_A_2DA',
      'USA FedEx Standard Overnight For Sample CAT_A' => 'FEDEX STD OVERNIGHT',
      'USA FedEx Standard Overnight For Sample CAT_B' => 'FEDEX STD OVERNIGHT',
      'FedEx International Economy For Sample CAT_A' => '000001_FEDEX INT_A_ECONOMY',
      'FedEx International Economy For Sample CAT_B' => '000001_FEDEX INT_A_ECONOMY',
      'FedEx International Priority For Sample CAT_A' => '000001_FEDEX IP_A_PRIORITY',
      'FedEx International Priority For Sample CAT_B' => '000001_FEDEX IP_A_PRIORITY',
    ],
    'digikey' => [
      'USA FedEx 2Day For Sample CAT_A' => 'FEDEX Express Economy 2nd Day Air',
      'USA FedEx 2Day For Sample CAT_B' => 'FEDEX Express Economy 2nd Day Air',
      'USA FedEx Standard Overnight For Sample CAT_A' => 'FEDEX Overnight PM Delivery',
      'USA FedEx Standard Overnight For Sample CAT_B' => 'FEDEX Overnight PM Delivery',
      'FedEx International Economy For Sample CAT_A' => 'FEDEX International Economy',
      'FedEx International Economy For Sample CAT_B' => 'FEDEX International Economy',
      'FedEx International Priority For Sample CAT_A' => 'FEDEX International Priority',
      'FedEx International Priority For Sample CAT_B' => 'FEDEX International Priority',
    ],
    'hartehanks' => [
      'USA - FedEx Express Saver For Kit Domestic' => 'FedEx - Express Saver',
      'USA - FedEx Overnight For Kits Domestic' => 'FedEx - Overnight',
      'FedEx International Economy For Kits' => 'FedEx International Economy',
      'FedEx International Priority For Kits' => 'FedEx International Priority',
    ],
  ];

  /**
   * Configuration for vendor.
   *
   * @var \Drupal\cypress_store_vendor\Vendor
   */
  protected $config;

  /**
   * Vendor name.
   *
   * @var \Drupal\cypress_store_vendor\Vendor
   */
  protected $vendor;

  /**
   * VendorBase constructor.
   */
  public function __construct() {
    $vendor = strtolower(substr(strrchr(get_class($this), '\\'), 1));
    $vendor = preg_replace('/^(avnet)(hk|sh)$/', '${1}', $vendor);
    $this->vendor = $vendor;
    $config = \Drupal::config('cypress_store_vendor.vendor_entity.' . $vendor)
      ->get('description');
    $config = Yaml::parse($config);
    $environment = isset($_ENV['AH_SITE_ENVIRONMENT']) ? $_ENV['AH_SITE_ENVIRONMENT'] : 'dev2';
    $this->config = $config[$environment];
  }

  /**
   * Get Shipping Address.
   *
   * @param mixed $order
   *   Order id or object.
   * @param bool $oracle_fields_required
   *   Whether need to include oracle field data.
   *
   * @return array
   *   Shipping profile address.
   */
  public function getShippingAddress($order, $oracle_fields_required = FALSE) {
    if (is_numeric($order)) {
      $order = Order::load($order);
    }
    $shipments = $order->get('shipments')->referencedEntities();
    // @todo condition check to see if order has shipment and if shipment is not there do not perform and thing
    /*
     * Logic to eradicate all old orders
     * Starts here.
     */
    if (!empty($shipments)) {
      /* Ends here */
      $first_shipment = reset($shipments);
      /*
       * Logic to eradicate all old orders
       * Starts here.
       */
      if (!empty($first_shipment->getShippingProfile())) {
        /* Ends here */
        $shipping_address = $first_shipment->getShippingProfile()
          ->get('field_contact_address')
          ->getValue();
      }
      if ($oracle_fields_required) {
        $oracle_fields = ['oracle_customer_site_id', 'om_customer_site_use_id'];
        foreach ($oracle_fields as $field) {
          $field_value = $first_shipment->getShippingProfile()
            ->get("field_$field")
            ->getValue()[0]['value'];
          if ($field_value == NULL) {
            $field_value = 0;
          }
          $shipping_address[0][$field] = $field_value;
        }
      }
      return $shipping_address[0];
    }
  }

  /**
   * Get Billing Address.
   *
   * @param mixed $order
   *   Order id or object.
   *
   * @return array
   *   Billing profile address.
   */
  public function getBillingAddress($order) {
    if (is_numeric($order)) {
      $order = Order::load($order);
    }
    $billing_address = $order
      ->getBillingProfile()
      ->get('field_contact_address')
      ->getValue();
    return $billing_address[0];
  }

  /**
   * Method to get product marketing part number id.
   *
   * @param \Drupal\commerce_order\Entity\OrderItem $order_item
   *   Order Item.
   *
   * @return string
   *   Marketing part number id.
   */
  public function getProductMpnId(OrderItem $order_item) {

    $product_variation = $order_item->getPurchasedEntity();
    if (!empty($product_variation)) {
      $product_id = $product_variation->get('product_id')
        ->getValue()[0]['target_id'];
      $product = Product::load($product_id);
      $mpn_id = '';
      $product_type = $product_variation->get('type')
        ->getValue()[0]['target_id'];
      if ($product_type == 'part_store') {
        $mpn_id = $product_variation->getTitle();
      }
      elseif ($product_type == 'default') {
        $mpn_id = $product->get('field_document_source')
          ->getValue()[0]['value'];
      }
    }
    return $mpn_id;
  }

  /**
   * Email Admin If there is error.
   *
   * While Placing order, check product Availability and check shipping in
   * vendor side.
   *
   * @param string $subject
   *   Subject of error mail.
   * @param string $body
   *   Content of error mail.
   */
  public function emailVendorExceptionMessage($subject, $body) {

    $message = array('subject' => $subject, 'body' => $body);
    $dispatcher = \Drupal::service('event_dispatcher');
    // Creating our CypressStoreVendor event class object.
    $event = new CypressStoreVendor($message);
    // Dispatching the event through the ‘dispatch’  method,
    // Passing event name and event object ‘$event’ as parameters.
    $dispatcher->dispatch(CypressStoreVendor::ERROR, $event);
  }

  /**
   * Method to get Shipment method identifier.
   *
   * @param object $shipment
   *   Shipment object.
   *
   * @return mixed
   *   Shipping method machine name.
   */
  public function getShipmentMethodName($shipment) {
    $shipment_method = $shipment->getShippingMethod();
    $shipment_method_name = $shipment_method->name->first()->value;
    $shipment_method_names = self::VENDORSHIPMENTMAP[$this->vendor];
    return $shipment_method_names[$shipment_method_name];
  }

}
