<?php

namespace Drupal\cypress_store_vendor;

use Drupal\Core\Render\Markup;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_order\Entity\Order;
use Drupal\user\Entity\User;
use Drupal\Core\Locale\CountryManager;

/**
 * Class ShippingConfirmationEmail.
 *
 * @package Drupal\cypress_store_vendor
 */
class ShippingConfirmationEmail {

  /**
   * Constructor.
   */
  public function __construct() {
  }

  /**
   * Method to send shipping confirmation email for each shipment.
   *
   * @param object $shipment
   *   Shipment details.
   */
  public function shippingEmail($shipment) {
    $shipment_id = $shipment->get('shipment_id')->getValue()[0]['value'];
    $output = '';
    $shipment = Shipment::load($shipment_id);
    $tracking_number = $shipment->getTrackingCode();
    $order_id = $shipment->getOrderId();
    $order = Order::load($order_id);
    $order_placed_date = $order->getCreatedTime();
    $placed_order_date = $date = date('d M Y', $order_placed_date);
    $order_mail = $order->getEmail();
    $user_id = $order->getCustomerId();
    $user_object = User::load($user_id);
    $username = $user_object->field_first_name->value . ' ' . $user_object->field_last_name->value;
    if (isset($username) && !empty($username)) {
      $order_username = $username;
    }
    $order_username = $user_object->name->value;
    $shipping_address = $shipment->getShippingProfile()
      ->get('field_contact_address')
      ->getValue();
    if (!empty($tracking_number)) {
      $shipping_tracking = '<p style="">Tracking Number: ' . $tracking_number . '</p>';
    }
    if (!empty($shipping_address[0]['given_name'])) {
      $first_name = $shipping_address[0]['given_name'];
    }
    if (!empty($shipping_address[0]['family_name'])) {
      $last_name = $shipping_address[0]['family_name'];
    }
    if (!empty($shipping_address[0]['address_line1'])) {
      $address_line1 = $shipping_address[0]['address_line1'];
    }
    if (!empty($shipping_address[0]['address_line2'])) {
      $address_line2 = '<p style="margin-top: 0;margin-bottom: 3px;">' . $shipping_address[0]['address_line2'] . '</p>';
    }
    if (!empty($shipping_address[0]['postal_code'])) {
      $postal_code = $shipping_address[0]['postal_code'];
    }
    if (!empty($shipping_address[0]['dependent_locality'])) {
      $dependent_locality = '<p style="margin-top: 0;margin-bottom: 3px;">' . $shipping_address[0]['dependent_locality'] . '</p>';
    }
    if (!empty($shipping_address[0]['locality'])) {
      $locality = $shipping_address[0]['locality'];
    }
    if (!empty($shipping_address[0]['administrative_area'])) {
      $administrative_area = $shipping_address[0]['administrative_area'];
    }
    if (!empty($shipping_address[0]['country_code'])) {
      $country_list = CountryManager::getStandardList();
      $country_code = array_search($shipping_address[0]['country_code'], $country_list);
      if (array_key_exists($country_code, $country_list)) {
        $country = $country_list[$country_code];
      }
    }

    if (!empty($shipping_address[0]['contact'])) {
      $telephone = $shipping_address[0]['contact'];
    }

    $shipment_items = $shipment->getItems();
    $host = \Drupal::request()->getHost();
    $output .= '<a href="' . $host . '" style="margin-top: 15px;"><img style="display: block;margin: 0 auto;" src="' . $host . '/themes/cypress_store/images/cypress_logo_mail.png"></a>';
    $output .= '<div class = "shipping-confirmation"><h3 style="font-size:22px;padding:0 15px;">Shipping Confirmation</h3>';
    $output .= '<div class = "thankyou-message"><h4 style="padding:0 15px;">Hi ' . $order_username . ',</h4><p style="padding:0 15px;">This is a friendly notification that the below items from your Cypress Store order has shipped.</p>
               <p style="padding:0 15px;">You can track the status of this order, and all your orders, online by visiting your account <a href="' . $host . '/myorders">here</a>.</p></div>';
    $number_of_items = count($shipment_items);
    $output .= '<div style="font-weight: bold;margin: 15px 0;padding:0 15px;" class ="num-items">Number of items: ' . $number_of_items . '</div>';
    $output .= '<div>' . $shipping_tracking . '</div>';
    foreach ($shipment_items as $shipment_item) {
      $order_item_id = $shipment_item->getOrderItemID();
      $order_item = OrderItem::load($order_item_id);
      $product_variation = $order_item->getPurchasedEntity();
      if (!empty($product_variation)) {
        $product_id = $product_variation->get('product_id')
          ->getValue()[0]['target_id'];
        if (!empty($product_id)) {
          $product = Product::load($product_id);
          $product_title = $product->getTitle();
          $quantity = (int) $order_item->getQuantity();
          $product_price = $order_item->getUnitPrice();
          $product_unit_price = number_format($product_price->getNumber(), 2, '.', '');
          $product_type = $product->get('type')->getValue()[0]['target_id'];
          $cart_image = \Drupal::service('cypress_checkout_flow.default')
            ->getOrderItemImage($order_item);
          $output .= '<div style="background: #f8f8f8;padding: 15px;">
              <div style="width: 20%;display: inline-block;" class = "output"><img style="height: 100px;width: 100px;vertical-align: middle;" src ="' . $cart_image . '" height="100" width="100"></div>
              <div style="width: 50%;display: inline-block;" class = "product-title">' . $product_title . '</div>
              <div style="width: 13%;display: inline-block;font-weight: bold;" class = "product-qty">' . $quantity . 'x</div>
              <div style="width: 15%;display: inline-block;font-weight: bold;" class = "product-price">$' . $product_unit_price . '</div>
            </div>';
        }
      }
    }
    $output .= '<div class = "delivery-address" style="padding:0 15px;"><h3 style="margin: 10px 0;"> Delivery Address </h3> <p style="margin-top: 0;margin-bottom: 3px;text-transform: capitalize;">' . $first_name . ' ' . $last_name . '</p>
                <p style="margin-top: 0;margin-bottom: 3px;">' . $address_line1 . '</p>'
      . $address_line2 .
      $dependent_locality .
      '<p style="margin-top: 0;margin-bottom: 3px;">' . $locality . ' ' . $postal_code . '</p>
                <p style="margin-top: 0;margin-bottom: 3px;">' . $country . '</p>
                <p style="margin-top: 0;margin-bottom: 3px;">' . $telephone . '</p></div>';
    $output .= '<div class="col-md-12 col-sm-12">
    <div style="margin: 3em 0;width: 50%;display: inline-block;" class="track-shipment col-md-6 col-sm-6 col-xs-12">
      <a style="background: #337ab7;padding: 1em 7em;color: #fff;text-decoration: none;font-weight: bold;font-size: 14px;" href="' . $host . '/shipment/' .
      $order_id . '">
        TRACK SHIPMENT
      </a>
    </div>
    <div style="width: 49%;display: inline-block;margin: 3em 0;" class="contact-us col-md-6 col-sm-6 col-xs-12">
      <a style="background: #5bc0de;padding: 1em 8em;color: #fff;text-decoration: none;font-weight: bold;font-size: 14px;" href="#">
        CONTACT US
      </a>
    </div>
    <div class = "shipping-email-footer">
    <p style="line-height: 1.7;">Visit <a href="' . $host . '/myorders">Myorders</a> to track your shipment. Please note that tracking information may not be available immediately.
    If you need to print an invoice for this order, visit <a href="' . $host . '/myorders">Myorders</a>, find the order you want to print an invoice for in the list and click the "View Invoice" link. the next page will serve as your official invoice.</p>
    <p>For any questions, or problems regarding your order, Please Contact Us online.</p><p><b>Please Note: </b> This Email message was sent from a notification-only address that cannot accept incoming email. Please do not reply to this message.</p>
    <div style="margin-top: 3em;"><p style="margin: 3px 0">Thanks for Shopping with Us.</p>
    <p style="margin: 3px 0">The Cypress Store Team</p></div>
    </div>
  </div></div>';

    /*
     * Send shipping confirmation email.
     */
    $mail_manager = \Drupal::service('plugin.manager.mail');
    $module = 'cypress_store_vendor';
    $key = 'shipping_confirmation_mail';
    $to = 'chirag@valuebound.com';
    $params['headers']['Bcc'] = 'disha.bhadra@valuebound.com, manoj.k@valuebound.com';
    $params['message'] = Markup::create($output);
    $params['subject'] = t('Your Cypress Store order has shipped (Order# @order_id)', ['@order_id' => $order_id]);
    $params['title'] = t('Your Cypress Store order has shipped (Order# @order_id)', ['@order_id' => $order_id]);
    $langcode = \Drupal::currentUser()->getPreferredLangcode();
    $send = TRUE;
    $result = $mail_manager->mail($module, $key, $to, $langcode, $params, NULL, $send);

    if ($result['result'] !== TRUE) {
      drupal_set_message(t('There was a problem sending your message and it was not sent.'), 'error');
    }
    else {
      drupal_set_message(t('Your message has been sent.'));
    }

  }

}
