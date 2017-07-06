<?php

namespace Drupal\cypress_store_vendor\Vendor;

use Drupal\commerce_order\Entity\OrderItem;

/**
 * Class DigiKey.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 */
class DigiKey extends VendorBase {

  /**
   * DigiKey constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Query for new shipment notifications.
   */
  public function queryShipment() {
    $end_point = $this->config['endPoint'];
    $parameters = array(
      'program_id' => $this->config['programId'],
      'security_id' => $this->config['securityId'],
    );
    try {
      $client = new \SoapClient($end_point);
      $response = $client->QueryShipments($parameters);
      return $response;
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : DigiKey' . '<br/>' . 'Request Body :' . htmlentities($parameters) . '<br/>' . 'Response Body : ' . $e->getMessage();
      $this->emailVendorExceptionMessage('DigiKey Query Shipment ', $body);

    }
  }

  /**
   * Retrieve shipment details for a specific order/shipment.
   *
   * @param array $param
   *   Parameter data to get shipment.
   *
   * @return mixed
   *   Tracking details.
   */
  public function getShipment($param = []) {
    $param_data = unserialize($param->data);
    $order_id = $param_data['DIGIKEY']['order_id'];
    $vid_number = $param_data['DIGIKEY']['vid_number'];

    $query_shipment = $this->queryShipment();

    foreach ($query_shipment->QueryShipmentsResult->shipments as $digikey_shipment) {
      if ($digikey_shipment->order_id == $order_id && $digikey_shipment->vid_number == $vid_number) {
        $shipment_id = $digikey_shipment->shipment_id;
        $end_point = $this->config['endPoint'];
        $parameters = array(
          'program_id' => $this->config['programId'],
          'security_id' => $this->config['securityId'],
          'vid_number' => $vid_number,
          'order_id' => $order_id,
          'shipment_id' => $shipment_id,
        );
        try {
          $client = new \SoapClient($end_point);
          $response = $client->GetShipment($parameters);

          if (is_object($response->GetShipmentResult)) {
            $vendor_tracking_data = [];
            $vendor_tracking_data['trackingId'] = $response
              ->GetShipmentResult->waybill_no;
            $vendor_tracking_data['carrier'] = $response
              ->GetShipmentResult->carrier;
            $vendor_tracking_data['shippedDate'] = $response
              ->GetShipmentResult->ship_date;
            $vendor_tracking_data['secondaryTrackingNumber'] = '';
            return $vendor_tracking_data;
          }

        }
        catch (\Exception $e) {
          $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : DigiKey' . '<br/>' . 'Request Body :' . htmlentities($parameters) . '<br/>' . 'Response Body : ' . htmlentities($response);
          $this->emailVendorExceptionMessage('DigiKey Get Shipment ', $body);

        }
      }
    }
  }

  /**
   * Query availability of sample product.
   *
   * @param string $part_number
   *   Part Number.
   *
   * @return mixed
   *   Inventory details.
   */
  public function getInventory($part_number) {
    $end_point = $this->config['endPoint'];
    $parameters = array(
      'program_id' => $this->config['programId'],
      'security_id' => $this->config['securityId'],
      'part_number' => $part_number,
    );
    try {
      $client = new \SoapClient($end_point);
      $response = $client->QueryAvailability($parameters);
      if ($response->QueryAvailabilityResult->item_count > 0) {
        return $response->QueryAvailabilityResult->items->item->quantity_available;
      }
      else {
        $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : DigiKey' . '<br/>' . 'Request Body :' . htmlentities($parameters) . '<br/>' . 'Response Body : ' . htmlentities($response);
        $this->emailVendorExceptionMessage('DigiKey Submit Order ', $body);

        return $response->QueryAvailabilityResult->item_count;
      }
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : DigiKey' . '<br/>' . 'Request Body :' . htmlentities($parameters) . '<br/>' . 'Response Body : ' . htmlentities($response);
      $this->emailVendorExceptionMessage('DigiKey Get Inventory ', $body);

      return 0;
    }
  }

  /**
   * Submit a new sample request for fulfillment.
   *
   * @param object $order
   *   Order object.
   * @param object $shipment
   *   Shipment object.
   *
   * @return bool
   *   Order submitted or not.
   */
  public function submitOrder($order, $shipment) {
    $shipment_id = $shipment->get('shipment_id')->getValue()[0]['value'];
    $shipping_address = $this->getShippingAddress($order);
    $created_timestamp = $order->get('created')->getValue();
    $order_date = gmdate('c', $created_timestamp[0]['value']);

    $program_id = $this->config['programId'];
    $security_id = $this->config['securityId'];
    $vid_number = $shipment_id;
    $order_date = $order_date;
    $order_type = 'Test';
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
    $item_count = 0;
    $shipment_items = $shipment->get('items')->getValue();
    $compliant = 'Yes';
    $backorders = 'Allow';
    $order_detail = '';
    // Parts fields for Parts.
    $primary_application = $order->get('field_primary_application')
      ->getValue()[0]['value'];
    $name_product_system = $order->get('field_name_product_system')
      ->getValue()[0]['value'];
    // Parts fields for parts and kit.
    $purpose_of_order = $order->get('field_purpose_of_order')
      ->getValue()[0]['value'];
    $end_customer = $order->get('field_end_customer')->getValue()[0]['value'];

    foreach ($shipment_items as $shipment_item) {
      $order_item = OrderItem::load($shipment_item['value']->getOrderItemId());
      $product_mpn_id = $this->getProductMpnId($order_item);
      $product_quantity = (integer) $shipment_item['value']->getQuantity();
      // Construct order detail xml.
      $order_detail .= "<detail>
      <manufacturer_part_number xsi:type=\"xsd:string\">$product_mpn_id</manufacturer_part_number>
      <customer_part_number xsi:type=\"xsd:string\">$product_mpn_id</customer_part_number>
      <quantity xsi:type=\"xsd:unsignedInt\">$product_quantity</quantity>
      <compliant xsi:type=\"xsd:bytes\">$compliant</compliant>
      <backorders xsi:type=\"xsd:bytes\">$backorders</backorders>
      </detail>";

      $item_count++;
    }

    $application = $primary_application;
    $end_equipment = $name_product_system;
    $ship_via = $this->getShipmentMethodName($shipment);
    $ship_control_code = 'Single';
    $export_compliance_done = 'Y';
    $shipping_payment_option = 'Consignee';
    $error_mode = 'SOAP';

    $end_point = $this->config['endPoint'];

    $parameter = <<<XML
<soap:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/">
   <soap:Header/>
   <soap:Body>
      <SubmitOrder xmlns="http://www.samplecomponents.com/webservices/">
         <program_id xsi:type="xsd:string">$program_id</program_id>
         <security_id xsi:type="xsd:string">$security_id</security_id>
         <order xmlns="http://www.samplecomponents.com/schemas/sample_order.xsd">
            <vid_number xsi:type="xsd:string">$vid_number</vid_number>
            <order_date xsi:type="xsd:dateTime">$order_date</order_date>
            <order_type xsi:type="xsd:byte">$order_type</order_type>
            <first_name xsi:type="xsd:string">$first_name</first_name>
            <last_name xsi:type="xsd:string">$last_name</last_name>
            <company_name xsi:type="xsd:string">$company_name</company_name>
            <address1 xsi:type="xsd:string">$address1</address1>
            <address2 xsi:type="xsd:string">$address2</address2>
            <city xsi:type="xsd:string">$city</city>
            <state xsi:type="xsd:string">$state</state>
            <zipcode xsi:type="xsd:string">$zipcode</zipcode>
            <country xsi:type="xsd:string">$country_code</country>
            <email xsi:type="xsd:string">$email</email>
            <phone xsi:type="xsd:string">$phone</phone>
            <fax xsi:type="xsd:string"/>
            <detail_count xsi:type="xsd:unsignedInt">$item_count</detail_count>
            <details>
               <!--<detail>-->
                  <!--<manufacturer_part_number xsi:type="xsd:string">CY8C3244PVI-133</manufacturer_part_number>-->
                  <!--<customer_part_number xsi:type="xsd:string">CY8C3244PVI-133</customer_part_number>-->
                  <!--<quantity xsi:type="xsd:unsignedInt">1</quantity>-->
                  <!--<compliant xsi:type="xsd:byte">Yes</compliant>-->
                  <!--<backorders xsi:type="xsd:byte">Allow</backorders>-->
               <!--</detail>-->
               $order_detail
            </details>
            <application xsi:type="xsd:string">$application</application>
            <end_equipment xsi:type="xsd:string">$end_equipment</end_equipment>
            <po xsi:type="xsd:string"/>
            <ship_via xsi:type="xsd:byte">$ship_via</ship_via>
            <ship_control_code xsi:type="xsd:byte">$ship_control_code</ship_control_code>
            <export_compliance_done xsi:type="xsd:byte">$export_compliance_done</export_compliance_done>
            <special_handling_code xsi:type="xsd:string"/>
            <program_identifier xsi:type="xsd:string"/>
            <shipping_payment_option xsi:type="xsd:byte">$shipping_payment_option</shipping_payment_option>
            <third_party_billing>
               <tpb_account xsi:type="xsd:string"/>
               <tpb_first_name xsi:type="xsd:string"/>
               <tpb_last_name xsi:type="xsd:string"/>
               <tpb_company_name xsi:type="xsd:string"/>
               <tpb_address1 xsi:type="xsd:string"/>
               <tpb_address2 xsi:type="xsd:string"/>
               <tpb_city xsi:type="xsd:string"/>
               <tpb_state xsi:type="xsd:string"/>
               <tpb_zipcode xsi:type="xsd:string"/>
               <tpb_country xsi:type="xsd:string"/>
            </third_party_billing>
            <notes xsi:type="xsd:string"/>
            <error_mode xsi:type="xsd:byte">$error_mode</error_mode>
         </order>
      </SubmitOrder>
   </soap:Body>
</soap:Envelope>
XML;

    try {

      $headers = [
        "Accept: text/xml",
        "Cache-Control: no-cache",
        "Pragma: no-cache",
        "Content-Type: text/xml; charset=utf-8",
        "Content-Length: " . strlen($parameter),
        "SOAPAction: \"http://www.samplecomponents.com/webservices/SubmitOrder\"",
      ];
      $url = $this->config['submitOrderEndPoint'];

      // PHP cURL  for https connection with auth.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      // The SOAP request.
      curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      // Converting.
      $response = curl_exec($ch);
      curl_close($ch);

      $content = substr($response, strpos($response, '<SubmitOrderResult'));
      $content = str_ireplace('<![CDATA[', '', $content);
      $content = $this->cleanTrailingXml($content);
      $content = str_ireplace(']]>', '', $content);
      $content = htmlspecialchars_decode($content);

      $shipments = new \SimpleXMLElement($content);

      $shipments_array = json_decode(json_encode((array) $shipments), TRUE);

      $shipment->setData('DIGIKEY', $shipments_array);
      $shipment->save();

      return TRUE;
    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : DigiKey' . '<br/>' . 'Request Body :' . htmlentities($parameter) . '<br/>' . 'Response Body : ' . htmlentities($response);

      $this->emailVendorExceptionMessage('DigiKey Submit Order ', $body);

      return FALSE;
    }

  }

  /**
   * Method clean trailing xml tags.
   *
   * @param string $content
   *   Content with which tags should be replaced.
   *
   * @return mixed
   *   Clean xml.
   */
  protected function cleanTrailingXml($content) {
    $trailing_xml_tags = [
      '</soap:Envelope>',
      '</soap:Body>',
      '</SubmitOrderResponse>',
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
