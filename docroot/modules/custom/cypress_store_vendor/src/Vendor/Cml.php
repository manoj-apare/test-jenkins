<?php

namespace Drupal\cypress_store_vendor\Vendor;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\profile\Entity\Profile;

/**
 * Class Cml.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 */
class Cml extends VendorBase {

  /**
   * Avnet constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Method to get inventory details from CML/OM.
   *
   * @param string $mpn
   *   Marketing Part number.
   *
   * @return int|string
   *   Part quantity in CML/OM.
   */
  public function getInventory($mpn) {
    $inventory = 0;
    $product_id = \Drupal::database()->select('commerce_product_field_data', 'cpfd')
      ->fields('cpfd', ['product_id'])
      ->condition('cpfd.title', $mpn)
      ->execute()->fetchCol(0);
    foreach ($product_id as $prod_id) {
      $product = Product::load($prod_id);
      $product_inventory = $product->get('field_inventory')->first();
      if (!empty($product_inventory)) {
        $inventory = $product_inventory->getValue()['value'];
      }
    }

    return $inventory;
  }

  /**
   * Method to get shipment details from CML/OM.
   *
   * @param array $params
   *   Parameters.
   *
   * @return mixed
   *   Shipment tracking details.
   */
  public function getShipment($params = []) {
    $shipment = Shipment::load($params->shipment_id);
    $vendor_tracking_data = [];
    if (!empty($shipment) && isset($shipment['tracking_code'])) {
      $vendor_tracking_data['trackingId'] = $shipment->tracking_code;
      $vendor_tracking_data['carrier'] = '';
      $vendor_tracking_data['shippedDate'] = $shipment->field_ship_date;
      $vendor_tracking_data['secondaryTrackingNumber'] = $shipment->field_secondary_tracking_number;
    }

    return $vendor_tracking_data;
  }

  /**
   * Method to submit order to CML/OM.
   *
   * @param mixed $order
   *   Commerce order.
   * @param mixed $shipment
   *   Shipment details.
   *
   * @return mixed
   *   SO Number, if placed. Else 0.
   */
  public function submitOrder($order, $shipment) {
    $ship_via = $this->getShipmentMethodName($shipment);
    $shipping_address = $this->getShippingAddress($order, TRUE);
    $first_name = trim($shipping_address['given_name']);
    $last_name = trim($shipping_address['family_name']);
    $country_code = $shipping_address['country_code'];
    $oracle_account_site_id = $shipping_address['oracle_customer_site_id'];
    $om_customer_site_use_id = $shipping_address['om_customer_site_use_id'];

    if ($country_code == 'US') {
      $operating_unit = 125;
      $responsibility_key = 'CSC_OM_SAMPLE_CLERK';
    }
    else {
      $operating_unit = 429;
      $responsibility_key = 'CSTI_OM_SAMPLE_CLERK';
    }

    // Customer account creation in OM if not there.
    if (empty($oracle_account_site_id)) {
      list($oracle_account_site_id, $om_customer_site_use_id) = $this
        ->createCustomAddress($order, $responsibility_key, $operating_unit);
      if (empty($om_customer_site_use_id)) {
        return 0;
      }
      $shipping_profile = $shipment->getShippingProfile();
      $shipping_profile->field_oracle_customer_site_id = $oracle_account_site_id;
      $shipping_profile->field_om_customer_site_use_id = $om_customer_site_use_id;
      $shipping_profile->save();
    }

    // Process order shipment item.
    $shipment_items = $shipment->getItems();
    $order_line_items = [];
    $line_item_count = 1;
    $date = gmdate('d-m-Y H:i:s', $order->getCreatedTime());
    foreach ($shipment_items as $shipment_item) {
      $mpn = $shipment_item->getTitle();
      $quantity = $shipment_item->getQuantity();
      $production_status_results = \Drupal::database()->query('SELECT fsr.field_status_raw_value
        FROM `commerce_product__field_status_raw` fsr
        join commerce_product_field_data fd
        on fsr.entity_id = fd.product_id
        where fd.title = :mpn', [':mpn' => $mpn]);
      $production_status_results->allowRowCount = TRUE;
      if ($production_status_results->rowCount() == 0) {
        $production_status_results = [
          0 => (object) [
            'field_status_raw_value' => '',
          ]
        ];
      }
      foreach ($production_status_results as $production_status_result) {
        $status = $production_status_result->field_status_raw_value;
        if ($responsibility_key == 'CSC_OM_SAMPLE_CLERK') {
          if ($status == 'production' || $status == 'Production') {
            $order_type_id = 1050;
          }
          else {
            $order_type_id = 1060;
          }
        }
        elseif ($responsibility_key == 'CSTI_OM_SAMPLE_CLERK') {
          if ($status == 'production' || $status == 'Production') {
            $order_type_id = 1361;
          }
          else {
            $order_type_id = 1359;
          }
        }
        $order_line_items[] = [
          // 'ORD_CRT_SEQ' => '',
          'ORG_ID' => (string) $operating_unit,
          'LINE_NUMBER' => (string) $line_item_count,
          'ORDERED_ITEM' => $mpn,
          // 'REQUEST_DATE' => $date,
          'ORDERED_QUANTITY' => (string) $quantity,
          'CANCELLED_QUANTITY' => '0',
          'SHIP_TO_ORG_ID' => (string) $om_customer_site_use_id,
          'SOLD_FROM_ORG_ID' => (string) $operating_unit,
          'SOLD_TO_ORG_ID' => '20890',
          // 'CUST_PO_NUMBER' => '',
          // 'SHIPMENT_NUMBER' => (string) $shipment->id(),
          'SHIPPING_METHOD_CODE' => $ship_via,
          // 'FREIGHT_CARRIER_CODE' => '',
          // 'FREIGHT_TERMS_CODE' => '',
          'FOB_POINT_CODE' => 'DDP DOCK',
          'ORIG_SYS_DOCUMENT_REF' => (string) $shipment->id(),
          'ORIG_SYS_LINE_REF' => (string) $shipment_item->getOrderItemId(),
          'UNIT_SELLING_PRICE' => (string) ($shipment_item->getDeclaredValue() / $shipment_item->getQuantity()),
          // 'UNIT_LIST_PRICE' => '',
          // 'ATTRIBUTE1' => '',
          // 'ATTRIBUTE2' => '',
          // 'ATTRIBUTE3' => '',
          // 'ATTRIBUTE4' => '',
          // 'ATTRIBUTE5' => '',
          // 'ATTRIBUTE6' => '',
          // 'ATTRIBUTE7' => '',
          // 'ATTRIBUTE8' => '',
          // 'ATTRIBUTE9' => '',
          // 'ATTRIBUTE10' => '',
          'ATTRIBUTE11' => (string) $shipment->id(),
          // 'ATTRIBUTE12' => '',
          // 'ATTRIBUTE13' => '',
          // 'ATTRIBUTE14' => '',
          // 'ATTRIBUTE15' => '',
          'CREATION_DATE' => $date,
          'CREATED_BY' => '1234',
          'LAST_UPDATE_DATE' => $date,
          'LAST_UPDATED_BY' => '1234',
          // 'LAST_UPDATE_LOGIN' => '',
          'CANCELLED_FLAG' => 'N',
          // 'CUSTOMER_LINE_NUMBER' => '',
          'SHIPPING_INSTRUCTIONS' => 'Attn: ' . $first_name . ' ' . $last_name,
          // 'PACKING_INSTRUCTIONS' => '',
          // 'END_CUSTOMER_ID' => '',
          'USER_NAME' => 'WEBOM_TECH_USER',
          'RESP_KEY' => $responsibility_key,
          // 'PROCESSED_FLAG' => '',
        ];
      }
      $line_item_count++;
    }
    $body = [
      'ORG_ID' => (string) $operating_unit,
      'ORDER_TYPE_ID' => (string) $order_type_id,
      'ORIG_SYS_DOCUMENT_REF' => (string) $shipment->id(),
      'TRANSACTIONAL_CURR_CODE' => 'USD',
      // 'CUST_PO_NUMBER' => '',
      'SHIPPING_METHOD_CODE' => $ship_via,
      // 'FREIGHT_CARRIER_CODE' => '',
      'FOB_POINT_CODE' => 'DDP DOCK',
      // 'FREIGHT_TERMS_CODE' => '',
      'SOLD_FROM_ORG_ID' => (string) $operating_unit,
      'SOLD_TO_ORG_ID' => '20890',
      'SHIP_TO_ORG_ID' => (string) $om_customer_site_use_id,
      // 'CREATION_DATE' => $date,
      // 'CREATED_BY' => '1234',
      // 'LAST_UPDATED_BY' => '1234',
      // 'LAST_UPDATE_DATE' => $date,
      // 'LAST_UPDATE_LOGIN' => '',
      // 'ATTRIBUTE1' => '',
      // 'ATTRIBUTE2' => '',
      // 'ATTRIBUTE3' => '',
      // 'ATTRIBUTE4' => '',
      // 'ATTRIBUTE5' => '',
      // 'ATTRIBUTE6' => '',
      // 'ATTRIBUTE7' => '',
      // 'ATTRIBUTE8' => '',
      // 'ATTRIBUTE9' => '',
      // 'ATTRIBUTE10' => '',
      'ATTRIBUTE11' => (string) $shipment->id(),
      // 'ATTRIBUTE12' => '',
      // 'ATTRIBUTE13' => '',
      // 'ATTRIBUTE14' => '',
      // 'ATTRIBUTE15' => '',
      'CANCELLED_FLAG' => 'N',
      'SHIPPING_INSTRUCTIONS' => 'Attn: ' . $first_name . ' ' . $last_name,
      // 'PACKING_INSTRUCTIONS' => '',
      // 'ATTRIBUTE16' => '',
      // 'ATTRIBUTE17' => '',
      // 'ATTRIBUTE18' => '',
      // 'ATTRIBUTE19' => '',
      // 'ATTRIBUTE20' => '',
      'USER_NAME' => 'WEBOM_TECH_USER',
      'RESP_KEY' => $responsibility_key,
      // 'PROCESSED_FLAG' => '',
      'LINE_REC' => $order_line_items,
    ];
    $client = \Drupal::httpClient();
    $body = json_encode($body);
    echo '<pre>'; print_r('Input: '); print_r($body);
    try {
      $request = $client->post(
        $this->config['order_creation']['end_point'],
        [
          'body' => $body,
          'headers' => [
            'auth' => [
              $this->config['order_creation']['username'],
              $this->config['order_creation']['password'],
            ],
            'Content-Type' => 'application/json',
          ],
        ]
      );

      $response = $request->getBody();
      $response_data = json_decode($response);
      echo '<pre>'; print_r('Response: '); print_r($response_data); exit;
      $so_no = $response_data->so_no;
      $shipment->field_so_no = $so_no;
      $shipment->save();
    }
    catch (\Exception $e) {
      echo '<pre>'; print_r('Error: ' . $e->getMessage()); exit;
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : CML' . '<br/>' . 'Request Body :' . htmlentities($body);
      $this->emailVendorExceptionMessage('CML Submit Order ', $body);

      return 0;
    }
  }

  /**
   * Method to get shipment details from CML/OM.
   */
  public function updateShipment($data) {

    // Get values from OrderRestResource.
    $shipment_id = \Drupal::database()->query('SELECT
      entity_id 
      FROM commerce_shipment__field_so_no 
      WHERE field_so_no_value = :so_no', [':so_no' => $data['so_no']]
    )->fetchAll()[0]->entity_id;
    if (!empty($shipment_id)) {
      $shipment = Shipment::load($shipment_id);
      $shipment->tracking_code = $data['tracking_number'];
      $shipment->field_oracle_rs_date = $data['oracle_rs_date'];
      $shipment->field_schedule_number = $data['schedule_number'];
      $shipment->field_secondary_tracking_number = $data['secondary_tracking_number'];
      $shipment->field_ship_date = $data['ship_date'];
      $shipment->field_so_no = $data['so_no'];
      $shipment->save();

      return $shipment;
    }
    elseif (empty($shipment_id)) {
      return ['Error' => 'There is no order with the given so number, ' . $data['so_no']];
    }
  }

  /**
   * Method to create Custom Address.
   */
  protected function createCustomAddress($order, $responsibility_key, $operating_unit) {
    $shipping_address = $this->getShippingAddress($order, TRUE);
    $json_array_construct = array(
      'CUST_ACCOUNT_ID' => "20892",
      'ORG_ID' => (string) $operating_unit,
      'USER_NAME' => 'WEBOM_TECH_USER',
      'RESP_KEY' => $responsibility_key,
      'CREATED_BY_MODULE' => "CYSTORE",
      'COUNTRY' => $shipping_address['country_code'],
      'ADDRESS1' => $shipping_address['address_line1'],
      'ADDRESS2' => $shipping_address['address_line2'],
      'ADDRESS3' => '',
      'CITY' => $shipping_address['locality'],
      'POSTAL_CODE' => $shipping_address['postal_code'],
      'STATE' => $shipping_address['administrative_area'],
      'PROVINCE' => $shipping_address['administrative_area'],
    );

    $body = json_encode($json_array_construct);
    echo '<pre>'; print('Customer Account Input:'); print_r
    ($body);
    $client = \Drupal::httpClient();
    try {
      $request = $client->post(
         $this->config['customer_account_creation']['end_point'],
         [
           'body' => $body,
           'headers' => [
             'auth' => [
               $this->config['customer_account_creation']['username'],
               $this->config['customer_account_creation']['password'],
             ],
             'Content-Type' => 'application/json',
           ],
         ]
      );

      $response = $request->getBody();
      $response_data = json_decode($response);
      echo '<pre>'; print('Customer Account Response:'); print_r
      ($response_data);
      if (isset($response_data->P_CUST_ACCT_SITE_ID)
         && !empty($response_data->P_CUST_ACCT_SITE_ID)) {
        return [
          (int) $response_data->P_CUST_ACCT_SITE_ID,
          (int) $response_data->P_CUST_SITE_USE_ID,
        ];
      }
      else {
        $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : CML' . '<br/>' . 'Request Body :' . $body . '<br/>' . 'Response: ' . $response;
        $this->emailVendorExceptionMessage('CML Submit Order ', $body);
        return ['', ''];
      }
    }
    catch (\Exception $e) {
      echo '<pre>'; print_r('Error: ' . $e->getMessage()); exit;
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : CML' . '<br/>' . 'Request Body :' . $body . '<br/>' . 'Error: ' . $e->getMessage();
      $this->emailVendorExceptionMessage('CML Submit Order ', $body);

      return ['', ''];
    }
  }

}
