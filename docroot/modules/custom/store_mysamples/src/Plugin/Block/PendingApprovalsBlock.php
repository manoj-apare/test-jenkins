<?php

namespace Drupal\store_mysamples\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\yamlform\Entity\YamlFormSubmission;

/**
 * Provides a 'List of pending approval records' Block.
 *
 * @Block(
 *   id = "pending_approval_list",
 *   admin_label = @Translation("List of pending approval records"),
 * )
 */
class PendingApprovalsBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $mail = $user->getEmail();
    $header = [
      'promo_code' => t('Promo Code'),
      'part_number' => t('MPN'),
      'discount_amount' => t('Discount Amount'),
      'quantity' => t('Quantity'),
      'created_date' => t('Date Added'),
      'expire_date' => t('Expiration Date'),
      'customer' => t('Customer'),
      'end_user' => t('End User'),
      'project_name' => t('Project Name'),
      'application' => t('Application'),
      'approver_mail' => t('Approver'),
      'promocode_status' => t('Status/Action'),
    ];


    $query = \Drupal::database()->select('yamlform_submission', 'pc');
    $query->fields('pc', ['sid']);
    $query->condition('pc.completed', strtotime('-8 day'), '>=');
    $query->condition('pc.yamlform_id', 'promocode');
    $query->condition('pc.in_draft', 0);
    $query->orderBy('pc.completed', 'DESC');
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $yamlformupdate = YamlFormSubmission::load($result->sid);

      if( $yamlformupdate->getData('promo_code') != '') {
        $approvers[$result->sid]['promo_code'] = $yamlformupdate->getData('promo_code');
        $approvers[$result->sid]['part_number'] = $yamlformupdate->getData('part_number');
        $approvers[$result->sid]['discount_amount'] = $yamlformupdate->getData('discount_amount');
        $approvers[$result->sid]['quantity'] = $yamlformupdate->getData('quantity');
        $cdate = is_numeric($yamlformupdate->getData('created_date')) ? $yamlformupdate->getData('created_date') : strtotime($yamlformupdate->getData('created_date'));
        $created_date = ($cdate == 0) ? '' : date('m-d-y', $cdate);
        $edate = is_numeric($yamlformupdate->getData('expire_date')) ? $yamlformupdate->getData('expire_date') : strtotime($yamlformupdate->getData('expire_date'));
        $expire_date = ($cdate == 0) ? '' : date('m-d-y', $edate);

        $approvers[$result->sid]['created_date'] = $created_date;
        $approvers[$result->sid]['expire_date'] = $expire_date;
        $approvers[$result->sid]['customer'] = $yamlformupdate->getData('customer');
        $approvers[$result->sid]['end_user'] = $yamlformupdate->getData('end_user');
        $approvers[$result->sid]['project_name'] = $yamlformupdate->getData('project_name');
        $approvers[$result->sid]['application'] = $yamlformupdate->getData('application');
        $approverMail = str_replace(',', '<br/>', $this->getName($yamlformupdate->getData('approver_mail')));
        $renderMail = \Drupal\Core\Render\Markup::create($approverMail);
        $approvers[$result->sid]['approver_mail'] = $renderMail;
        $approvers[$result->sid]['promocode_status'] = $yamlformupdate->getData('promocode_status');
      }
    }

    // Generate the table.
    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $approvers,
    );
    $markup = render($table);


    return array(
      '#type' => 'markup',
      '#prefix' => '<div class="requested-promocode">' . t('Below is a list of all your promo codes and their status.') . '</div>',
      '#markup' => $markup,
      '#title' => t('List of pending promo code requests.'),
      '#cache' => array(
        'max-age' => 0,
      ),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getName($email) {
    $mail = explode(',', $email);

    $query = \Drupal::database()->select('users_field_data', 'u');
    $query->fields('u', ['mail']);
    $query->leftjoin('user__field_first_name', 'fn', 'u.uid = fn.entity_id');
    $query->fields('fn', ['field_first_name_value']);
    $query->leftJoin('user__field_last_name', 'ln', 'u.uid = ln.entity_id');
    $query->fields('ln', ['field_last_name_value']);
    $query->condition('u.mail', $mail, 'in');
    $results = $query->execute()->fetchAll();

    foreach($results as $result) {
      $name = $result->field_first_name_value . ' ' . $result->field_last_name_value;

      // If name is not imported, use email.
      if (empty($result->field_first_name_value) && empty($result->field_last_name_value)) {
        $name = $result->mail;
      }
      $wrapname = strlen($name) > 13 ? substr($name, 0, 10) . '...' : $name;
      $approver[] = '<a href=\'mailto:' . $result->mail . '\' title="' . $name . '">' . $wrapname . '</a>';
    }

    $approver_mail =implode(',', $approver);
    return (!empty($approver_mail)) ? $approver_mail : '';
  }

}
