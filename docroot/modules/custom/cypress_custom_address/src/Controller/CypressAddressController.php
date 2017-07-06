<?php

namespace Drupal\cypress_custom_address\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\profile\Entity\Profile;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Drupal\Core\Url;

/**
 * Controller routine for page example route.
 */
class CypressAddressController extends ControllerBase {

  /**
   * Method to get deliver your features.
   *
   * @param int $profile_id
   *   Marketing Part number.
   *
   * @return string
   *   Returns a url.
   */
  public function content($profile_id) {
    $orders = \Drupal::service('commerce_cart.cart_provider')->getCarts();
    $order = array_pop($orders);
    $order_id = $order->id();
    $shipping_profile = Profile::load($profile_id);
    $error_message = \Drupal::service('cypress_custom_address.shipping_profile')
      ->setShippingProfile($order, $shipping_profile);
    if ($error_message) {
      drupal_set_message($error_message, 'error');
      return new RedirectResponse('\cart');
    }
    // Encrypt the order id.
    $encrypted_order_id = \Drupal::service('cypress_checkout_flow.encrypt_decrypt')->customEncryptDecrypt($order_id, 'e');

    // Reroute to checkout page.
    $order->set('checkout_step', 'order_information');
    $order->save();

    $url = Url::fromRoute('commerce_checkout.form', ['commerce_order' => $encrypted_order_id, 'step' => 'order_information']);
    return new RedirectResponse($url->toString());
  }

}
