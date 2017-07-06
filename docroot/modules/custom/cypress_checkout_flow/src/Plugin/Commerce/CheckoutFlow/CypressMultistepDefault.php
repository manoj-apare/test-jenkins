<?php

namespace Drupal\cypress_checkout_flow\Plugin\Commerce\CheckoutFlow;

use Drupal\commerce_checkout\Plugin\Commerce\CheckoutFlow\CheckoutFlowWithPanesBase;

/**
 * Provides the default multistep checkout flow.
 *
 * @CommerceCheckoutFlow(
 *   id = "cypress_multistep_default",
 *   label = "Cypress - Multistep - Default",
 * )
 */
class CypressMultistepDefault extends CheckoutFlowWithPanesBase {

  /**
   * {@inheritdoc}
   */
  public function getSteps() {
    // Note that previous_label and next_label are not the labels
    // shown on the step itself. Instead, they are the labels shown
    // when going back to the step, or proceeding to the step.
    return [
      'address_select' => [
        'label' => $this->t('Address Select'),
        'next_label' => $this->t('Go to order info'),
        'previous_label' => $this->t('Go back'),
        'has_sidebar' => FALSE,
      ],
      'order_information' => [
        'label' => $this->t('Order information'),
        'has_sidebar' => FALSE,
        'previous_label' => $this->t('Go back'),
      ],
      'payment' => [
        'label' => $this->t('Payment'),
        'next_label' => $this->t('Pay and complete purchase'),
        'has_sidebar' => FALSE,
        'hidden' => TRUE,
      ],
      'complete' => [
        'label' => $this->t('Complete'),
        'next_label' => $this->t('Pay and complete purchase'),
        'has_sidebar' => FALSE,
      ],
    ];
  }

}
