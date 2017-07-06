<?php

namespace Drupal\cypress_order_cancellation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_order\Entity\Order;

/**
 * Class OrderCancellationForm.
 *
 * @package Drupal\cypress_order_cancellation\Form
 */
class OrderCancellationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'order_cancellation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $order_id = NULL) {
    $order = Order::load(1831);
    $full_options[$order_id] = 'Select All';
    $form['#attached']['library'][] = 'cypress_order_cancellation/order-cancellation';
    // Select all checkbox for comeplete order cancellation.
    $form["fullorder_checkbox"] = array(
      "#type"       => "checkboxes",
      "#options"    => $full_options,
      "#attributes" => array('class' => array('full-order-checkbox')),
    );
    foreach ($order->getItems() as $order_item) {
      $id = $order_item->Id();
      $title = $order_item->getTitle();
      $single_options[$id] = $title;
    }
    // Single order items checkboxes for cancellation.
    $form["singleitem_checkbox"] = array(
      "#type"    => "checkboxes",
      "#options" => $single_options,
    );
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Cancel Order'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Display result.
    foreach ($form_state->getValues() as $key => $value) {
      drupal_set_message($key . ': ' . $value);
    }

  }

}
