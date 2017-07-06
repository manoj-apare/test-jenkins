<?php

namespace Drupal\cypress_custom_address\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\commerce_order\Entity\Order;
use Drupal\commerce_product\Entity\ProductVariation;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("field_list_order_items")
 */
class ListOrderItems extends FieldPluginBase {

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

    $order_id = $values->_entity->id();
    if (!empty($order_id)) {
      $order_obj = Order::load($order_id);
      $items = $order_obj->getItems();
      $product_image_service = \Drupal::service('cypress_checkout_flow.default');
      $pro_title = '<ol>';
      foreach ($items as $order_item) {
        $prod_var_id = $order_item->get('purchased_entity')->target_id;
        $product_var = ProductVariation::load($prod_var_id);
        if (!empty($product_var)) {
          $product_image = $product_image_service->getOrderItemImage($order_item);
          $pro_title .= '<li>' . '<img src="' . $product_image . '" width="100px" height="100px"/>' .
            '<div class="product-title">' . $product_var->getTitle() . '</div>' .
          '</li>';
        }
      }
      $pro_title .= '</ol>';
    }
    return check_markup($pro_title, 'full_html');
  }

}
