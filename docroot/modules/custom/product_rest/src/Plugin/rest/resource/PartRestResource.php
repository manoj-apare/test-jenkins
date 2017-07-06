<?php

namespace Drupal\product_rest\Plugin\rest\resource;

use Drupal\Core\Session\AccountProxyInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\commerce_product\Entity\Product;
use Drupal\rest\ResourceResponse;
use Drupal\commerce_product\Entity\ProductVariation;
use Drupal\commerce_price\Price;
use Drupal\taxonomy\Entity\Term;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Psr\Log\LoggerInterface;
use Drupal\physical\Weight;

/**
 * Provides a resource to get view modes by entity and bundle.
 *
 * @RestResource(
 *   id = "part_rest_resource",
 *   label = @Translation("Part rest resource"),
 *   uri_paths = {
 *     "canonical" = "/api/product/part",
 *     "https://www.drupal.org/link-relations/create" = "/api/product/part"
 *   }
 * )
 */
class PartRestResource extends ResourceBase {

  /**
   * A current user instance.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a Drupal\rest\Plugin\ResourceBase object.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param array $serializer_formats
   *   The available serialization formats.
   * @param \Psr\Log\LoggerInterface $logger
   *   A logger instance.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   A current user instance.
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    array $serializer_formats,
    LoggerInterface $logger,
    AccountProxyInterface $current_user) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);

    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('product_rest'),
      $container->get('current_user')
    );
  }

  /**
   * Responds to POST requests.
   *
   * Returns a list of bundles for specified entity.
   *
   * @param mixed $data
   *   Data.
   *
   * @return \Drupal\rest\ResourceResponse
   *   Throws exception expected.
   */
  public function post($data) {

    // You must to implement the logic of your REST Resource here.
    // Use current user after pass authentication to validate access.
    if (!$this->currentUser->hasPermission('access content')) {
      throw new AccessDeniedHttpException();
    }

    if (!isset($data['type']) || (isset($data['type']) && empty($data['type']))) {
      return new ResourceResponse(['Error:' => 'Type is required field and can\'t be empty']);
    }

    if (!isset($data['title']) || (isset($data['title']) && empty($data['title']))) {
      return new ResourceResponse(['Error:' => 'Title is required field and can\'t be empty']);
    }

    if (!isset($data['price_one']) || (isset($data['price_one']) && !is_numeric($data['price_one']))) {
      return new ResourceResponse(['Error:' => 'Price one is required field and should be a price']);
    }

    if (!isset($data['price_two']) || (isset($data['price_two']) && !is_numeric($data['price_two']))) {
      return new ResourceResponse(['Error:' => 'Prince two is required field and should be a price']);
    }

    if (!isset($data['price_three']) || (isset($data['price_three']) && !is_numeric($data['price_three']))) {
      return new ResourceResponse(['Error:' => 'Price three is required field and should be a price']);
    }

    if (!isset($data['price_four']) || (isset($data['price_four']) && !is_numeric($data['price_four']))) {
      $data['price_four'] = 0;
    }

    if (!isset($data['price_five']) || (isset($data['price_five']) && !is_numeric($data['price_five']))) {
      $data['price_five'] = 0;
    }
    if (!isset($data['price_six']) || (isset($data['price_six']) && !is_numeric($data['price_six']))) {
      $data['price_six'] = 0;
    }

    if ($data['can_sample'] == 'B') {
      $cansample = 2;
    }
    // For Product Taxonomy.
    $related_product = $data['part_related_products'];
    if ($related_product != '') {
      foreach ($related_product as $products) {
        $term_name = $products['term_name'];
        $allparents = $products['parents'];
        $vid = 'products';
        if (is_array($allparents)) {
          $pid = 0;
          foreach ($allparents as $parents) {
            $terms = $this->checkTid($parents, $vid);
            if ($terms != 0) {
              $pid = $terms[0]['target_id'];
            }
            else {
              if ($pid != 0) {
                $pid = $pid;
              }
              else {
                $pid = 0;
              }
            }
            $tags = $this->getTagIds($parents, $vid, $pid);
            $pid = $tags[0]['target_id'];
          }
        }
        else {
          $tags = $this->getTagIds($term_name, $vid, $pid = 0);
        }
        $all_tag_id = $this->checkTid($term_name, $vid);
        $product_tag_id[] = $tags[0]['target_id'];
      }
    }
    else {
      $product_tag_id = '';
    }

    // For Last Shipment date.
    $last_ship_date = explode(' ', $data['last_time_ship_date']);
    $last_date = $last_ship_date[0];

    // For Order Close.
    $order_close = explode(' ', $data['order_close_date']);
    $o_close = $order_close[0];

    // For Product obscelence date.
    $product_notice_date = explode(' ', $data['product_obs_notice_date']);
    $p_notice_date = $product_notice_date[0];

    // For Related Persona.
    $related_persona = $data['part_related_persona'];
    if ($related_persona != '') {
      foreach ($related_persona as $persona) {
        $tags = $this->getTagIds($persona, 'persona', $pid = 0);
        $related_persona_id[] = $tags[0]['target_id'];
      }
    }
    else {
      $related_persona_id = '';
    }

    // For Related Content Section.
    $related_content_section = $data['part_related_content_section'];
    if ($related_content_section != '') {
      foreach ($related_content_section as $content_section) {
        $tags = $this->getTagIds($content_section, 'content_section', $pid = 0);
        $related_content_section_id[] = $tags[0]['target_id'];
      }
    }
    else {
      $related_content_section_id = '';
    }

    // For Package Qualification Report.
    $package_spec_refs = $data['pqr_specnum_ref'];
    if ($package_spec_refs != '') {
      foreach ($package_spec_refs as $package_spec_ref) {
        $tags = $this->getTagIds($package_spec_ref, 'spec_numbers', $pid = 0);
        $package_spec_ref_id = $tags[0]['target_id'];
      }
    }

    $device_spec_refs = $data['dqr_specnum_ref'];
    if ($device_spec_refs != '') {
      foreach ($device_spec_refs as $device_spec_refs) {
        $tags = $this->getTagIds($device_spec_refs, 'spec_numbers', $pid = 0);
        $device_spec_ref_id = $tags[0]['target_id'];
      }
    }

    // For Related Content keywords.
    $related_content_keywords = $data['part_related_content_keywords'];
    if ($related_content_keywords != '') {
      foreach ($related_content_keywords as $content_keywords) {
        $tags = $this->getTagIds($content_keywords, 'content_keywords', $pid = 0);
        $related_content_keywords_id[] = $tags[0]['target_id'];
      }
    }
    else {
      $related_content_keywords_id = '';
    }

    if ($data['price_five'] == '') {
      $data['price_five'] = 0;
    }
    if ($data['price_six'] == '') {
      $data['price_six'] = 0;
    }
    $price = [
        [
          'part_value' => $data['price_one'],
          'weight' => 9,
        ],
        [
          'part_value' => $data['price_two'],
          'weight' => 24,
        ],
        [
          'part_value' => $data['price_three'],
          'weight' => 99,
        ],
        [
          'part_value' => $data['price_four'],
          'weight' => 249,
        ],
        [
          'part_value' => $data['price_five'],
          'weight' => 999,
        ],
        [
          'part_value' => $data['price_six'],
          'weight' => 1000,
        ],
    ];

    // Compare data with PLM.
    $query = \Drupal::database()->select('commerce_product_field_data', 'cp');
    $query->fields('cp', ['product_id']);
    $query->condition('cp.title', $data['title']);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $get_product_id = $result->product_id;
    }

    // Compare data with D7.
    $query = \Drupal::database()
      ->select('commerce_product__field_d7_node_id', 'cpnid');
    $query->fields('cpnid', ['entity_id']);
    $query->condition('cpnid.	field_d7_node_id_value', $data['node_id']);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $get_nid = $result->entity_id;
    }
    if (empty($get_product_id) && (!empty($get_product_id) || empty($get_nid))) {
      $product_variation = [];
      if (empty($data['samplemsrp']) || empty($data['kit_cost'])) {
        foreach ($price as $key => $pri) {
          $product_variation[] = ProductVariation::create(
            array(
              'type' => 'part_store',
              'price' => new Price($pri['part_value'], 'USD'),
              'weight' => new Weight($pri['weight'], 'kg'),
            )
          );
        }
      }
      if ($cansample == '2' && !empty($data['samplemsrp']) && $data['samplecategory'] != 'Kits') {
        $product_variation[] = ProductVariation::create(
          array(
            'type' => 'part_store',
            'price' => new Price($data['samplemsrp'], 'USD'),
          )
        );
      }
      elseif ($cansample == '2' && !empty($data['kit_cost']) && $data['samplecategory'] == 'Kits') {
        $product_variation[] = ProductVariation::create(
          array(
            'type' => 'part_store',
            'price' => new Price($data['kit_cost'], 'USD'),
          )
        );
      }

      $part = Product::create([
        'type' => 'part',
        'title' => $data['title'],
        'body' => [
          'value' => $data['body']['value'],
          'format' => 'full_html',
        ],
        'stores' => 1,
        'field_d7_node_id' => $data['node_id'],
        'field_can_sample' => $data['can_sample'],
        'field_development_kit' => $data['development_kit'],
        'field_eccn' => $data['eccn'],
        'field_eccn_suball' => $data['eccn_suball'],
        'field_estimated_lead_time_days' => $data['estimated_lead_time_days'],
        'field_hts_code' => $data['hts_code'],
        'field_inventory' => $data['inventory'],
        'field_shipping_closed' => $last_date,
        'field_lead_ball_finish' => $data['lead_ball_finish'],
        'field_minimum_order_quantity_moq' => $data['minimum_order_quantity_moq'],
        'field_moisture_sensitivity_level' => $data['moisture_sensitivity_level'],
        'field_mpn_id' => $data['mpn_id'],
        'field_no_of_pins' => $data['no_of_pins'],
        'field_order_close' => $o_close,
        'field_order_entry_closed_date' => date('Y-m-d\TH:i:s', $data['order_entry_closed_date']),
        'field_order_increment' => $data['order_increment'],
        'field_package' => $data['package'],
        'field_package_type' => $data['package_type'],
        'field_pb_free' => $data['pb_free'],
        'field_peak_reflow_temp' => $data['peak_reflow_temp'],
        'field_pqtp_name' => $data['pqtp_name'],
        'field_price_five' => $data['price_five'],
        'field_price_four' => $data['price_four'],
        'field_price_one' => $data['price_one'],
        'field_price_six' => $data['price_six'],
        'field_price_three' => $data['price_three'],
        'field_price_two' => $data['price_two'],
        'field_product_family' => $data['product_family'],
        'field_prune_start_date' => $p_notice_date,
        'field_rohs_compliant' => $data['rohs_compliant'],
        'field_show_price' => $data['show_price'],
        'field_standard_pack_quantity' => $data['standard_pack_quantity'],
        'field_status_display' => $data['status_display'],
        'field_status_raw' => $data['status'],
        'field_related_persona' => $related_persona_id,
        'field_related_content_section' => $related_content_section_id,
        'field_related_content_keywords' => $related_content_keywords_id,
        'field_related_products' => $product_tag_id,
        'field_part_family' => $data['part_family'],
        'field_pqr_specnum_ref' => $package_spec_ref_id,
        'field_dqr_specnum_ref' => $device_spec_ref_id,
        'field_active' => $data['active'],
        'field_samplemsrp' => $data['samplemsrp'],
        'field_kit_cost' => $data['kit_cost'],
        'field_mpn_cat_a_b' => $data['cat_a_b'],
        'field_sample_category' => $data['samplecategory'],
        'field_plm' => $data['plm'],
        'variations' => $product_variation,
      ]);
      $part->save();
    }
    elseif (!empty($get_product_id || !empty($get_nid))) {
      if (!empty($get_product_id)) {
        $part = Product::load($get_product_id);
      }
      else {
        $part = Product::load($get_nid);
      }
      if ($cansample == '2' && !empty($data['samplemsrp']) && $data['samplecategory'] != 'Kits') {
        $product_variation = $part->getVariations()[0]->id();
        $product_variation = ProductVariation::load($product_variation);
        $product_variation->type = 'part_store';
        $product_variation->price = new Price($data['samplemsrp'], 'USD');
        $product_variation->save();
      }
      elseif ($cansample == '2' && !empty($data['kit_cost']) && $data['samplecategory'] == 'Kits') {
        $product_variation = $part->getVariations()[0]->id();
        $product_variation = ProductVariation::load($product_variation);
        $product_variation->type = 'part_store';
        $product_variation->price = new Price($data['kit_cost'], 'USD');
        $product_variation->save();
      }
      else {
        $part_variations = $part->getVariations();
        foreach ($part_variations as $key => $part_variation) {
          $variation_id = $part_variation->id();
          $cy_part_variation = ProductVariation::load($variation_id);
          $cy_part_variation->type = 'part_store';
          $cy_part_variation->price = new Price($price[$key]['part_value'], 'USD');
          $cy_part_variation->save();
        }
      }
      // Save Part Product.
      $part->title = $data['title'];
      $part->body->value = $data['body']['value'];
      $part->body->format = 'full_html';
      $part->field_can_sample = $data['can_sample'];
      $part->field_development_kit = $data['development_kit'];
      $part->field_eccn = $data['eccn'];
      $part->field_eccn_suball = $data['eccn_suball'];
      $part->field_estimated_lead_time_days = $data['estimated_lead_time_days'];
      $part->field_hts_code = $data['hts_code'];
      $part->field_inventory = $data['inventory'];
      $part->field_shipping_closed = $last_date;
      $part->field_lead_ball_finish = $data['lead_ball_finish'];
      $part->field_minimum_order_quantity_moq = $data['minimum_order_quantity_moq'];
      $part->field_moisture_sensitivity_level = $data['moisture_sensitivity_level'];
      $part->field_mpn_id = $data['mpn_id'];
      $part->field_no_of_pins = $data['no_of_pins'];
      $part->field_order_close = $o_close;
      $part->field_order_entry_closed_date = date('Y-m-d\TH:i:s', $data['order_entry_closed_date']);
      $part->field_order_increment = $data['order_increment'];
      $part->field_package = $data['package'];
      $part->field_package_type = $data['package_type'];
      $part->field_pb_free = $data['pb_free'];
      $part->field_peak_reflow_temp = $data['peak_reflow_temp'];
      $part->field_pqtp_name = $data['pqtp_name'];
      $part->field_price_five = $data['price_five'];
      $part->field_price_four = $data['price_four'];
      $part->field_price_one = $data['price_one'];
      $part->field_price_six = $data['price_six'];
      $part->field_price_three = $data['price_three'];
      $part->field_price_two = $data['price_two'];
      $part->field_product_family = $data['product_family'];
      $part->field_prune_start_date = $p_notice_date;
      $part->field_rohs_compliant = $data['rohs_compliant'];
      $part->field_show_price = $data['show_price'];
      $part->field_standard_pack_quantity = $data['standard_pack_quantity'];
      $part->field_status_display = $data['status_display'];
      $part->field_status_raw = $data['status'];
      $part->field_related_persona = $related_persona_id;
      $part->field_related_content_section = $related_content_section_id;
      $part->field_related_content_keywords = $related_content_keywords_id;
      $part->field_related_products = $product_tag_id;
      $part->field_part_family = $data['part_family'];
      $part->field_pqr_specnum_ref = $package_spec_ref_id;
      $part->field_dqr_specnum_ref = $device_spec_ref_id;
      $part->field_active = $data['active'];
      $part->field_samplemsrp = $data['samplemsrp'];
      $part->field_kit_cost = $data['kit_cost'];
      $part->field_mpn_cat_a_b = $data['cat_a_b'];
      $part->field_sample_category = $data['samplecategory'];
      $part->field_plm = $data['plm'];
      $part->save();
    }
    elseif ($data['operations'] == 'delete') {
      if (!empty($get_product_id)) {
        $id = $get_product_id;
      }
      else {
        $id = $get_nid;
      }
      $part = Product::load($id);
      $part->type = 'part';
      $part->delete();
    }

    return new ResourceResponse($part);
  }

  /**
   * Utility: find term by name and vid.
   *
   * @param int $pid
   *   Parent term vid.
   * @param string $name
   *   Term name.
   * @param int $vid
   *   Term vid.
   *
   * @return int
   *   Term id or 0 if none.
   */
  private function getTidByName($pid, $name = NULL, $vid = NULL) {
    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    // For single term insertion.
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);

    // Create tag if not present.
    if (empty($term)) {
      Term::create([
        'name' => $name,
        'vid' => $vid,
        'parent' => [$pid],
      ])->save();

      $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
      $term = reset($terms);
    }
    return !empty($term) ? $term->id() : 0;
  }

  /**
   * Method to get tag ids.
   *
   * @param mixed $tags
   *   Tag names.
   * @param string $vid
   *   Vocabulary id.
   * @param int $pid
   *   Parent term vid.
   *
   * @return array
   *   Tag ids.
   */
  private function getTagIds($tags, $vid, $pid) {
    $tag_ids = [];
    $tags = array($tags);
    foreach ($tags as $tag_name) {
      $tag_id = $this->getTidByName($pid, $tag_name, $vid);
      if ($tag_id) {
        $tag_ids[] = ['target_id' => $tag_id];
      }
    }

    return $tag_ids;
  }

  /**
   * Method to check whether the term id is already there.
   *
   * @param string $name
   *   Term name.
   * @param int $vid
   *   Term vid.
   *
   * @return int
   *   Term id or 0 if none.
   */
  private function checkTid($name = NULL, $vid = NULL) {

    $properties = [];
    if (!empty($name)) {
      $properties['name'] = $name;
    }
    if (!empty($vid)) {
      $properties['vid'] = $vid;
    }
    // For single term insertion.
    $terms = \Drupal::entityManager()->getStorage('taxonomy_term')->loadByProperties($properties);
    $term = reset($terms);
    if ($term) {
      $tag_ids[] = ['target_id' => $term];
    }
    else {
      return !empty($term) ? $term->id() : 0;
    }
  }

}
