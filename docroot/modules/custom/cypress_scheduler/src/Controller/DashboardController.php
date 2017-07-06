<?php

namespace Drupal\cypress_scheduler\Controller;

use Drupal\Core\Controller\ControllerBase;

/**
 * Class DashboardController.
 *
 * @package Drupal\cypress_scheduler\Controller
 */
class DashboardController extends ControllerBase {

  /**
   * Hello.
   *
   * @return string
   *   Return Hello string.
   */
  public function hello($name) {
    return [
      '#type' => 'markup',
      '#markup' => $this->t('Implement method: hello with parameter(s): $name'),
    ];
  }

  /**
   * Form Rendering.
   *
   * @return array
   *    Return Form
   */
  public function cypressDashboardForms() {
    $build = [];
    $build['orderSubmit'] = \Drupal::formBuilder()->getForm('Drupal\cypress_scheduler\Form\DashboardOrderSubmitForm');
    return $build;
  }

}
