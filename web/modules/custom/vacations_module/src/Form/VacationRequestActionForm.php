<?php

namespace Drupal\vacations_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for vacation request actions.
 */
class VacationRequestActionForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vacation_request_action_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state, $request = NULL, $action = NULL) {
    // Add your form elements here.

    $form['#request'] = $request;
    $form['#action'] = $action;

    $form['submit'] = [
      '#type' => 'submit',
      '#value' => $this->t('Submit'),
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    // Add validation logic if needed.
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $request = $form_state->get('request');
    $action = $form_state->get('action');

    // Додайте логіку вибору дії тут та викликайте методи approveAction або rejectAction.
  }

}
