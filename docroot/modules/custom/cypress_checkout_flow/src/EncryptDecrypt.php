<?php

namespace Drupal\cypress_checkout_flow;

/**
 * Encrypt and Decrypt.
 */
class EncryptDecrypt {

  /**
   * The function to encrypt and decrypt the order number.
   *
   * @param string $string
   *   String data.
   * @param string $action
   *   Action data.
   *
   * @return bool|string
   *   return output.
   */
  public function customEncryptDecrypt($string, $action = 'e') {
    $secret_key = 'customEncryptDecrypt_secret_key';
    $secret_iv = 'customEncryptDecrypt_secret_iv';
    $output = FALSE;
    $encrypt_method = "AES-256-CBC";
    $key = hash('sha256', $secret_key);
    $iv = substr(hash('sha256', $secret_iv), 0, 4);
    if ($action == 'e') {
      $output = base64_encode(openssl_encrypt($string, $encrypt_method, $key, 0, $iv));
    }
    elseif ($action == 'd') {
      $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
  }

}
