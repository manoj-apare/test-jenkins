<?php

namespace Drupal\commerce_promotion\Plugin\Field\FieldWidget;

use Drupal\commerce_promotion\PromocodeQuantityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Plugin implementation of 'commerce_promocode_quantity'.
 *
 * @FieldWidget(
 *   id = "commerce_promocode_quantity",
 *   label = @Translation("Promocode Quantity"),
 *   field_types = {
 *     "integer"
 *   }
 * )
 */
class PromocodeQuantityWidget extends WidgetBase implements ContainerFactoryPluginInterface {

  /**
   * The promotion usage.
   *
   * @var \Drupal\commerce_promotion\PromocodeQuantityInterface
   */
  protected $quantity;

  /**
   * Constructs a new UsageLimitWidget object.
   *
   * @param string $plugin_id
   *   The plugin_id for the widget.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\Core\Field\FieldDefinitionInterface $field_definition
   *   The definition of the field to which the widget is associated.
   * @param array $settings
   *   The widget settings.
   * @param array $third_party_settings
   *   Any third party settings.
   * @param \Drupal\commerce_promotion\PromocodeQuantityInterface $quantity
   *   The promotion usage.
   */
  public function __construct($plugin_id, $plugin_definition, FieldDefinitionInterface $field_definition, array $settings, array $third_party_settings, PromocodeQuantityInterface $quantity) {
    parent::__construct($plugin_id, $plugin_definition, $field_definition, $settings, $third_party_settings);

    $this->quantity = $quantity;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $plugin_id,
      $plugin_definition,
      $configuration['field_definition'],
      $configuration['settings'],
      $configuration['third_party_settings'],
      $container->get('commerce_promotion.promocode_quantity')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $field_name = $this->fieldDefinition->getName();
    /** @var \Drupal\commerce_promotion\Entity\PromotionInterface $promotion */
    $promotion = $items[$delta]->getEntity();
    $value = isset($items[$delta]->value) ? $items[$delta]->value : NULL;
    $quantity = $this->formatPlural($this->quantity->getUsage($promotion), '1 use', '@count uses');

    $element['quantity'] = [
      '#type' => 'radios',
      '#title' => $this->t('Total available'),
      '#options' => [
        0 => $this->t('Unlimited'),
        1 => $this->t('Limited number of quantity'),
      ],
      '#default_value' => $value ? 1 : 0,
    ];
    $element['promocode_quantity_limit'] = [
      '#type' => 'number',
      '#title' => $this->t('Number of quantity'),
      '#title_display' => 'invisible',
      '#default_value' => $value ?: 10,
      '#description' => $this->t('Current quantity: @usage.', ['@usage' => $quantity]),
      '#states' => [
        'invisible' => [
          ':input[name="' . $field_name . '[0][quantity]"]' => ['value' => 0],
        ],
      ],
    ];

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $new_values = [];
    foreach ($values as $key => $value) {
      if (empty($value['quantity'])) {
        continue;
      }
      $new_values[$key] = $value['promocode_quantity_limit'];
    }
    return $new_values;
  }

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(FieldDefinitionInterface $field_definition) {
    $entity_type = $field_definition->getTargetEntityTypeId();
    $field_name = $field_definition->getName();
    return $entity_type == 'commerce_promotion' && $field_name == 'promocode_quantity_limit';
  }

}
