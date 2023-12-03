<?php

namespace Drupal\vacations_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\vacations_module\Entity\Certificate;
use Drupal\vacations_module\Entity\Transaction;

/**
 * Form controller for Certificate entity forms.
 *
 * @ingroup vacations_module
 */
class CertificateForm extends FormBase {

  /**
   * {@inheritdoc}
   */

  public function getFormId() {
    return 'vacations_module_certificate_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
//    $form = parent::buildForm($form, $form_state);
//
//    $entity = $this->entity;

    // Додати додаткові поля форми для Certificate.
    $form['days'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Days'),
//      '#default_value' => $entity->get('days')->value,
      '#required' => TRUE,
    ];

    $form['staff_id'] = [
      '#type' => 'entity_autocomplete',
      '#title' => $this->t('Staff ID'),
      '#target_type' => 'user',
//      '#default_value' => $entity->get('staff_id')->entity,
      '#required' => TRUE,
    ];

    $form['start_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start Date'),
//      '#default_value' => $entity->get('start_date')->value,
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#date_timezone' => \Drupal::time()->getCurrentTime(),
    ];

    $form['end_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('End Date'),
//      '#default_value' => $entity->get('end_date')->value,
      '#date_date_element' => 'date',
      '#date_time_element' => 'none',
      '#date_timezone' => \Drupal::time()->getCurrentTime(),
    ];
    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
//    $entity = $this->getEntity();
    $entity = \Drupal\vacations_module\Entity\Certificate::create();
    // Зберегти значення полів Certificate.
    $entity->set('days', $form_state->getValue('days'));
    $entity->set('staff_id', $form_state->getValue('staff_id'));
    $entity->set('start_date', $form_state->getValue('start_date')->getTimestamp());
    $entity->set('end_date', $form_state->getValue('end_date')->getTimestamp());
    $entity->set('certificate_type', 'special');

    // Зберегти сутність.
    $entity->save();
    \Drupal::messenger()->addMessage($this->t('The Certificate has been saved.'));
    // Вивести повідомлення про успішне збереження.
    $certificate2_query = \Drupal::entityQuery('certificate')
      ->accessCheck(FALSE);
    $certificate2_query->exists('staff_id');
    $user_ids_with_certificates2 = $certificate2_query->execute();

    $certificates2 = Certificate::loadMultiple($user_ids_with_certificates2);
        $all_days_current_user = 0;
        foreach ($certificates2 as $certificate2){
          if($certificate2->get('staff_id')->target_id == $entity->get('staff_id')->target_id){
            $all_days_current_user = $all_days_current_user + $certificate2->get('days')->value;
          }

        }
        $transaction = Transaction::create([
          'user_id' => $entity->get('staff_id')->target_id,
          'days_adjusted' => $entity->get('days')->value,
          'current_balance' => $all_days_current_user,
        ]);
        $transaction->save();

    // Перенаправити на сторінку перегляду сутності.
    $form_state->setRedirect('vacations_module.vacation_requests');
  }

}
