<?php

namespace Drupal\cypress_custom_address\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\commerce_order\Entity\Order;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("field_deliver_here")
 */
class DeliverHere extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function usesGroupBy() {
    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function query() {
    // Do nothing -- to override the parent query.
  }

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();

    $options['hide_alter_empty'] = ['default' => FALSE];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(&$form, FormStateInterface $form_state) {
    parent::buildOptionsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function render(ResultRow $values) {
    $profile_id = $values->profile_id;
    $current_path = \Drupal::service('path.current')->getPath();
    $param = explode('/', $current_path);
    // Execute this code on checkout page.
    if ($param['1'] == 'checkout') {
      $order_id = $param['2'];
    }
    $order = Order::load($order_id);
    $shipment_id = $order->get('shipments')->getValue()[0]['target_id'];
    if (!$shipment_id) {
      $shipment_id = 0;
    }
    $output = '<a href="/deliver-address/' . $profile_id . '">DELIVER HERE</a>';
    $result = check_markup($output, 'full_html');
    return $result;
  }

}
