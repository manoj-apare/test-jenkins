<?php

namespace Drupal\cypress_store_vendor\EventSubscriber;

use Drupal\state_machine\Event\WorkflowTransitionEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class OrderSubmitSubscriber.
 *
 * @package Drupal\cypress_store_vendor
 */
class OrderSubmitSubscriber implements EventSubscriberInterface {

  /**
   * Constructor.
   */
  public function __construct() {

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events['commerce_order.place.post_transition'] = ['submitOrderToVendor'];

    return $events;
  }

  /**
   * Called on event commerce_order.place.post_transition is dispatched.
   *
   * @param WorkflowTransitionEvent $event
   *   Order workflow transition event object.
   */
  public function submitOrderToVendor(WorkflowTransitionEvent $event) {
    $order = $event->getEntity();
    $shipments = $event->getEntity()->get('shipments')->referencedEntities();
    if (!empty($shipments)) {
      foreach ($shipments as $shipment) {
        $vendor = $shipment->get('field_vendor')->getValue()[0]['value'];
        if ($vendor == 'CML') {
          continue;
        }
        \Drupal::service('cypress_store_vendor.vendor')
          ->submitOrder($vendor, $order, $shipment);
      }
    }
  }

}
