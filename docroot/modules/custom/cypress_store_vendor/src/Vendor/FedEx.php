<?php

namespace Drupal\cypress_store_vendor\Vendor;

require_once __DIR__ . "/../../fedex_wsdl/fedex-common.php5";

/**
 * Class FedEx.
 *
 * @package Drupal\cypress_store_vendor\Vendor
 */
class FedEx extends VendorBase {

  /**
   * FedEx constructor.
   */
  public function __construct() {
    parent::__construct();

  }

  /**
   * Normal Tracking Not Tracking By Reference.
   *
   * Not Tested As tracking number not generated
   * FedEx Standard Api TrackService_v12_php
   * Track Service.
   */
  public function trackService($tracking_number) {
    // $tracking_number = '122816215025810';
    // The WSDL is not included with the sample code.
    // Please include and reference in $path_to_wsdl variable.
    $path_to_wsdl = __DIR__ . "/../../fedex_wsdl/TrackService_v12.wsdl";

    ini_set("soap.wsdl_cache_enabled", "0");
    // Refer to http://us3.php.net/manual/en/ref.soap.php for more information.
    $client = new \SoapClient($path_to_wsdl, array('trace' => 1));

    $request['WebAuthenticationDetail'] = array(
      'ParentCredential' => array(
        'Key' => getProperty('parentkey'),
        'Password' => getProperty('parentpassword'),
      ),
      'UserCredential' => array(
        'Key' => $this->config['authenticationKey'],
        'Password' => $this->config['password'],
      ),
    );

    $request['ClientDetail'] = array(
      'AccountNumber' => $this->config['accountNumber'],
      'MeterNumber' => $this->config['meterNumber'],
    );
    $request['TransactionDetail'] = array('CustomerTransactionId' => 'Track Request');
    $request['Version'] = array(
      'ServiceId' => 'trck',
      'Major' => '12',
      'Intermediate' => '0',
      'Minor' => '0',
    );
    $request['SelectionDetails'] = array(
      'PackageIdentifier' => array(
        'Type' => 'TRACKING_NUMBER_OR_DOORTAG',
        'Value' => $tracking_number,
        // Replace 'XXX' with a valid tracking identifier.
      ),
    );

    try {
      if (setEndpoint('changeEndpoint')) {
        $new_location = $client->__setLocation(setEndpoint('endpoint'));
      }
      // Here we are changing the address location url which is in wsdl file
      // and making it dynamic for productiona and Testing instance.
      $client->__setLocation($this->config['url'] . '/track');

      $response = $client->track($request);

      if ($response->HighestSeverity != 'FAILURE' && $response->HighestSeverity != 'ERROR') {

        if ($response->HighestSeverity == 'SUCCESS' && $response->CompletedTrackDetails->HighestSeverity == 'SUCCESS') {

          if ($response->CompletedTrackDetails->TrackDetails->Notification->Severity == 'SUCCESS' && isset($response->CompletedTrackDetails->TrackDetails->StatusDetail->Code)) {
            $fedex_response = $response->CompletedTrackDetails->TrackDetails;
            $response_array = json_decode(json_encode((array) $fedex_response), TRUE);
            return $response_array;
          }
        }
      }
      /* Else {
      //   return $response;
      } */
      // Write to log file.
//      writeToLog($client);
    }
    catch (SoapFault $exception) {
      return FALSE;
    }
  }

}
