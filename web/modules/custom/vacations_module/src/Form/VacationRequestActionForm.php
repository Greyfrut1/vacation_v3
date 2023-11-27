<?php

namespace Drupal\vacations_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for approving or rejecting vacation requests.
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
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['action'] = [
      '#type' => 'select',
      '#title' => $this->t('Select an action'),
      '#options' => [
        'approve' => $this->t('Approve'),
        'reject' => $this->t('Reject'),
      ],
      '#required' => TRUE,
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
    $action = $form_state->getValue('action');
    // You can perform actions based on the selected option.
    // For now, let's just display a message.
    \Drupal::messenger()->addMessage($this->t('Vacation request has been @action.', ['@action' => $action]));
  }
}
