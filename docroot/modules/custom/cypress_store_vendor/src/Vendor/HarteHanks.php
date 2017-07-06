<?php

namespace Drupal\cypress_store_vendor\Vendor;

use Drupal\commerce_order\Entity\OrderItem;

/**
 * Class HarteHanks.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 */
class HarteHanks extends VendorBase {

  /**
   * Harte Hanks constructor.
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Function to submit order for harte hanks.
   *
   * @param object $order
   *   Order Object.
   * @param object $shipment
   *   Shipment Object.
   *
   * @return bool
   *   Boolean value.
   */
  public function submitOrder($order, $shipment) {
    $shipment_id = $shipment->get('shipment_id')->getValue()[0]['value'];
    // $order = Order::load($orderId);
    /*$billing_address = $this->getBillingAddress($order);*/
    $shipping_address = $this->getShippingAddress($order);
    $createdtimestamp = $order->get('created')->getValue();
    $order_date = date('Y-m-d H:i:s', $createdtimestamp[0]['value']);

    $user_name = $this->config['Username'];
    $password = $this->config['Password'];
    $shipping_option = 'UPS Ground';
    $ba_first_name = trim($shipping_address['given_name']);
    $ba_last_name = trim($shipping_address['family_name']);
    $ba_company_name = $shipping_address['organization'];
    $ba_address1 = $shipping_address['address_line1'];
    $ba_address2 = $shipping_address['address_line2'];
    $ba_city = $shipping_address['locality'];
    $ba_state = $shipping_address['administrative_area'];
    $ba_zipcode = $shipping_address['postal_code'];
    $ba_country_code = $shipping_address['country_code'];
    $ba_phone = $shipping_address['contact'];

    $sa_first_name = trim($shipping_address['given_name']);
    $sa_last_name = trim($shipping_address['family_name']);
    $sa_company_name = $shipping_address['organization'];
    $sa_address1 = $shipping_address['address_line1'];
    $sa_address2 = $shipping_address['address_line2'];
    $sa_city = $shipping_address['locality'];
    $sa_state = $shipping_address['administrative_area'];
    $sa_zipcode = $shipping_address['postal_code'];
    $sa_country_code = $shipping_address['country_code'];
    $sa_phone = $shipping_address['contact'];
    $email = $order->getEmail();

    // $order_items = $order->getItems();
    $shipment_items = $shipment->get('items')->getValue();
    $order_detail = '';
    foreach ($shipment_items as $shipment_item) {
      $product_quantity = (integer) $shipment_item['value']->getQuantity();
      $order_item = OrderItem::load($shipment_item['value']->getOrderItemId());
      $product_mpn_id = $this->getProductMpnId($order_item);
      // Construct order detail xml.
      $order_detail .= "
        <OfferOrdered>
          <Offer>
            <Header>
              <ID>$product_mpn_id</ID>
            </Header>
          </Offer>
          <Quantity>$product_quantity</Quantity>
          <OrderShipToKey>
            <Key>0</Key>
          </OrderShipToKey>
          <Comments>HH</Comments>
        </OfferOrdered>";

    }

    $parameter = <<<XML
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
   <soap:Header>
      <AuthenticationHeader xmlns="http://sma-promail/">
         <Username>$user_name</Username>
         <Password>$password</Password>
      </AuthenticationHeader>
   </soap:Header>
   <soap:Body>
      <AddOrder xmlns="http://sma-promail/">
         <order>
            <Header>
               <ID>$shipment_id</ID>
               <ReferenceNumber/>
               <Comments/>
            </Header>
            <Shipping>
               <ShippingOption>
                  <Description>$shipping_option</Description>
               </ShippingOption>
               <ShipComments/>
            </Shipping>
            <OrderedBy>
               <Prefix/>
               <FirstName>$ba_first_name</FirstName>
               <LastName>$ba_last_name</LastName>
               <CompanyName>$ba_company_name</CompanyName>
               <Address1>$ba_address1</Address1>
               <Address2>$ba_address2</Address2>
               <City>$ba_city</City>
               <State>$ba_state</State>
               <PostalCode>$ba_zipcode</PostalCode>
               <Phone>$ba_phone</Phone>
               <Email>$email</Email>
               <TaxExempt>false</TaxExempt>
               <TaxExemptApproved>false</TaxExemptApproved>
               <Commercial>false</Commercial>
            </OrderedBy>
            <ShipTo>
               <OrderShipTo>
                  <Comments/>
                  <FirstName>$sa_first_name</FirstName>
                  <LastName>$sa_last_name</LastName>
                  <CompanyName>$sa_company_name</CompanyName>
                  <Address1>$sa_address1</Address1>
                  <Address2>$sa_address2</Address2>
                  <City>$sa_city</City>
                  <State>$sa_state</State>
                  <PostalCode>$sa_zipcode</PostalCode>
                  <TaxExempt>false</TaxExempt>
                  <TaxExemptApproved>false</TaxExemptApproved>
                  <Commercial>false</Commercial>
                  <Flag>Other</Flag>
                  <Key>0</Key>
                  <Rush>false</Rush>
               </OrderShipTo>
            </ShipTo>
            <BillTo>
               <TaxExempt>false</TaxExempt>
               <TaxExemptApproved>false</TaxExemptApproved>
               <Commercial>false</Commercial>
               <Flag>OrderedBy</Flag>
            </BillTo>
            <Offers>
               $order_detail
            </Offers>
         </order>
      </AddOrder>
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
        "SOAPAction: \"http://sma-promail/AddOrder\"",
      ];
      // SOAPAction: your op URL.
      // $url = 'https://oms.harte-hanks.com/pmomsws/order.asmx?op=AddOrder';
      $url = $this->config['addOrderEndPoint'];

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

      $content = substr($response, strpos($response, '<AddOrderResult>'));
      $content = str_ireplace('<![CDATA[', '', $content);
      $content = $this->cleanTrailingXml($content);
      $content = str_ireplace(']]>', '', $content);
      $content = htmlspecialchars_decode($content);

      $shipments = new \SimpleXMLElement($content);
      $shipments_array = json_decode(json_encode((array) $shipments), TRUE);
      $shipment->setData('HH', $shipments_array);
      $shipment->save();
      return TRUE;

    }
    catch (\Exception $e) {

      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : HarteHanks' . '<br/>' . 'Request Body :' . htmlentities($parameter) . '<br/>' . 'Response Body : ' . htmlentities($response);

      $this->emailVendorExceptionMessage('HarteHanks Submit Order ', $body);

      return FALSE;
    }

  }

  /**
   * Function to get inventory for harte hanks.
   *
   * @param string $mpn
   *   Marketing part number.
   *
   * @return int|\SimpleXMLElement[]
   *   Quantity.
   */
  public function getInventory($mpn) {
    $user_name = $this->config['Username'];
    $password = $this->config['Password'];

    $parameter = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<soap:Header>
		<AuthenticationHeader xmlns="http://sma-promail/">
			<Username>$user_name</Username>
			<Password>$password</Password>
		</AuthenticationHeader>
		<DebugHeader xmlns="http://sma-promail/">
			<Debug>true</Debug>
		</DebugHeader>
	</soap:Header>
	<soap:Body>
		<GetProductAvailabilities xmlns="http://sma-promail/">
			<partNumber>$mpn</partNumber>
			<!--<owner>D46-Cypress Material Management</owner>-->
		</GetProductAvailabilities>
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
        "SOAPAction: \"http://sma-promail/GetProductAvailabilities\"",
      ];

      $url = $this->config['productAvailabilitiesEndPoint'];

      // PHP cURL  for https connection with auth.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
      // The SOAP request.
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      // Converting.
      $response = curl_exec($ch);
      curl_close($ch);

      $content = substr($response, strpos($response, '<WarehouseLevels>'));
      $content = str_ireplace('<![CDATA[', '', $content);
      $content = $this->cleanTrailingXml($content);
      $content = str_ireplace(']]>', '', $content);
      $content = htmlspecialchars_decode($content);

      $shipments = new \SimpleXMLElement($content);

      if (isset($shipments) && $shipments->Available > 0) {
        return $shipments->Available;
      }
      else {
        $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : HarteHanks' . '<br/>' . 'Request Body :' . htmlentities($parameter) . '<br/>' . 'Response Body : ' . htmlentities($response);
        $this->emailVendorExceptionMessage('HarteHanks Get Product Availability ', $body);
        return 0;
      }

    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : HarteHanks' . '<br/>' . 'Request Body :' . htmlentities($parameter) . '<br/>' . 'Response Body : ' . htmlentities($response);
      $this->emailVendorExceptionMessage('HarteHanks Get Product Availability ', $body);
    }
  }

  /**
   * HarteHanks Get Order Info.
   *
   * @param array $param
   *   Shipment data.
   *
   * @return mixed
   *   Tracking number.
   */
  public function getShipment($param = []) {
    $order_id = unserialize($param->data)['HH']['OrderID'];
    $user_name = $this->config['Username'];
    $password = $this->config['Password'];
    $parameter = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<soap:Envelope xmlns:soap="http://schemas.xmlsoap.org/soap/envelope/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema">
	<soap:Header>
		<AuthenticationHeader xmlns="http://sma-promail/">
			<Username>$user_name</Username>
			<Password>$password</Password>
		</AuthenticationHeader>
		<DebugHeader xmlns="http://sma-promail/">
			<Debug>true</Debug>
		</DebugHeader>
	</soap:Header>
	<soap:Body>
		<GetOrderInfo xmlns="http://sma-promail/">
			<orderId>$order_id</orderId>
		</GetOrderInfo>
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
        "SOAPAction: \"http://sma-promail/GetOrderInfo\"",
      ];
      // SOAPAction: your op URL.
      $url = $this->config['orderInfoEndPoint'];

      // PHP cURL  for https connection with auth.
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
      curl_setopt($ch, CURLOPT_TIMEOUT, 10);
      curl_setopt($ch, CURLOPT_POST, TRUE);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $parameter);
      // The SOAP request.
      curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

      // Converting.
      $response = curl_exec($ch);
      curl_close($ch);

      $content = substr($response, strpos($response, '<GetOrderInfoResult>'));
      $content = str_ireplace('<![CDATA[', '', $content);
      $content = $this->cleanTrailingXml($content);
      $content = str_ireplace(']]>', '', $content);
      $content = htmlspecialchars_decode($content);

      $shipments = new \SimpleXMLElement($content);

      if (!empty($shipments->ShippingOrders) && isset($shipments->ShippingOrders->PickPackType->Packages->PackagesType->TrackingId)) {
        $vendor_tracking_data = [];
        $vendor_tracking_data['trackingId'] = $shipments->ShippingOrders->PickPackType->Packages->PackagesType->TrackingId;
        $vendor_tracking_data['carrier'] = $shipments->ShippingOrders->PickPackType->Packages->PackagesType->Carrier;
        // E.g: FedEx, UPS.
        $vendor_tracking_data['shippedDate'] = $shipments->ShippingOrders->PickPackType->Packages->PackagesType->DateShipped;
        $vendor_tracking_data['secondaryTrackingNumber'] = '';
        return $vendor_tracking_data;
      }

    }
    catch (\Exception $e) {
      $body = 'Environment : ' . $_ENV['AH_SITE_ENVIRONMENT'] . '<br/>' . 'Vendor : HarteHanks' . '<br/>' . 'Request Body :' . htmlentities($parameter) . '<br/>' . 'Response Body : ' . htmlentities($response);

      $this->emailVendorExceptionMessage('HarteHanks Get Order Info ', $body);
    }

  }

  /**
   * Function to clean trailing XML.
   *
   * @param string $content
   *   XML data.
   *
   * @return mixed
   *   String.
   */
  protected function cleanTrailingXml($content) {
    $trailing_xml_tags = [
      '</soap:Envelope>',
      '</soap:Body>',
      '</GetOrderInfoResponse>',
      '</Warehouses>',
      '</ProductAvailabilities>',
      '</GetProductAvailabilitiesResult>',
      '</GetProductAvailabilitiesResponse>',
      '</AddOrderResponse>',
    ];

    return $this->replaceTrailingXmlTags($trailing_xml_tags, $content);
  }

  /**
   * Replacing Trailing Xml Tags.
   *
   * @param string $trailing_xml_tags
   *   Trailing xml tags.
   * @param string $content
   *   XML data.
   *
   * @return string
   *   Trimmed XML content.
   */
  protected function replaceTrailingXmlTags($trailing_xml_tags, $content) {
    foreach ($trailing_xml_tags as $xml_tag) {
      $content = str_ireplace($xml_tag, '', $content);
    }

    return trim($content);
  }

}
