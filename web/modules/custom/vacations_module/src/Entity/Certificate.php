<?php

namespace Drupal\vacations_module\Entity;

use Drupal\Core\Entity\ContentEntityBase;
use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;

/**
 * Defines the certificate entity.
 *
 * @ingroup vacations_module
 *
 * @ContentEntityType(
 *   id = "certificate",
 *   label = @Translation("Certificate"),
 *   base_table = "certificate",
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
 *     "canonical" = "/certificate/{certificate}",
 *     "add-form" = "/certificate/add",
 *     "edit-form" = "/certificate/{certificate}/edit",
 *     "delete-form" = "/certificate/{certificate}/delete",
 *     "collection" = "/admin/content/certificates",
 *   },
 * )
 */
class Certificate extends ContentEntityBase implements ContentEntityInterface {

  /**
   * Defines the base fields for the certificate entity.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   *   The entity type to define fields for.
   *
   * @return array
   *   An array of base field definitions.
   */
  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('ID'))
      ->setDescription(t('Id of certificate entity'))
      ->setReadOnly(TRUE);

    $fields['uuid'] = BaseFieldDefinition::create('uuid')
      ->setLabel(t('UUID'))
      ->setDescription(t('The UUID of the certificate entity.'))
      ->setReadOnly(TRUE);

    $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields["days"] = BaseFieldDefinition::create("integer")
      ->setLabel(t("Days"))
      ->setDescription(t("Certificate days."))
      ->setDefaultValue(0);

    $fields["staff_id"] = BaseFieldDefinition::create('entity_reference')
      ->setLabel(t("Staff ID"))
      ->setDescription(t("Reference to staff"))
      ->setSetting('target_type', 'user')
      ->setSetting('handler', 'default');

    $fields['start_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('Start Date'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['certificate_type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Certificate Type'))
      ->setDescription(t('The type of the certificate.'))
      ->setSettings([
        'allowed_values' => [
          'basic' => 'Basic',
          'special' => 'Special',
        ],
      ])
      ->setDefaultValue('basic');


    $fields['end_date'] = BaseFieldDefinition::create('timestamp')
      ->setLabel(t('End Date'))
      ->setDescription(t('The time that the entity was expired.'));

    return $fields;
  }

}
