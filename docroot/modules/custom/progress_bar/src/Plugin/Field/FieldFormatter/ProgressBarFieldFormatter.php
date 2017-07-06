<?php

namespace Drupal\progress_bar\Plugin\Field\FieldFormatter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'progress_bar' formatter.
 *
 * @FieldFormatter(
 *   id = "progress_bar",
 *   label = @Translation("Cypress Progress bar field formatter"),
 *   field_types = {
 *     "state"
 *   }
 * )
 */
class ProgressBarFieldFormatter extends FormatterBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      // Implement default settings.
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    return [
      // Implement settings form.
    ] + parent::settingsForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];
    // Implement settings summary.
    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = [];

    foreach ($items as $delta => $item) {
      switch ($this->viewValue($item)) {
        case 'New':
          $state_per = 10;
          break;

        case 'In progress':
          $state_per = 40;
          break;

        case 'Shipped':
          $state_per = 70;
          break;

        case 'Delivered':
          $state_per = 100;
          break;
      }
      $elements[$delta] = [
        '#theme' => 'cypress_progress_bar',
        '#state' => $state_per,
      ];
    }

    return $elements;
  }

  /**
   * Generate the output appropriate for one field item.
   *
   * @param \Drupal\Core\Field\FieldItemInterface $item
   *   One field item.
   *
   * @return string
   *   The textual output generated.
   */
  protected function viewValue(FieldItemInterface $item) {
    // The text value has no text format assigned to it, so the user input
    // should equal the output, including newlines.
    return nl2br(Html::escape($item->value));
  }

}
