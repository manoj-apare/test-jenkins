<?php

namespace Drupal\cypress_store_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * Provides a CypressStoreMigration migrate process plugin.
 *
 * @MigrateProcessPlugin(
 *  id = "cypressaddressmigration"
 * )
 */
class CypressAddressMigration extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    // Get the user id from legacy_id.
    $legacy_id = $row->getSourceProperty('USERLEGACYID');
    $query = \Drupal::database()->select('user__field_legacy_uid', 'ufid');
    $query->fields('ufid', ['entity_id']);
    $query->condition('ufid.field_legacy_uid_value', $legacy_id);
    $results = $query->execute()->fetchAll();
    foreach ($results as $result) {
      $user_id = $result->entity_id;
    }
    if (!empty($user_id)) {
      $uid = $user_id;
    }
    else {
      $uid = NULL;
    }

    return $uid;

  }

}
