<?php

namespace Drupal\vacations_module\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the request entity.
 *
 * @ingroup vacations_module
 *
 * @ContentEntityType(
 *   id = "request",
 *   label = @Translation("Request"),
 *   base_table = "request",
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
 *     "canonical" = "/request/{request}",
 *     "add-form" = "/request/add",
 *     "edit-form" = "/request/{request}/edit",
 *     "delete-form" = "/request/{request}/delete",
 *     "collection" = "/admin/content/requests",
 *   },
 * )
 */
class Request extends ContentEntityBase implements ContentEntityInterface {

  /**
   * {@inheritdoc}
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('ID of request entity'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the request entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['start_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('Start Date'))
      ->setDescription(t('The start date of the vacation request.'))
      ->setSetting('datetime_type', 'datetime')
      ->setDefaultValue(NULL)  // Змінив порожній масив на NULL.
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'short',
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ]);

    $fields['end_date'] = BaseFieldDefinition::create('datetime')
      ->setLabel(t('End Date'))
      ->setDescription(t('The end date of the vacation request.'))
      ->setSetting('datetime_type', 'datetime')
      ->setDefaultValue(NULL)  // Змінив порожній масив на NULL.
      ->setDisplayOptions('view', [
        'label' => 'above',
        'type' => 'datetime_default',
        'settings' => [
          'format_type' => 'short',
        ],
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'datetime_default',
        'weight' => 0,
      ]);

    $fields['user_id'] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t('User ID'))
      ->setDescription(t('Reference to the user making the vacation request.'))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');

    $fields['status'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Status'))
      ->setDescription(t('The status of the vacation request.'))
      ->setSettings([
        'allowed_values' => [
          'pending' => 'На розгляді',
          'approved' => 'Підтверджено',
          'rejected' => 'Відхилено',
        ],
      ])
      ->setDisplayOptions('view', [
        'label' => 'visible',
        'type' => 'string',
        'weight' => 0,
      ])
      ->setDisplayOptions('form', [
        'type' => 'options_select',
        'weight' => 0,
      ]);

    $fields['reason'] = BaseFieldDefinition::create('text_long')
      ->setLabel(t('Reason'))
      ->setDescription(t('The reason for the vacation request.'));

    return $fields;
  }
}
