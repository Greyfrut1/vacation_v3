<?php

namespace Drupal\vacations_module\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Provides a form for creating a new vacation request.
 */
class RequestForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'vacations_module_request_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    // Додайте поля форми для введення даних користувача.
    $form['start_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('Start Date'),
      '#required' => TRUE,
    ];

    $form['end_date'] = [
      '#type' => 'datetime',
      '#title' => $this->t('End Date'),
      '#required' => TRUE,
    ];

    $form['reason'] = [
      '#type' => 'textarea',
      '#title' => $this->t('Reason for vacation'),
      '#required' => TRUE,
    ];

    // Додайте кнопку "Submit" для відправлення форми.
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
    // Отримайте дані форми та створіть новий запис "Request".
    $start_date = $form_state->getValue('start_date')->getTimestamp();
    $end_date = $form_state->getValue('end_date')->getTimestamp();
    $reason = $form_state->getValue('reason');

    // Отримайте поточного користувача.
    $current_user = \Drupal::currentUser();
    \Drupal::messenger()->addMessage('start ' . $start_date);
    \Drupal::messenger()->addMessage('end ' . $end_date);
    \Drupal::messenger()->addMessage('reason ' . $reason);
    // Створіть новий об'єкт "Request".
    $request = \Drupal\vacations_module\Entity\Request::create([
      'start_date' => $start_date,
      'end_date' => $end_date,
      'user_id' => $current_user->id(),
      'status' => 'pending', // Призначте статус за замовчуванням (на розгляді).
      'reason' => $reason,
    ]);

    // Збережіть новий запис "Request".
    $request->save();
    \Drupal::messenger()->addMessage($this->t('Your vacation request has been submitted.'));
    // Повідомте користувача про успішне створення запиту.
//    \Drupal::messen    ($this->t('Your vacation request has been submitted.'));

    // Перенаправте користувача на головну сторінку або іншу сторінку за необхідності.
    $form_state->setRedirect('<front>');
  }

}
