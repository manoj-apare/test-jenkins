<?php

namespace Drupal\cypress_custom_address\Plugin\views\field;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\field\FieldPluginBase;
use Drupal\views\ResultRow;
use Drupal\commerce_shipping\Entity\Shipment;

/**
 * A handler to provide a field that is completely custom by the administrator.
 *
 * @ingroup views_field_handlers
 *
 * @ViewsField("field_shipping_information_details")
 */
class ShippingInformationDetails extends FieldPluginBase {

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
    if (!empty($shipment_id)) {
      $ship_obj = Shipment::load($shipment_id);
      // Get the vendor for each item.
      $vendor = $ship_obj->get('field_vendor')->value;
      // Get the current user role.
      $user_roles = \Drupal::currentUser()->getRoles();
      // If user role is admin then show vendor detail.
      if (in_array("administrator", $user_roles)) {
        // If not empty vendor.
        if (!empty($vendor)) {
          $ship_items = '<div class = "order-number"><div class = "order_value">Vendor:' . $vendor . '</div></div>';
        }
      }
      $shipment = $ship_obj->getData('FEDEX');
      if (!empty($shipment)) {

        $tracking = $shipment['TrackingNumber'];
        $trackingidentifier = $shipment['TrackingNumberUniqueIdentifier'];
        $statusde = $shipment['StatusDetail']['Description'];
        $statustime = $shipment['StatusDetail']['CreationTime'];
        $carriercode = $shipment['CarrierCode'];
        $service = $shipment['Service']['Description'];
        $shipmentvalue = $shipment['ShipmentWeight']['Value'];
        $shipmentunit = $shipment['ShipmentWeight']['Units'];

        if (!empty($tracking)) {
          $ship_items .= '<details>';
          $ship_items .= '<summary>Shipment Details</summary><ol>';
          $ship_items .= '<li>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Tracking Number: </div><div class="col-sm-8">' . $tracking . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Tracking Identifier: </div><div class="col-sm-8">' . $trackingidentifier . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Status: </div><div class="col-sm-8">' . $statusde . '  ' . $statustime . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Carrier Code: </div><div class="col-sm-8">' . $carriercode . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Service: </div><div class="col-sm-8">' . $service . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Shipment Weight: </div><div class="col-sm-8">' . $shipmentvalue . '  ' . $shipmentunit . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Shipper Address: </div><div class="col-sm-8">' . $shipment['ShipperAddress']['CountryName'] . ',  ' . $shipment['ShipperAddress']['City'] . '</div></div>' .
            '<div class="col-sm-12"><div class="ship-items-label col-sm-4">Delivery Address: </div><div class="col-sm-8">' . $shipment['DestinationAddress']['CountryName'] . ', ' . $shipment['DestinationAddress']['City'] . '</div></div>' .
            '</li>';
          $ship_items .= '</ol></details>';
        }
        else {
          $ship_items = "There is no Shipment Details Found.";
        }
      }
    }
    return check_markup($ship_items, 'full_html');
  }

}
