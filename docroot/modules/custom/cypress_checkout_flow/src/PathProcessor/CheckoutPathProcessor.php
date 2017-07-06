<?php

namespace Drupal\cypress_checkout_flow\PathProcessor;

use Drupal\Core\PathProcessor\OutboundPathProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Symfony\Component\HttpFoundation\Request;
use Drupal\Core\PathProcessor\InboundPathProcessorInterface;

/**
 * Processes the inbound and outbound.
 */
class CheckoutPathProcessor implements InboundPathProcessorInterface, OutboundPathProcessorInterface {

  /**
   * {@inheritdoc}
   */
  public function processInbound($path, Request $request) {
    // Fetch the current path to get the order id.
    $current_path = \Drupal::service('path.current')->getPath();
    $param = explode('/', $current_path);
    // Execute this code on checkout page.
    if ($param['1'] == 'checkout') {
      $order_id = $param['2'];
      // If path matches with URL.
      if (preg_match('/\/checkout\/[a-zA-Z0-9]+\/([a-zA-Z_]+)/i', $path, $matches)) {
        $step = $matches['1'];
        // Decrypt the order id.
        $decrypted = \Drupal::service('cypress_checkout_flow.encrypt_decrypt')->customEncryptDecrypt($order_id, 'd');
        // Fetch the original path and process.
        $path = '/checkout/' . $decrypted . '/' . $step;
      }
    }
    return $path;
  }

  /**
   * {@inheritdoc}
   */
  public function processOutbound($path, &$options = [], Request $request = NULL, BubbleableMetadata $bubbleable_metadata = NULL) {
    // Fetch the original path.
    $current_path = \Drupal::service('path.current')->getPath();
    $param = explode('/', $current_path);
    // Execute this code on checkout page.
    if ($param['1'] == 'checkout') {
      $order_id = $param['2'];
      // If path matches with URL.
      if (preg_match('/\/checkout\/[a-zA-Z0-9]+\/([a-zA-Z_]+)/i', $path, $matches)) {
        $step = $matches['1'];
        // Encrypt the ordr id.
        $encrypted = \Drupal::service('cypress_checkout_flow.encrypt_decrypt')->customEncryptDecrypt($order_id, 'e');
        // Convert path with hash value.
        $path = '/checkout/' . $encrypted . '/' . $step;
      }
    }
    return $path;
  }

}
