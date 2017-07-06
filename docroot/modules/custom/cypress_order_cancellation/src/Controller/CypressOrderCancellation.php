<?php

namespace Drupal\cypress_order_cancellation\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class CypressOrderCancellation.
 *
 * @package Drupal\cypress_order_cancellation\Controller
 */
class CypressOrderCancellation extends ControllerBase {

  /**
   * Method to render the order cancellation form.
   *
   * @param int $profile_id
   *    Order id of the order to be cancelled.
   *
   * @return array
   *    Return the order cancellation form.
   */
  public function content($profile_id) {
    $build = [];
    $build['orderSubmit'] = \Drupal::formBuilder()->getForm('Drupal\cypress_order_cancellation\Form\OrderCancellationForm', $profile_id);
    return $build;
  }

}
