<?php

namespace Drupal\cypress_scheduler\Form;

use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provide dashboard order information.
 */
class DashboardOrderSubmitForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'manual_vendor_order_submit';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // TODO: Implement buildForm() method.
    $form['order_id'] = [
      '#type' => 'textfield',
      '#title' => t('Enter OrderId:'),
      '#required' => TRUE,
      '#ajax' => [
        'callback' => '::checkForShipmentId',
        'effect' => 'fade',
        'event' => 'change',
        'progress' => [
          'type' => 'throbber',
          'message' => NULL,
        ],
        'wrapper' => 'shipping-content',
      ],
    ];

    $form['shipment'] = [
      '#type' => 'select',
      '#chosen' => TRUE,
      '#empty_option' => $this->t('Select a Shipping Id'),
      '#default_value' => 'All',
      '#prefix' => '<div id="shipping-content">',
      '#suffix' => '</div>',
      '#validated' => TRUE,
    ];

    $form['actions']['#type'] = 'actions';
    $form['actions']['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Place Order To Vendor'),
      '#button_type' => 'primary',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // TODO: Implement submitForm() method.
    $order_id = $form_state->getValue()['order_id'];
    $order = Order::load($order_id);
    if (is_object($order)) {
      if (!empty($form_state->getValue('shipment'))) {
        $shipment_id = $form_state->getValue('shipment');
        $shipment = Shipment::load($shipment_id);
        $vendor = $shipment->get('field_vendor')->getValue()[0]['value'];
        $this->placeOrderAtVendorEnd($vendor, $order, $shipment);
      }
      else {
        $shipments = $order->get('shipments')->referencedEntities();

        foreach ($shipments as $shipment) {
          $vendor = $shipment->get('field_vendor')->getValue()[0]['value'];
          $this->placeOrderAtVendorEnd($vendor, $order, $shipment);
        }
      }
    }
    else {
      drupal_set_message(t("Please Enter Valid Order Id."), 'error');
    }
  }

  /**
   * Helper Function Handling ajax submition and rebuilding shipment field.
   */
  public function checkForShipmentId(array &$form, FormStateInterface $form_state) {
    $order_id = $form_state->getValue('order_id');
    $order = Order::load($order_id);
    $shipment_id = $this->getShipmentId($order);
    $form['shipment']['#options'] = $shipment_id;
    return $form['shipment'];

  }

  /**
   * Helper function returning ShipmentId array.
   *
   * @param int $order
   *   Order data.
   *
   * @return array
   *   Return shipmentid.
   */
  public function getShipmentId($order) {
    $shipment_id = [];
    $shipments = $order->get('shipments')->referencedEntities();
    $shipment_id[''] = t('All');
    foreach ($shipments as $shipment) {
      $shipment_id[$shipment->id()] = $shipment->id();
    }
    return $shipment_id;
  }

  /**
   * Helper Function for submiting order at Vendor End.
   */
  public function placeOrderAtVendorEnd($vendor, $order, $shipment) {
    if ($shipment->getState()->value == 'New') {
      // Calling Service That do Order Submit on vendor End.
      \Drupal::service('cypress_store_vendor.vendor')
        ->submitOrder($vendor, $order, $shipment);
      if ($shipment->getState()->value == 'In progress') {
        drupal_set_message(t("Order Placed Successfully at Vendor's End."));
      }
      else {
        drupal_set_message(t("There was a problem Submitting Order at Vendor's End. Please Check Your Mail."), 'error');
      }
    }
  }

}
