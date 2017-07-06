<?php

namespace Drupal\store_mysamples\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\yamlform\Entity\YamlFormSubmission;

/**
 * Class PromocodeReject.
 *
 * @package Drupal\store_mysampes\Controller
 */
class PromocodeReject extends ControllerBase {

  /**
   * Method to show the Promocode Reject Status.
   *
   * @param int $yamlsubmissionid
   *   Submission id for yaml Reject Form.
   *
   * @return string
   *   Returns string
   */
  public function content($yamlsubmissionid) {
    $current_path = \Drupal::service('path.current')->getPath();
    $yaml_form_submission_entity = YamlFormSubmission::load($yamlsubmissionid);
    $data = $yaml_form_submission_entity->getData();
    $approver_subject = 'Request to Approve Promo Code';
    $requester_subject = 'Request For Promo Code Successfully Sent';
    $data['promocode_status'] = t('Rejected');
    $data['approver_mail_statement'] = 'Promo code ' . $data['promo_code'] . ' has been ' . $data['promocode_status'];
    $data['requester_mail_statement'] = 'Request for ' . $data['promo_code'] . ' has been ' . $data['promocode_status'];
    $data['approver_subject'] = '[' . $data['promocode_status'] . '] ' . $approver_subject;
    $data['requester_subject'] = '[' . $data['promocode_status'] . '] ' . $requester_subject;
    $yaml_form_submission_entity->setData($data);
    $yaml_form_submission_entity->save();

    if (preg_match('/yamlform\/submission\/\d+\/reject$/', $current_path)) {
      $param = explode('/', $current_path);
      $submission_status = $param[4];
      if (!empty($submission_status)) {
        $form['#attached']['library'][] = 'store_mysamples/promocode-submission';
      }

      if ($data['promocode_status'] == 'Rejected') {
        $yaml_form = $yaml_form_submission_entity->getYamlForm();
        $yaml_form_handlers = $yaml_form->getHandlers();
        $yaml_form_handler_ids = $yaml_form_handlers->getInstanceIds();
        foreach ($yaml_form_handler_ids as $yaml_form_handler_id) {
          if ($yaml_form_handler_id == 'email') {
            $yaml_email_handler = $yaml_form_handlers->get($yaml_form_handler_id);
            $yaml_form_submission_data = $yaml_email_handler->getMessage($yaml_form_submission_entity);
            $yaml_form_submission_data['subject'] = $data['approver_subject'];
            $yaml_email_handler->sendMessage($yaml_form_submission_data);
          }
          if ($yaml_form_handler_id == 'email_1') {
            $yaml_email_handler = $yaml_form_handlers->get($yaml_form_handler_id);
            $yaml_form_submission_data = $yaml_email_handler->getMessage($yaml_form_submission_entity);
            $yaml_form_submission_data['subject'] = $data['requester_subject'];
            $yaml_email_handler->sendMessage($yaml_form_submission_data);
          }
        }
      }
    }
    return array(
      '#markup' => 'Your Promocode ' . $data['promo_code'] . ' for part number ' . $data['part_number'] . '  is been Rejected.',
    );
  }

}
