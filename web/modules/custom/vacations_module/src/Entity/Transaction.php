<?php

namespace Drupal\vacations_module\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the transaction entity.
 *
 * @ingroup vacations_module
 *
 * @ContentEntityType(
 *   id = "transaction",
 *   label = @Translation("Transaction"),
 *   base_table = "transaction",
 *   entity_keys = {
 *     "id" = "id",
 *     "uuid" = "uuid",
 *     "created" = "created",
 *   },
 *   admin_permission = "administer my awesome entities",
 *   handlers = {
 *     "views_data" = "Drupal\views\EntityViewsData",
 *   },
 *   links = {
 *     "canonical" = "/transaction/{transaction}",
 *     "add-form" = "/transaction/add",
 *     "edit-form" = "/transaction/{transaction}/edit",
 *     "delete-form" = "/transaction/{transaction}/delete",
 *     "collection" = "/admin/content/transactions",
 *   },
 * )
 */
class Transaction extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('ID of transaction entity'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the transaction entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['days_adjusted'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Days Adjusted'))
      ->setDescription(t('Number of days adjusted in the user\'s balance. Positive value for days added, negative for days subtracted.'))
      ->setDefaultValue(0);

    $fields['current_balance'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Current Balance'))
      ->setDescription(t('Current balance of the user in days.'))
      ->setDefaultValue(0);

    return $fields;
  }
}
