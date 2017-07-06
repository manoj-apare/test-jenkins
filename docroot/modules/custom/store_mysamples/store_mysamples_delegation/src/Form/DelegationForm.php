<?php

namespace Drupal\store_mysamples_delegation\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * DelegationForm Class Doc Comment.
 *
 * @category Class
 * @package delegationForm
 * @author frnd
 */
class DelegationForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'approver_delegation_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();
    $query = \Drupal::database()->select('user__field_legacy_uid', 'ufl');
    $query->fields('ufl', ['field_legacy_uid_value']);
    $query->join('users_field_data', 'ufd', 'ufd.uid=ufl.entity_id');
    // 321021 (bask@cypress.com)
    $query->condition('ufd.uid', $user->id());
    $result = $query->execute()->fetch();

    // Form fields.
    $form['delegation_form'] = array(
      '#type' => 'fieldset',
      '#title' => $this->t('Please fill the form to assign a delegate while on leave.'),
      '#suffix' => '<div class="delegate-info"><i>' . $this->content($result->field_legacy_uid_value) . '<i></div>',
    );
    $form['delegation_form']['approver_name'] = array(
      '#type' => 'textfield',
      '#title' => t('Approver Initial:'),
      '#required' => TRUE,
    );
    $form['delegation_form']['approver_from_date'] = array(
      '#type' => 'date',
      '#title' => t('Start Date:'),
      '#required' => TRUE,
    );
    $form['delegation_form']['approver_to_date'] = array(
      '#type' => 'date',
      '#title' => t('End Date:'),
      '#required' => TRUE,
    );
    $form['delegation_form']['actions']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Save'),
    );
    $form['delegation_form']['legacy_uid'] = array(
      '#type' => 'hidden',
      '#required' => TRUE,
      '#value' => $result->field_legacy_uid_value,
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    $user = \Drupal::currentUser();

    $name = $form_state->getValue('approver_name');
    $fromDate = strtotime($form_state->getValue('approver_from_date'));
    $toDate = strtotime($form_state->getValue('approver_to_date'));

    // Query to fetch if the name is in the user table.
    $query = \Drupal::database()->select('users_field_data', 'ufd');
    $query->fields('ufd', ['uid', 'name', 'mail']);
    $query->fields('ufl', ['field_legacy_uid_value']);
    $query->join('user__field_legacy_uid', 'ufl', 'ufd.uid=ufl.entity_id');
    $query->condition('ufd.mail', $name . '@%', 'LIKE');
    $delegatedUser = $query->execute()->fetchAll();

    if (!empty($delegatedUser)) {
      foreach ($delegatedUser as $user) {
        $form_state->setValue('entity_id', $user->uid);
        $form_state->setValue('temp_approver_mail', $user->mail);
        $form_state->setValue('delegate_legacy_id', $user->field_legacy_uid_value);
      }
    }

    // Show error if approver name is invalid.
    if (empty($delegatedUser)) {
      $form_state->setErrorByName('approver_name', $this->t('User is invalid'));
    }
    if ($user->name == $name) {
      $form_state->setErrorByName('approver_name', $this->t('You cannot assign delegate to yourself.'));
    }
    // Show error if to date is less than from date.
    if ($fromDate > $toDate) {
      $form_state->setErrorByName('approver_from_date', $this->t('From date cannot be greater than To date'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Input parameters.
    $startDate = $form_state->getValue('approver_from_date');
    $startDateTimestamp = strtotime($startDate);
    $endDate = $form_state->getValue('approver_to_date');
    $endDateTimestamp = strtotime($endDate);

    // Query to check record already exist for the approver.
    $query = \Drupal::database()->select('delegation_info', 'del');
    $query->fields('del', array('did'));
    $query->condition('del.legacy_id', $form_state->getValue('legacy_uid'));
    $result = $query->execute()->fetchAll();

    if (empty($form_state->getValue('legacy_uid'))) {
      $message = t('Sorry, you are not an authorized approver.');
      drupal_set_message($message, 'error');
    }
    else {
      // If no records for an approver, insert else update.
      if (empty($result)) {
        $insert = \Drupal::database()->insert('delegation_info');
        $insert->fields([
          'legacy_id',
          'delegate_legacy_id',
          'entity_id',
          'temp_approver_mail',
          'start_date',
          'end_date',
        ]);
        $insert->values([
          $form_state->getValue('legacy_uid'),
          $form_state->getValue('delegate_legacy_id'),
          $form_state->getValue('entity_id'),
          $form_state->getValue('temp_approver_mail'),
          $startDateTimestamp,
          $endDateTimestamp,
        ]);
        $insert->execute();
      }
      else {
        $update = \Drupal::database()->update('delegation_info');
        $update->fields([
          'legacy_id' => $form_state->getValue('legacy_uid'),
          'entity_id' => $form_state->getValue('entity_id'),
          'delegate_legacy_id' => $form_state->getValue('delegate_legacy_id'),
          'temp_approver_mail' => $form_state->getValue('temp_approver_mail'),
          'start_date' => $startDateTimestamp,
          'end_date' => $endDateTimestamp,
        ]);
        $update->execute();
      }
      $message = t('The dates have been updated successfully.');
      drupal_set_message($message);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function content($approverLegacyID) {
    $query = \Drupal::database()->select('delegation_info', 'del');
    $query->fields('del', ['start_date', 'end_date']);
    $query->join('users_field_data', 'ufd', 'ufd.uid=del.entity_id');
    $query->fields('ufd', ['name']);
    $query->condition('del.legacy_id', $approverLegacyID);
    $results = $query->execute()->fetchAll();

    foreach ($results as $result) {
      $delegate['name'] = $result->name;
      $delegate['start_date'] = $result->start_date;
      $delegate['end_date'] = $result->end_date;
    }

    if (empty($delegate['name'])) {
      $str = '';
    }
    else {
      $str = t('You have assigned <b> @name </b> the authority to approve promocode request made between @startDate and @endDate on your behalf.', array(
        '@name' => $delegate['name'],
        '@startDate' => date('m-d-Y', $delegate['start_date']),
        '@endDate' => date('m-d-Y', $delegate['end_date']),
      ));
    }

    return $str;
  }

}
