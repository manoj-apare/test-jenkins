<?php

namespace Drupal\store_mysamples\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\yamlform\Entity\YamlFormSubmission;

/**
 * Provides a 'List of promocode to approve.' Block.
 *
 * @Block(
 *   id = "promocode_to_approve",
 *   admin_label = @Translation("List of promocode to approve"),
 * )
 */
class ApproversPendingListBlock extends BlockBase {

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
      'requester_mail' => t('Requester'),
      'promocode_status' => t('Status/Action'),
    ];
    $results = $this->activePromocode();
    $requesters = array();
    if (is_array($results)) {
      foreach ($results as $result) {
        $yamlformupdate = YamlFormSubmission::load($result);

        if ($yamlformupdate->getData('promo_code') != '') {
          $requesters[$result]['promo_code'] = $yamlformupdate->getData('promo_code');
          $requesters[$result]['part_number'] = $yamlformupdate->getData('part_number');
          $requesters[$result]['discount_amount'] = '$' . $yamlformupdate->getData('discount_amount');
          $requesters[$result]['quantity'] = $yamlformupdate->getData('quantity');
          $cdate = is_numeric($yamlformupdate->getData('created_date')) ? $yamlformupdate->getData('created_date') : strtotime($yamlformupdate->getData('created_date'));
          $created_date = ($cdate == 0) ? '' : date('m-d-y', $cdate);
          $edate = is_numeric($yamlformupdate->getData('expire_date')) ? $yamlformupdate->getData('expire_date') : strtotime($yamlformupdate->getData('expire_date'));
          $expire_date = ($cdate == 0) ? '' : date('m-d-y', $edate);

          $requesters[$result]['created_date'] = $created_date;
          $requesters[$result]['expire_date'] = $expire_date;
          $requesters[$result]['customer'] = $yamlformupdate->getData('customer');
          $requesters[$result]['end_user'] = $yamlformupdate->getData('end_user');
          $requesters[$result]['project_name'] = $yamlformupdate->getData('project_name');
          $requesters[$result]['application'] = $yamlformupdate->getData('application');
          $approverMail = str_replace(',', '<br/>', $this->getName($yamlformupdate->getData('requester_mail')));
          $renderMail = \Drupal\Core\Render\Markup::create($approverMail);
          $requesters[$result]['requester_mail'] = $renderMail;
          $status = $yamlformupdate->getData('promocode_status');
          if ($status == 'Rejected' || $status == 'Approved') {
            $requesters[$result]['promocode_status'] = $status;
          } else {
            $modifyURL = '/admin/structure/yamlform/manage/promocode/submission/' . $result . '/edit?op=modify';
            $url = '/yamlform/submission/' . $result . '/';
            $strLinks = '<a href="' . $url . 'approve" style="font-color: #0071b8; cursor: pointer;  cursor: pointer;" target="_blank">Approve</a><br/>';
            $strLinks .= '<a href="' . $url . 'reject" style="font-color: #0071b8; cursor: pointer;" target="_blank">Reject</a><br/>';
            $strLinks .= '<a href="' . $modifyURL . '" style="font-color: #0071b8; cursor: pointer;cursor: pointer;" target="_blank">Modify</a>';
            $requesters[$result]['promocode_status'] = \Drupal\Core\Render\Markup::create($strLinks);
          }
        }
      }
    }
    // Generate the table.
    $table = array(
      '#type' => 'table',
      '#header' => $header,
      '#rows' => $requesters,
    );
    $markup = render($table);


    return array(
      '#type' => 'markup',
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
  public function activePromocode() {
    $userCurrent = \Drupal::currentUser();
    $user = \Drupal\user\Entity\User::load($userCurrent->id());
    $mail = $user->getEmail();
    $query = \Drupal::database()->select('yamlform_submission', 'pc');
    $query->fields('pc', ['sid']);
    $query->join('yamlform_submission_data', 'pcdata', 'pcdata.sid=pc.sid');
    $query->condition('pc.completed', strtotime('-2 day'), '>=');
    $query->condition('pc.yamlform_id', 'promocode');
    $query->condition('pc.in_draft', 0);
    $query->condition('pcdata.name', 'approver_mail');
    $query->condition('pcdata.value', '%' . $mail . '%', 'LIKE');
    $query->orderBy('pc.completed', 'DESC');

    $results = $query->execute()->fetchAll();

    $sids = '';
    foreach ($results as $result) {
      $sids[] = $result->sid;
    }

    return $sids;
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
      $requester[] = '<a href=\'mailto:' . $result->mail . '\' title="' . $name . '">' . $wrapname . '</a>';
    }

    $requester_mail =implode(',', $requester);
    return (!empty($requester_mail)) ? $requester_mail : '';
  }

}
