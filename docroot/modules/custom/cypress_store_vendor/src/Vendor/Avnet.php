<?php

namespace Drupal\cypress_store_vendor\Vendor;

use Drupal\commerce_shipping\Entity\Shipment;

/**
 * Class Avnet.
 *
 * @package Drupal\cypress_store_vendor
 */
class Avnet extends VendorBase {

  /**
   * Avnet constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Method to get inventory details from Avnet.
   *
   * @param string $mpn
   *   Marketing Part number.
   *
   * @return int|string
   *   Part quantity in Avnet.
   */
  public function getInventory($mpn) {
    $inventory_details = \Drupal::configFactory()
      ->getEditable('cypress_store_vendor.avnet_inventory_entity.details')
      ->get('details');
    $inventory = unserialize($inventory_details);
    if (isset($inventory[$this->region]) && isset($inventory[$this->region][$mpn])) {
      return ltrim($inventory[$this->region][$mpn]['quantity'], 0);
    }
    return 0;
  }

  /**
   * Method to get whole inventory details of Avnet.
   */
  public function updateInventory() {

    $client = \Drupal::httpClient();
    $body = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pl="www.avnet.com/3pl">
  <soapenv:Header/>
  <soapenv:Body>
    <pl:gatewayMessage>
      <gatewayRequest>
        <encodedXmlRequest>
            &lt;inventory_request&gt;
            &lt;partlist get_all="true"/&gt;
            &lt;/inventory_request&gt;
        </encodedXmlRequest>
      </gatewayRequest>
    </pl:gatewayMessage>
  </soapenv:Body>
</soapenv:Envelope>
XML;
    try {
      $request = $client->post(
        $this->config['endPoint'],
        [
          'auth' => [$this->config['Username'], $this->config['Password']],
          'body' => $body,
        ]
      );

      $response = $request->getBody();
      $original_content = $response->getContents();
      $content = substr($original_content, strpos($original_content, '<encodedXmlResponse>') + 20);
      $content = $this->cleanTrailingXml($content);
      $content = htmlspecialchars_decode($content);
      if (empty(trim($content))) {
        $msg = $this->getErrorMessage($original_content);
        throw new \Exception($msg, 500);
      }

      $avnet_inventory_entity = \Drupal::configFactory()
        ->getEditable('cypress_store_vendor.avnet_inventory_entity.details');
      $avnet_inventory_entity->set('changed', REQUEST_TIME);
      $avnet_inventory_entity->set('details', $this->parseInventoryDetails($content));
      $avnet_inventory_entity->save();
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : AVNET' . '<br/>' . 'Request Body :' . htmlentities($body) . '<br/> Error: ' . $e->getMessage();
      $this->emailVendorExceptionMessage('CML Submit Order ', $body);
    }
  }

  /**
   * Method to submit order to Avnet vendor.
   *
   * @param mixed $order
   *   Commerce order.
   * @param mixed $shipment
   *   Shipment details.
   *
   * @return mixed
   *   Order id.
   */
  public function submitOrder($order, $shipment) {
    $order_date = $order->get('created')->getValue();
    $order_date = date('m/d/Y H:i', $order_date[0]['value']);
    $order_type = 'P';
    $shipping_address = $this->getShippingAddress($order);
    $first_name = trim($shipping_address['given_name']);
    $last_name = trim($shipping_address['family_name']);
    $company_name = $shipping_address['organization'];
    $address1 = $shipping_address['address_line1'];
    $address2 = $shipping_address['address_line2'];
    $city = $shipping_address['locality'];
    $state = $shipping_address['administrative_area'];
    $zipcode = $shipping_address['postal_code'];
    $country_code = $shipping_address['country_code'];
    $email = $order->getEmail();
    $phone = $shipping_address['contact'];
    $order_detail = '';
    $shipment_id = $shipment->id();
    $shipment_items = $shipment->getItems();
    $order_items_count = count($shipment_items);
    foreach ($shipment_items as $shipment_item) {
      $product_mpn_id = $shipment_item->getTitle();
      $product_quantity = $shipment_item->getQuantity();
      // Construct order detail xml.
      $order_detail .= "&lt;detail&gt;
      &lt;partno&gt;$product_mpn_id&lt;/partno&gt;
      &lt;custpartno&gt;$product_mpn_id&lt;/custpartno&gt;
      &lt;qty&gt;$product_quantity&lt;/qty&gt;
      &lt;htc&gt;&lt;/htc&gt;
      &lt;eccn&gt;&lt;/eccn&gt;
      &lt;eccnall&gt;&lt;/eccnall&gt;
      &lt;/detail&gt;";
    }

    $ship_via = $this->getShipmentMethodName($shipment);

    $client = \Drupal::httpClient();

    $body = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pl="www.avnet.com/3pl">
  <soapenv:Header/>
  <soapenv:Body>
    <pl:gatewayMessage>
      <gatewayRequest>
        <encodedXmlRequest>
          &lt;order&gt;
            &lt;order_id&gt;$shipment_id&lt;/order_id&gt;
            &lt;order_date&gt;$order_date&lt;/order_date&gt;
            &lt;order_type&gt;$order_type&lt;/order_type&gt;
            &lt;first_name&gt;$first_name&lt;/first_name&gt;
            &lt;last_name&gt;$last_name&lt;/last_name&gt;
            &lt;company_name&gt;$company_name&lt;/company_name&gt;
            &lt;address1&gt;$address1&lt;/address1&gt;
            &lt;address2&gt;$address2&lt;/address2&gt;
            &lt;city&gt;$city&lt;/city&gt;
            &lt;state&gt;$state&lt;/state&gt;
            &lt;zipcode&gt;$zipcode&lt;/zipcode&gt;
            &lt;country&gt;$country_code&lt;/country&gt;
            &lt;email&gt;$email&lt;/email&gt;
            &lt;phone&gt;$phone&lt;/phone&gt;
            $order_detail
            &lt;detail_count&gt;$order_items_count&lt;/detail_count&gt;
              &lt;application&gt;&lt;/application&gt;
              &lt;end_equipment&gt;&lt;/end_equipment&gt;
              &lt;ship_control_code/&gt;
              &lt;ship_via&gt;$ship_via&lt;/ship_via&gt;
              &lt;tpb_account/&gt;
              &lt;tpb_type/&gt;
              &lt;tpb_first_name/&gt;
              &lt;tpb_last_name/&gt;
              &lt;tpb_company_name/&gt;
             &lt;tpb_address1/&gt;
             &lt;tpb_address2/&gt;
              &lt;tpb_city/&gt;
              &lt;tpb_state/&gt;
              &lt;tpb_zipcode/&gt;
              &lt;tpb_country/&gt;
          &lt;/order&gt;
        </encodedXmlRequest>
      </gatewayRequest>
    </pl:gatewayMessage>
  </soapenv:Body>
</soapenv:Envelope>
XML;

    try {
      $request = $client->post(
        $this->config['endPoint'],
        [
          'auth' => [$this->config['Username'], $this->config['Password']],
          'body' => $body,
        ]
      );

      $response = $request->getBody();
      $original_content = $response->getContents();
      $content = substr($original_content, strpos($original_content, '<encodedXmlResponse>') + 20);
      $content = $this->cleanTrailingXml($content);
      $content = htmlspecialchars_decode($content);
      if (empty(trim($content))) {
        $msg = $this->getErrorMessage($original_content);
        throw new \Exception($msg, 500);
      }

      $order_ack = (array) new \SimpleXMLElement($content);
      return (bool) $order_ack['order_id'];
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : AVNET' . '<br/>' . 'Request Body :' . htmlentities($body) . '<br/>' . 'Response Body : ' . htmlentities($response);
      $this->emailVendorExceptionMessage('Avnet Submit Order ', $body);
    }
    return 0;
  }

  /**
   * Method to get shipment details from Avnet.
   *
   * @param array $params
   *   Parameters.
   *
   * @return mixed
   *   Shipment object.
   */
  public function getShipment($params = []) {
    $this->updateShipment();
    $shiment_obj = Shipment::load($params->shipment_id);
    if (is_object($shiment_obj)) {
      $shipment = $shiment_obj->getData('shipment');

      if (!empty($shipment) && isset($shipment['waybill_no'])) {
        $vendor_tracking_data = [];
        $vendor_tracking_data['trackingId'] = $shipment['waybill_no'];
        // e.g: FedEx, UPS.
        $vendor_tracking_data['carrier'] = $shipment['carrier'];
        $vendor_tracking_data['shippedDate'] = $shipment['ship_date'];
        $vendor_tracking_data['secondaryTrackingNumber'] = '';
        return $vendor_tracking_data;
      }
    }
  }

  /**
   * Get AVNET shipment details and update it for corresponding order shipments.
   */
  protected function updateShipment() {
    $client = \Drupal::httpClient();
    $body = <<<XML
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:pl="www.avnet.com/3pl">
  <soapenv:Header/>
    <soapenv:Body>
      <pl:gatewayMessage>
        <gatewayRequest>
          <encodedXmlRequest>
            &lt;shipment_request&gt;
              &lt;partner_id&gt;{$this->config['partnerId']}&lt;/partner_id&gt;
            &lt;/shipment_request&gt;
          </encodedXmlRequest>
        </gatewayRequest>
      </pl:gatewayMessage>
    </soapenv:Body>
</soapenv:Envelope>
XML;
    try {
      $request = $client->post(
        $this->config['endPoint'],
        [
          'auth' => [$this->config['Username'], $this->config['Password']],
          'body' => $body,
        ]
      );

      $response = $request->getBody();
      $original_content = $response->getContents();
      $content = substr($original_content, strpos($original_content, '<encodedXmlResponse>') + 20);
      $content = str_ireplace('<![CDATA[', '', $content);
      $content = $this->cleanTrailingXml($content);
      $content = str_ireplace(']]>', '', $content);
      $content = htmlspecialchars_decode($content);
      if (empty(trim($content))) {
        $msg = $this->getErrorMessage($original_content);
        throw new \Exception($msg, 500);
      }

      $shipments = new \SimpleXMLElement($content);
      foreach ($shipments as $shipment) {
        $shipment_detail = (array) $shipment->shipment;
        unset($shipment_detail['detail']);
        $order = Shipment::load($shipment_detail['order_id']);
        if (is_object($order)) {
          $order->setData('vendor', 'avnet');
          $order->setData('shipment', $shipment_detail);
          $order->save();
        }
      }
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : AVNET' . '<br/>' . 'Request Body :' . htmlentities($body) . '<br/> Error: ' . $e->getMessage();
      $this->emailVendorExceptionMessage('CML Submit Order ', $body);
    }
  }

  /**
   * Parse the Avnet inventory xml detail.
   *
   * @param string $inventory_xml
   *   Avnet inventory xml.
   *
   * @return array
   *   Whole Avnet inventory detail as an array.
   */
  protected function parseInventoryDetails($inventory_xml) {
    $inventory_details = simplexml_load_string($inventory_xml);
    $inventory = [];
    foreach ($inventory_details->part as $part) {
      $part = (array) $part;
      $inventory[$part['warehouse_code']][$part['partno']] = [
        'quantity' => $part['qoh'],
        'date' => $part['inventory_date'],
      ];
    }
    return serialize($inventory);
  }

  /**
   * Function to parse XML data.
   *
   * @param string $content
   *   XML content.
   *
   * @return mixed
   *   Parsing XML data.
   */
  protected function cleanTrailingXml($content) {
    $trailing_xml_tags = [
      '</encodedXmlResponse>',
      '</gatewayResponse>',
      '</tns:gatewayMessage>',
      '</SOAP-ENV:Body>',
      '</SOAP-ENV:Envelope>',
    ];

    return $this->replaceTrailingXmlTags($trailing_xml_tags, $content);
  }

  /**
   * Function to get error message.
   *
   * @param string $content
   *   XML content.
   *
   * @return mixed
   *   Error message.
   */
  protected function getErrorMessage($content) {
    $content = substr($content, strpos($content, '</gatewayRequest>') + 16);
    $trailing_xml_tags = [
      '</tns:gatewayMessage>',
      '</SOAP-ENV:Body>',
      '</SOAP-ENV:Envelope>',
    ];

    return $this->replaceTrailingXmlTags($trailing_xml_tags, $content);
  }

  /**
   * Function to replace trailing xml tags.
   *
   * @param string $trailing_xml_tags
   *   Stripped xml tags.
   * @param string $content
   *   XML data.
   *
   * @return string
   *   trimmed content.
   */
  protected function replaceTrailingXmlTags($trailing_xml_tags, $content) {
    foreach ($trailing_xml_tags as $xml_tag) {
      $content = str_ireplace($xml_tag, '', $content);
    }

    return trim($content);
  }

}
