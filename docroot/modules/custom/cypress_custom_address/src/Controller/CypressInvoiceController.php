<?php

namespace Drupal\cypress_custom_address\Controller;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\Core\Locale\CountryManager;

/**
 * Class CypressInvoiceController.
 *
 * @package Drupal\cypress_custom_address\Controller
 */
class CypressInvoiceController {

  /**
   * Method to get invoice data.
   *
   * @param int $order_id
   *   Orderid number.
   *
   * @return string
   *   Returns a invoice data.
   */
  public function invoice($order_id) {
    $order = Order::load($order_id);
    $order_placed_date = $order->getCreatedTime();
    $order_placed_date1 = gmdate("Y-m-d", $order_placed_date);
    $total_price = (float) $order->getTotalPrice()->getNumber();
    $subtotal = (float) $order->getSubtotalPrice()->getNumber();
    $order_item_quantity = array();
    $order_titles = array();

    foreach ($order->getItems() as $order_item) {
      $order_items[] = $order_item->get('order_item_id')->value;
      $order_item_quantity[] = (int) $order_item->getQuantity();
      $order_titles[] = $order_item->getTitle();
      $unit_price[] = $order_item->getUnitPrice()->getNumber();
      $line_item_total[] = $order_item->getTotalPrice()->getNumber();
    }

    $shipping = $order->getAdjustments();
    $adjustment = array();
    if (!empty($shipping)) {
      foreach ($shipping as $key => $value) {
        if (is_object($value->getLabel())) {
          $label = $value->getLabel()->__toString();
        }
        else {
          $label = $value->getLabel();
        }
        $getVal = $value->getAmount();
        $amt = (float) $getVal->getNumber();
        $adjustment_amount = number_format($amt, 2, '.', '');
        $adjustment[$label][] = ' $' . $adjustment_amount;
      }
    }
    foreach ($order_items as $key => $value) {
      $order_item = OrderItem::load($value);
      $discount = $order_item->getAdjustments();
      foreach ($discount as $keys => $values) {
        $label = $values->getLabel();
        $getVal = $values->getAmount();
        $dis_amnt = (float) $getVal->getNumber();
        $quantity = $order_item->getQuantity();
        $adjustment_price = number_format($dis_amnt * $quantity, '2', '.', '');
        $discount_price = str_replace("-", " -$", $adjustment_price);
        $adjustment[$label][] = $discount_price;
      }
    }
    // Get Billing Address data.
    if (!empty($order->getBillingProfile())) {
      $billing_address = $order
        ->getBillingProfile()
        ->get('field_contact_address')
        ->getValue();
    }

    if (!empty($billing_address)) {
      $countries = CountryManager::getStandardList();
      foreach ($countries as $key => $value) {
        if ($key == $billing_address[0]['country_code']) {
          $country_name = (string) $value;
        }
      }
      $billing_name = $billing_address[0]['given_name'] . ' ' . $billing_address[0]['family_name'];
      $billing_address1 = $billing_address[0]['addresssecure_commerce_cypress_line1'];
      if (!empty($billing_address[0]['address_line2'])) {
        $billing_address2 = $billing_address[0]['address_line2'];
      }
      if (!empty($billing_address[0]['locality'])) {
        $billing_locality = $billing_address[0]['locality'];
      }
      if (!empty($country_name)) {
        $billing_country = $country_name;
      }
      if (!empty($billing_address[0]['contact'])) {
        $billing_contact = $billing_address[0]['contact'];
      }
      $billing_info = array(
        $billing_name,
        $billing_address1,
        $billing_address2,
        $billing_locality,
        $country_name,
        $billing_contact,
      );
    }
    else {
      $billing_info = array('no_address' => "No Billing Address Available.");
    }
    // Get Shipping Address data.
    $shipments = $order->get('shipments')->referencedEntities();
    $first_shipment = reset($shipments);
    if ($first_shipment !== FALSE) {
      $shipping_address = $first_shipment->getShippingProfile()
        ->get('field_contact_address')
        ->getValue();
    }
    if (!empty($shipping_address)) {
      $shipping_name = $shipping_address[0]['given_name'] . ' ' . $shipping_address[0]['family_name'];
      $shipping_address1 = $shipping_address[0]['address_line1'];
      if (!empty($shipping_address[0]['address_line2'])) {
        $shipping_address2 = $shipping_address[0]['address_line2'];
      }
      if (!empty($shipping_address[0]['locality'])) {
        $shipping_locality = $shipping_address[0]['locality'];
      }
      if (!empty($country_name)) {
        $shipping_country = $country_name;
      }
      if (!empty($shipping_address[0]['contact'])) {
        $shipping_contact = $shipping_address[0]['contact'];
      }

      $shipping_address = array(
        $shipping_name,
        $shipping_address1,
        $shipping_address2,
        $shipping_locality,
        $shipping_country,
        $shipping_contact,
      );
    }
    else {
      $shipping_address = array('no_address' => "No Shipping Address Available.");
    }
    // Generating  html.
    $invoice_template = array(
      '#theme' => 'invoice',
      '#order_date' => $order_placed_date1,
      '#order_id' => $order_id,
      '#total_price' => $total_price,
      '#quantity' => $order_item_quantity,
      '#order_titles' => $order_titles,
      '#subtotal' => $subtotal,
      '#adjustments' => $adjustment,
      '#billing_info' => $billing_info,
      '#shipping_address' => $shipping_address,
      '#unit_price' => $unit_price,
      '#line_item_total' => $line_item_total,
    );

    $output = \Drupal::service('renderer')->render($invoice_template,
      array(
        'variables' => array(
          'order_date' => $order_date,
          'order_id' => $order_id,
          'total_price' => $total_price,
          'quantity' => $quantity,
          'order_titles' => $order_titles,
          'subtotal' => $subtotal,
          'adjustments' => $adjustments,
          'billing_info' => $billing_info,
          'shipping_address' => $shipping_address,
          'unit_price' => $unit_price,
          'line_item_total' => $line_item_total,
        ),
      )
    );
    return [
      '#type' => 'markup',
      '#markup' => $output,
    ];

  }

}
