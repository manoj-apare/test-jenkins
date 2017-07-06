<?php

namespace Drupal\cypress_checkout_flow\Plugin\Commerce\CheckoutPane;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface;
use Drupal\commerce_order\Adjustment;
use Drupal\commerce_order\Entity\OrderItem;
use Drupal\commerce_shipping\OrderShipmentSummaryInterface;
use Drupal\Core\Entity\Entity\EntityFormDisplay;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\commerce_shipping\PackerManagerInterface;

/**
 * Provides the shipment method pane.
 *
 * Collects information for each shipment.
 * Assumes that all shipments share the same shipping profile.
 *
 * @CommerceCheckoutPane(
 *   id = "cypress_shipment_method",
 *   label = @Translation("Cypress Shipment method"),
 * )
 */
class CypressShipmentMethod extends CheckoutPaneBase implements
    ContainerFactoryPluginInterface {

  /**
   * The packer manager.
   *
   * @var \Drupal\commerce_shipping\PackerManagerInterface
   */
  protected $packerManager;

  /**
   * The order shipment summary.
   *
   * @var \Drupal\commerce_shipping\OrderShipmentSummaryInterface
   */
  protected $orderShipmentSummary;

  /**
   * Constructs a new ShippingInformation object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowInterface $checkout_flow
   *   The parent checkout flow.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\commerce_shipping\OrderShipmentSummaryInterface $order_shipment_summary
   *   The order shipment summary.
   * @param \Drupal\commerce_shipping\PackerManagerInterface $packer_manager
   *   The packer manager.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow, EntityTypeManagerInterface $entity_type_manager, OrderShipmentSummaryInterface $order_shipment_summary, PackerManagerInterface $packer_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $checkout_flow, $entity_type_manager);

    $this->packerManager = $packer_manager;
    $this->orderShipmentSummary = $order_shipment_summary;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition, CheckoutFlowInterface $checkout_flow = NULL) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $checkout_flow,
      $container->get('entity_type.manager'),
      $container->get('commerce_shipping.order_shipment_summary'),
      $container->get('commerce_shipping.packer_manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return parent::defaultConfiguration();
  }

  /**
   * {@inheritdoc}
   */
  public function isVisible() {
    if (!$this->order->hasField('shipments')) {
      return FALSE;
    }

    // The order must contain at least one shippable purchasable entity.
    foreach ($this->order->getItems() as $order_item) {
      $purchased_entity = $order_item->getPurchasedEntity();
      if ($purchased_entity && $purchased_entity->hasField('weight')) {
        return TRUE;
      }
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneSummary() {
    $summary = [];
    if ($this->isVisible()) {
      $summary = $this->orderShipmentSummary->build($this->order);
    }
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    $shipments = $this->order->shipments->referencedEntities();
    foreach ($this->order->shipments->referencedEntities() as $shipment) {
      $shipping_profile = $shipment->getShippingProfile();
      $form_state->set('shipping_profile', $shipping_profile);
      break;
    }
    $recalculate_shipping = $form_state->get('recalculate_shipping');
    $force_packing = empty($shipments) && empty($this->configuration['require_shipping_profile']);
    if ($recalculate_shipping || $force_packing) {
      list($shipments, $removed_shipments) = $this->packerManager->packToShipments($this->order, $shipping_profile, $shipments);

      // Store the IDs of removed shipments for submitPaneForm().
      $pane_form['removed_shipments']['#value'] = array_map(function ($shipment) {
        /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment */
        return $shipment->id();
      }, $removed_shipments);
    }

    $pane_form['shipments'] = [
      '#type' => 'container',
      '#prefix' => '<h3 class="col-sm-8 order-summary-header">
          Order Summary
        </h3>
        <a href="http://www.cypress.com/cypress-store" class="pull-right col-sm-4 order-summary-to-shopping">
          Continue Shopping >>
        </a>',
    ];
    /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment */
    foreach ($shipments as $index => $shipment) {
      $pane_form['shipments'][$index] = [
        '#parents' => array_merge($pane_form['#parents'], ['shipments', $index]),
        '#array_parents' => array_merge($pane_form['#parents'], ['shipments', $index]),
        '#type' => 'container',
      ];
      $shipment_order_items = $shipment->getItems();
      $shipment_order_items_markup = '<div class="shipment_order_items_wrapper">';
      $number_formatter = \Drupal::service('commerce_price.number_formatter_factory')
        ->createInstance();
      $currency_storage = \Drupal::entityTypeManager()->getStorage('commerce_currency');
      foreach ($shipment_order_items as $shipment_order_item) {
        $order_item = OrderItem::load($shipment_order_item->getOrderItemId());
        $price = $order_item->getTotalPrice()->getNumber();
        $currency = $order_item->getTotalPrice()->getCurrencyCode();
        $currency = $currency_storage->load($currency);
        $image_url = \Drupal::service('cypress_checkout_flow.default')
          ->getOrderItemImage($order_item);
        $shipment_order_items_markup .= '<div class="shipment_order_item col-sm-8 col-xs-12">
          <div class="shipment_order_item_quantity col-sm-2 col-xs-7">' .
            (int) $order_item->getQuantity() . 'x
          </div>
          <div class="shipment_order_item_image col-sm-4 col-xs-5" width="100px" height="100px">
            <img src="' . $image_url . '">
          </div>
          <div class="shipment_order_item_name col-sm-4 col-xs-8">' .
            $order_item->getTitle() . '
          </div>
          <div class="shipment_item_total_price col-sm-2 col-xs-4">' .
          $number_formatter->formatCurrency(round($price, 2), $currency) . '
          </div>
         </div>';
      }
      $shipment_order_items_markup .= '</div>';
      $pane_form['shipments'][$index]['order_items'] = [
        '#markup' => $shipment_order_items_markup,
      ];
      $form_display = EntityFormDisplay::collectRenderDisplay($shipment, 'default');
      $form_display->removeComponent('shipping_profile');
      $form_display->removeComponent('title');
      $form_display->buildForm($shipment, $pane_form['shipments'][$index], $form_state);
      $pane_form['shipments'][$index]['#shipment'] = $shipment;
      $pane_form['shipments'][$index]['#attributes'] = ['class' => ['clearfix']];
      // $shipping_method_storage = $this->entityTypeManager->getStorage('commerce_shipping_method');
      // $shipping_methods = $shipping_method_storage->loadMultipleForShipment($shipment);
      // $first_shipping_method = reset($shipping_methods);
      // $shipment->setShippingMethodId($first_shipping_method->id())
      //   ->setShippingService('default');
      // $shipment->save();
      // $order_adjustments = $this->order->getAdjustments();
      // if (empty($order_adjustments)) {
      //   $shipping_adjustment = new Adjustment([
      //     'type' => 'shipping',
      //     'label' => 'Shipping'
      //   ]);
      // }
      // else {
      //   foreach($order_adjustments as $order_adjustment) {
      //
      //   }
      // }
    }

    $pane_form['order_total_summary'] = [
      '#theme' => 'commerce_order_total_summary',
      '#totals' => \Drupal::service('commerce_order.order_total_summary')
        ->buildTotals($this->order),
      '#prefix' => '<div class="clearfix"><div class="col-sm-4 col-xs-12 pull-right">',
      '#suffix' => '</div></div>',
    ];
    $pane_form['#attached']['library'][] = 'cypress_checkout_flow/cypress_checkout_flow.shipping_rate';

    return $pane_form;
  }

  /**
   * {@inheritdoc}
   */
  public function validatePaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitPaneForm(array &$pane_form, FormStateInterface $form_state, array &$complete_form) {
    // Save the modified shipments.
    $shipments = [];
    $shipping_profile = $form_state->get('shipping_profile');
    foreach (Element::children($pane_form['shipments']) as $index) {
      /** @var \Drupal\commerce_shipping\Entity\ShipmentInterface $shipment */
      $shipment = clone $pane_form['shipments'][$index]['#shipment'];
      $form_display = EntityFormDisplay::collectRenderDisplay($shipment, 'default');
      $form_display->removeComponent('shipping_profile');
      $form_display->removeComponent('title');
      $form_display->extractFormValues($shipment, $pane_form['shipments'][$index], $form_state);
      $shipment->setShippingProfile($shipping_profile);
      $shipment->save();
      $shipments[] = $shipment;
    }
    $this->order->shipments = $shipments;

    // Delete shipments that are no longer in use.
    $removed_shipment_ids = $pane_form['removed_shipments']['#value'];
    if (!empty($removed_shipment_ids)) {
      $shipment_storage = $this->entityTypeManager->getStorage('commerce_shipment');
      $removed_shipments = $shipment_storage->loadMultiple($removed_shipment_ids);
      $shipment_storage->delete($removed_shipments);
    }
  }

}
