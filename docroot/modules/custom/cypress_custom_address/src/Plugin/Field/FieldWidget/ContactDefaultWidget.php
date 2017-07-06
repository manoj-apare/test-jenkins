<?php

namespace Drupal\cypress_custom_address\Plugin\Field\FieldWidget;

use Drupal\address\Plugin\Field\FieldWidget\AddressDefaultWidget;
use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use \libphonenumber\PhoneNumberUtil;
use \libphonenumber\NumberParseException;

/**
 * Class ContactDefaultWidget.
 *
 * @FieldWidget(
 *   id = "contact_default",
 *   label = @Translation("Address With Telephone"),
 *   description = @Translation("An contact text field with an associated Address."),
 *   field_types = {
 *     "contact_address_item"
 *   }
 * )
 */
class ContactDefaultWidget extends AddressDefaultWidget {

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    $widget = parent::formElement($items, $delta, $element, $form, $form_state);
    $widget['address']['#type'] = 'contact_address_item';
    $widget['address']['locality'] = [
      '#weight' => 1,
    ];
    $widget['address']['contact'] = [
      '#title' => $this->t('Telephone'),
      '#type' => 'textfield',
      '#required' => TRUE,
      '#default_value' => isset($items[$delta]->contact) ? $items[$delta]->contact : NULL,
      '#weight' => 10,
      '#states' => [
        'invisible' => [
          ':input[name="payment_information[billing_information][field_contact_address][0][address][country_code]"]' => ['value' => ''],
        ],
      ],
      '#element_validate' => [
         array($this, 'contactValidate'),
       ],
    ];
    return $widget;
  }

  /**
   * {@inheritdoc}
   */
  public function contactValidate($element, FormStateInterface $form_state, $form) {
    $form_values = $form_state->getValues();
    $array_parents = $element['#parents'];
    if (in_array('payment_information', $array_parents, TRUE)) {
      if ($form_values['payment_information']['billing_information']['reuse_profile'] == 1) {
        return;
      }
      elseif (in_array('add_payment_method', $array_parents, TRUE)
        && $form_values['payment_information']['add_payment_method']['billing_information']['reuse_profile'] == 1) {
        return;
      }
    }
    $element_name = implode('][', $array_parents);
    array_pop($array_parents);
    $telephone = NestedArray::getValue(
      $form_values,
      array_merge($array_parents, ['contact'])
    );
    $country_code = NestedArray::getValue(
      $form_values,
      array_merge($array_parents, ['country_code'])
    );
    $phone_util = PhoneNumberUtil::getInstance();
    try {
      $phone_util_number = $phone_util->parse($telephone, $country_code);
      $is_possible = $phone_util->isPossibleNumber($phone_util_number);
      if (!$is_possible) {
        $form_state->setErrorByName($element_name, 'Please enter valid phone number.');
      }
    }
    catch (NumberParseException $e) {
      $form_state->setErrorByName($element_name, 'Please enter valid phone number.');
    }
  }

}
