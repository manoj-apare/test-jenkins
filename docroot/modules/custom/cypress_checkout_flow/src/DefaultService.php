<?php

namespace Drupal\cypress_checkout_flow;

use Drupal\commerce_product\Entity\Product;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\taxonomy\Entity\Term;
use Drupal\file\Entity\File;

/**
 * Class DefaultService.
 *
 * @package Drupal\cypress_checkout_flow
 */
class DefaultService {

  /**
   * Constructs a new DefaultService object.
   */
  public function __construct() {

  }

  /**
   * Helper function to get image from order item.
   */
  public function getOrderItemImage($order_item) {
    $host = \Drupal::request()->getSchemeAndHttpHost();
    $product_var_id = $order_item->get('purchased_entity')->getValue()[0]['target_id'];
    $product_var = ProductVariation::load($product_var_id);
    $product_id = $product_var->get('product_id')->getValue()[0]['target_id'];
    $product = Product::load($product_id);
    $type = $product->get('type')->getValue()[0]['target_id'];
    if ($type == 'default') {
      $product_image = $product->get('field_image')->getValue()[0]['value'];
      if (!empty($product_image)) {
        return $product_image;
      }
      else {
        return $host . '/themes/cypress_store/No_image_available.png';
      }
    }
    elseif ($type == 'part') {
      $cart_image = $host . '/themes/cypress_store/cypress_part_default.png';
      if (!empty($product->get('field_related_products'))) {
        $related_product_ids = $product->get('field_related_products');
        foreach ($related_product_ids as $key => $related_product_id) {
          $related_category = $related_product_id->target_id;
          $term = Term::load($related_category);
          if (!empty($term)) {
            $product_image_id = $term->get('field_product_term_image')->target_id;
          }
          if (!empty($product_image_id)) {
            $image = File::load($product_image_id);
            $uri = $image->getFileUri();
            $cart_image = file_create_url($uri);
          }
          elseif (empty($product_image_id) && !empty($term)) {
            $storage = \Drupal::service('entity_type.manager')
              ->getStorage('taxonomy_term');
            $parents = $storage->loadParents($term->id());
            foreach ($parents as $parent_tid => $parent_term) {
              $id = $parent_tid;
              $parent_data = Term::load($id);
              $image_id = $parent_data->get('field_product_term_image')->target_id;
              if (!empty($image_id)) {
                $image = File::load($image_id);
                $uri = $image->getFileUri();
                $cart_image = file_create_url($uri);
                break;
              }
            }
          }
        }
      }

      return $cart_image;
    }
  }

}
