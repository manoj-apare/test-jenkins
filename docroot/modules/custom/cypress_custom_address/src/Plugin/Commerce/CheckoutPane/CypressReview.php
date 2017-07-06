<?php

namespace Drupal\cypress_custom_address\Plugin\Commerce\CheckoutPane;

use Drupal\Core\Form\FormStateInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface;
use Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneBase;
use Drupal\Core\Url;
use Drupal\cypress_checkout_flow\Plugin\Commerce\CheckoutPane\CypressShippingInformation;

/**
 * Provides the Cypress Review pane.
 *
 * @CommerceCheckoutPane(
 *   id = "cypress_review",
 *   label = @Translation("Cypress Review"),
 *   default_step = "order_information",
 * )
 */
class CypressReview extends CheckoutPaneBase implements CheckoutPaneInterface {

  /**
   * {@inheritdoc}
   */
  public function buildPaneForm(array $pane_form, FormStateInterface $form_state, array &$complete_form) {
    /** @var \Drupal\commerce_checkout\Plugin\Commerce\CheckoutPane\CheckoutPaneInterface[] $enabled_panes */
    $enabled_panes = array_filter($this->checkoutFlow->getPanes(), function ($pane) {
      return !in_array($pane->getStepId(), ['_sidebar', '_disabled']);
    });
    foreach ($enabled_panes as $pane_id => $pane) {
      if ($pane instanceof CypressShippingInformation) {
        if ($summary = $pane->buildPaneSummary()['shipping_profile']) {
          // BC layer for panes which still return rendered strings.
          if ($summary && !is_array($summary)) {
            $summary = [
              '#markup' => $summary,
            ];
          }
          $order_id = $this->order->id();
          // Encrypt the order id.
          $encrypted_order_id = \Drupal::service('cypress_checkout_flow.encrypt_decrypt')->customEncryptDecrypt($order_id, 'e');
          $step_id = $this->order->checkout_step->value;
          $previous_step_id = $this->checkoutFlow->getPreviousStepId($step_id);
          $previous_step_url = Url::fromRoute('commerce_checkout.form',
            [
              'commerce_order' => $encrypted_order_id,
              'step' => $previous_step_id,
            ]
          )->toString();
          $edit_link = $summary['#profile']
            ->toUrl('edit-form',
                  [
                    'query' =>
                          [
                            'destination' => '/checkout/' . $encrypted_order_id . '/' . $step_id,
                          ],
                  ]
              )
            ->toString();
          $pane_form[$pane_id] = [
            '#type' => 'fieldset',
            '#title' => 'Shipping To',
            'summary' => $summary,
            'actions' => [
              '#markup' => '<div class="shipping_to_actions">
                <a href="' . $edit_link . '" class="edit_shipping_to btn btn-primary">Edit</a>
                <a href="' . $previous_step_url . '" class="change_shipping_to btn btn-primary">Change</a>
              </div>',
            ],
            '#prefix' => '<div class="selected_shipping_address">',
            '#suffix' => '</div>',
          ];
        }
        break;
      }
    }

    return $pane_form;
  }

}
