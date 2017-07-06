<?php

namespace Drupal\cypress_checkout_flow;

/**
 * Verify the IP.
 */
class IpVerification {

  /**
   * Get the country name from IP.
   */
  public function customIpVerification() {
    $name = '';
    $client_ip = get_client_ip();
    if (!empty($client_ip)) {
      $detail = file_get_contents('https://freegeoip.net/json/' . $client_ip);
      $data = json_decode($detail);
      $country_name = $data->country_name;
      if ($country_name == 'China') {
        $name = TRUE;
      }
    }
    return $name;
  }

}

/**
 * Getting client Ip Address.
 *
 * @return string
 *   Return ipaddress.
 */
function get_client_ip() {
  $ipaddress = '';
  if (isset($_SERVER['HTTP_CLIENT_IP'])) {
    $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
  }
  elseif (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
  }
  elseif (isset($_SERVER['HTTP_X_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
  }
  elseif (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
  }
  elseif (isset($_SERVER['HTTP_FORWARDED'])) {
    $ipaddress = $_SERVER['HTTP_FORWARDED'];
  }
  elseif (isset($_SERVER['REMOTE_ADDR'])) {
    $ipaddress = $_SERVER['REMOTE_ADDR'];
  }
  else {
    $ipaddress = FALSE;
  }
  return $ipaddress;
}
