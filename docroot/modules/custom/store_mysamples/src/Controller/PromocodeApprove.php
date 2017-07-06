<?php

namespace Drupal\store_mysamples\Controller;

use Drupal\commerce_promotion\Entity\Coupon;
use Drupal\commerce_promotion\Entity\Promotion;
use Drupal\Core\Controller\ControllerBase;
use Drupal\yamlform\Entity\YamlFormSubmission;

/**
 * Class PromocodeApprove.
 *
 * @package Drupal\store_mysampes\Controller
 */
class PromocodeApprove extends ControllerBase {

  /**
   * Method to show the Promocode Approve Status.
   *
   * @param int $yamlsubmissionid
   *   Submission id for yaml Approve Form.
   *
   * @return string
   *   Returns string
   */
  public function content($yamlsubmissionid) {
    $current_path = \Drupal::service('path.current')->getPath();
    // $yaml_form_submission_entity->setData($data);
    // $yaml_form_submission_entity->save();
    if (preg_match('/yamlform\/submission\/\d+\/approve$/', $current_path)) {
      $yaml_form_submission_entity = YamlFormSubmission::load($yamlsubmissionid);
      $data = $yaml_form_submission_entity->getData();
      $data['promocode_status'] = 'Approved';
      $approver_subject = 'Request to Approve Promo Code';
      $requester_subject = 'Request For Promo Code Successfully Sent';
      $data['approver_subject'] = '[' . $data['promocode_status'] . '] ' . $approver_subject;
      $data['requester_subject'] = '[' . $data['promocode_status'] . '] ' . $requester_subject;
      $expire_date = date('m-d-y', strtotime("+7 days"));
      $data['expire_date'] = $expire_date;
      $data['approver_mail_statement'] = 'Promo code ' . $data['promo_code'] . ' has been Approved. Please note that this promo code is set to expire on ' . $expire_date . ' so please make sure to enter your order before then.';
      $data['requester_mail_statement'] = 'Request for ' . $data['promo_code'] . ' has been ' . $data['promocode_status'];
      $cat = $data['type'];
      $discount_amount = 0;
      $mpnid = $data['mpn_id'];
      $quantity = $data['quantity'];
      $data['action_links'] = 'display: none';
      if ($cat != 'cat_a') {
        $add_to_cart_link = 'width: 32%; display: inline-block; text-align: left;';
        $data['add_to_cart_link'] = $add_to_cart_link;
      }
      $param = explode('/', $current_path);
      $submission_status = $param[4];
      if (!empty($submission_status)) {
        $form['#attached']['library'][] = 'store_mysamples/promocode-submission';
      }
      if ($cat == 'cat_a') {
        $query = \Drupal::database()->select('commerce_product', 'cp');
        $query->fields('cp', ['product_id']);
        $query->join('commerce_product__field_price_one', 'p1', 'p1.entity_id = cp.product_id');
        $query->fields('p1', ['field_price_one_value']);
        $query->join('commerce_product__field_price_two', 'p2', 'p2.entity_id = cp.product_id');
        $query->fields('p2', ['field_price_two_value']);
        $query->join('commerce_product__field_price_three', 'p3', 'p3.entity_id = cp.product_id');
        $query->fields('p3', ['field_price_three_value']);
        $query->join('commerce_product__field_price_four', 'p4', 'p4.entity_id = cp.product_id');
        $query->fields('p4', ['field_price_four_value']);
        $query->join('commerce_product__field_price_five', 'p5', 'p5.entity_id = cp.product_id');
        $query->fields('p5', ['field_price_five_value']);
        $query->join('commerce_product__field_price_six', 'p6', 'p6.entity_id = cp.product_id');
        $query->fields('p6', ['field_price_six_value']);
        $query->join('commerce_product__field_mpn_id', 'cpmpnid', 'cpmpnid.entity_id = cp.product_id');
        $query->condition('cpmpnid.field_mpn_id_value', $mpnid);
        $results = $query->execute()->fetchAll();

        foreach ($results as $result) {
          $mpn_details = $result;
        }
        // Calculate discount based on quantity.
        if ($quantity <= 9) {
          $discount_price_per_unit = round($mpn_details->field_price_one_value, 2);
        }
        elseif ($quantity >= 10 and $quantity <= 24) {
          $discount_price_per_unit = round($mpn_details->field_price_two_value, 2);
        }
        elseif ($quantity >= 25 and $quantity <= 99) {
          $discount_price_per_unit = round($mpn_details->field_price_three_value, 2);
        }
        elseif ($quantity >= 100 and $quantity <= 249) {
          $discount_price_per_unit = round($mpn_details->field_price_four_value, 2);
        }
        elseif ($quantity >= 250 and $quantity <= 999) {
          $discount_price_per_unit = round($mpn_details->field_price_five_value, 2);
        }
        else {
          $discount_price_per_unit = round($mpn_details->field_price_six_value, 2);
        }
        $msrp = $discount_price_per_unit;
      }

      else {
        $query = \Drupal::database()->select('commerce_product', 'cp');
        $query->fields('cp', ['product_id']);
        $query->join('commerce_product__field_mpn_id', 'mpn', 'cp.product_id = mpn.entity_id');
        $query->fields('mpn', ['field_mpn_id_value']);
        $query->leftjoin('commerce_product__field_samplemsrp', 'msrp', 'cp.product_id = msrp.entity_id');
        $query->fields('msrp', ['field_samplemsrp_value']);
        $query->leftJoin('commerce_product__field_kit_cost', 'kc', 'cp.product_id = kc.entity_id');
        $query->fields('kc', ['field_kit_cost_value']);
        $query->leftJoin('commerce_product__field_active', 'ac', 'cp.product_id = ac.entity_id');
        $query->fields('ac', ['field_active_value']);
        $query->condition('mpn.field_mpn_id_value', $mpnid, '=');
        $query->condition('ac.field_active_value', 1);
        $results = $query->execute()->fetchAll();
        foreach ($results as $result) {
          $mpn_details = $result;
        }
        // CAT B Kit Price.
        if ($cat == 'cat_b_kit') {
          $msrp = $mpn_details->field_kit_cost_value;
        }
        // CAT B Silicon Price.
        elseif ($cat == 'cat_b') {
          $msrp = $mpn_details->field_samplemsrp_value;
        }
      }
      // To get the product_id for Promotions.
      $query = \Drupal::database()->select('commerce_product_field_data', 'cp');
      $query->fields('cp', ['product_id']);
      $query->condition('cp.title', $data['part_number']);
      $results = $query->execute()->fetchAll();
      $product_id = $results[0]->product_id;

      $data['approver_mail_statement'] = 'Promo code ' . $data['promo_code'] . ' has been Approved. Please note that this promo code is set to expire on ' . $expire_date . ' so please make sure to enter your order before then.';
      $data['requester_mail_statement'] = 'Request for ' . $data['promo_code'] . ' has been ' . $data['promocode_status'];
      $yaml_form_submission_entity->setData($data);
      $yaml_form_submission_entity->save();
      if ($data['promocode_status'] == 'Approved') {

        $yaml_form = $yaml_form_submission_entity->getYamlForm();
        $yaml_form_handlers = $yaml_form->getHandlers();
        $yaml_form_handler_ids = $yaml_form_handlers->getInstanceIds();
        foreach ($yaml_form_handler_ids as $yaml_form_handler_id) {
          if ($yaml_form_handler_id == 'email') {
            $yaml_email_handler = $yaml_form_handlers->get($yaml_form_handler_id);
            $yaml_form_submission_data = $yaml_email_handler->getMessage($yaml_form_submission_entity);
            $yaml_form_submission_data['subject'] = $data['approver_subject'];
            $yaml_email_handler->sendMessage($yaml_form_submission_data);
            // Create custom Promotion for each promocode.
            $promotion_with_coupon = Promotion::create([
              'name' => $data['part_number'],
              'order_types' => 'default',
              'stores' => 1,
              'status' => TRUE,
              'end_date' => date('Y-m-d', strtotime("+7 days")),
              'offer' => [
                'target_plugin_id' => 'commerce_promotion_product_quantity_fixed_off',
                'target_plugin_configuration' => [
                  'product_id' => $product_id,
                  'amount' => $msrp,
                  'quantity' => $data['quantity'],
                ],
              ],
              'conditions' => [
                [
                  'target_plugin_id' => 'commerce_promotion_order_total_price',
                  'target_plugin_configuration' => [
                    'id' => 'commerce_promotion_order_total_price',
                    'amount' => [
                      'number' => '0.00',
                      'currency_code' => 'USD',
                    ],
                    'operator' => '>',
                    'negate' => 0,
                  ],
                ],
              ],
            ]);
            $promotion_with_coupon->save();
            // Coupon Creation.
            $coupon = Coupon::create([
              'code' => $data['promo_code'],
              'usage_limit' => 1,
              'status' => TRUE,
            ]);
            $coupon->save();
            $promotion_with_coupon->get('coupons')->appendItem($coupon);
            $promotion_with_coupon->save();
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
      '#markup' => 'Your Promocode ' . $data['promo_code'] . ' for part number ' . $data['part_number'] . '  is been Approved.
     <br> Please check the email with add to cart link for adding this product to cart.',
    );
  }

}
