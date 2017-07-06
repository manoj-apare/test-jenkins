<?php

namespace Drupal\commerce_promotion\Plugin\Commerce\PromotionOffer;

use Drupal\Core\Form\FormStateInterface;

/**
 * Provides the base class for percentage off offers.
 */
abstract class PercentageQuantityOffBase extends PromotionOfferBase {

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'amount' => 0,
      'quantity' => 0,
    ] + parent::defaultConfiguration();
  }

  /**
   * Gets the percentage amount, as a decimal, negated.
   *
   * @return string
   *   The amount.
   */
  public function getAmount() {
    return (string) $this->configuration['amount'];
  }

  public function getCouponQuantity() {
    return (string) $this->configuration['quantity'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    $form += parent::buildConfigurationForm($form, $form_state);

    $form['amount'] = [
      '#type' => 'commerce_number',
      '#title' => $this->t('Percentage'),
      '#default_value' => $this->configuration['amount'] * 100,
      '#maxlength' => 255,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 100,
      '#size' => 4,
      '#field_suffix' => $this->t('%'),
    ];

    $form['quantity'] = [
      '#type' => 'commerce_number',
      '#title' => $this->t('Quantity'),
      '#default_value' => $this->configuration['quantity'],
      '#maxlength' => 255,
      '#required' => TRUE,
      '#min' => 0,
      '#max' => 100,
      '#size' => 4,
     // '#field_suffix' => $this->t(''),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValue($form['#parents']);
    if (empty($values['amount'])) {
      $form_state->setError($form, $this->t('Percentage amount cannot be empty.'));
    }
    if (empty($values['quantity'])) {
      $form_state->setError($form, $this->t('Quantity cannot be empty'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    parent::submitConfigurationForm($form, $form_state);

    $values = $form_state->getValue($form['#parents']);
    $this->configuration['amount'] = (string) ($values['amount'] / 100);
    $this->configuration['quantity'] = (string) ($values['quantity']);
    parent::submitConfigurationForm($form, $form_state);
  }

}
