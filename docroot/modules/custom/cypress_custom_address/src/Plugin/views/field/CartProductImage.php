<?php

namespace Drupal\cypress_custom_address\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\commerce_order\Entity\OrderItem;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("field_cart_product_image")
 */
class CartProductImage extends FieldPluginBase {

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
    // To get the product image.
    $order_item_id = $values->_relationship_entities['order_items']->id();
    $order_item = OrderItem::load($order_item_id);
    $cart_image = \Drupal::service('cypress_checkout_flow.default')
      ->getOrderItemImage($order_item);

    $output = '<div class = "output no-image-placeholder"><img src ="' . $cart_image . '" height="100" width="100"></div>';
    $img = check_markup($output, 'full_html');
    return $img;

  }

}
