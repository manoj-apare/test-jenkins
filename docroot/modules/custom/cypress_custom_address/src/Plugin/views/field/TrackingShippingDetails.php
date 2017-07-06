<?php

namespace Drupal\cypress_custom_address\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\commerce_shipping\Entity\Shipment;
use Drupal\commerce_order\Entity\OrderItem;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("field_tracking_shipping_details")
 */
class TrackingShippingDetails extends FieldPluginBase {

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
    $shipment_id = $values->_entity->id();
    $ship_items = "";
    $product_image_service = \Drupal::service('cypress_checkout_flow.default');
    if (!empty($shipment_id)) {
      $ship_obj = Shipment::load($shipment_id);
      if (!empty($ship_obj)) {
        $ship_items = '<summary>Items</summary><ol>';
        foreach ($ship_obj->getItems() as $item) {
          $order_item_id = $item->getOrderItemId();
          $order_item = OrderItem::load($order_item_id);
          $product_image = $product_image_service->getOrderItemImage($order_item);
          $ship_items .= '<li>' . '<div class="product-details-tracking">
             <img src="' . $product_image . '" width="100px" height="100px"/>  ' .
            '<div class="product-title">' . $item->getTitle() . '</div></div>' .
            '<br></li>';
        }
        $ship_items .= '</ol>';
      }
    }
    return check_markup($ship_items, 'full_html');
  }

}
