<?php

namespace Drupal\cypress_store_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;
use Drupal\Core\Locale\CountryManager;
use CommerceGuys\Addressing\Subdivision\SubdivisionRepository;

/**
 * Provides a CypressStoreMigration migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "cypressstoremigration"
 * )
 */
class CypressStoreMigrationAddress extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Plugin logic goes here.
    $country_name = $row->getSourceProperty('COUNTRYNAME');
    $address_line1 = $row->getSourceProperty('ADDRESS');
    $address_line2 = $row->getSourceProperty('ADDRESSMORE');
    $organization = $row->getSourceProperty('COMPANYNAME');
    $administrative_area = $row->getSourceProperty('REGIONNAME');
    $locality = $row->getSourceProperty('CITYNAME');
    $given_name = $row->getSourceProperty('FIRSTNAME');
    $family_name = $row->getSourceProperty('LASTNAME');
    $tel_code = $row->getSourceProperty('TELEPHONEAREACODE');
    $tel_number = $row->getSourceProperty('TELEPHONE');
    $contact = $tel_code . '-' . $tel_number;
    $postal_code = $row->getSourceProperty('POSTALCODE');

    // To get he country code.
    $country_list = CountryManager::getStandardList();
    $country_code = array_search($country_name, $country_list);

    // To get the state value of respective country.
    $subdivision_repository = new SubdivisionRepository();
    $states = $subdivision_repository->getAll([$country_code]);
    foreach ($states as $state) {
      $municipalities = $state->getName();
      if ($administrative_area == $municipalities) {
        $state_code = $state->getCode();
      }
    }

    // Return new address values from csv.
    $address_new_values = array(
      "country_code" => $country_code,
      "administrative_area" => $state_code,
      "locality" => $locality,
      "postal_code" => $postal_code,
      "address_line1" => $address_line1,
      "address_line2" => $address_line2,
      "given_name" => $given_name,
      "family_name" => $family_name,
      "organization" => $organization,
      "contact" => $contact,
    );
    return $address_new_values;

  }

}
