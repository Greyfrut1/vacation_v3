<?php

namespace Drupal\vacations_module\Controller;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\vacations_module\Entity\Certificate;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller for displaying vacation requests.
 */
class VacationRequestsController extends ControllerBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new VacationRequestsController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface|\Symfony\Component\DependencyInjection\ContainerInterface $container) {
    return new static($container->get('entity_type.manager'));
  }

  /**
   * Displays a list of vacation requests.
   */
  public function vacationRequestsPage() {
    $header = [
      'id' => $this->t('ID'),
      'start_date' => $this->t('Start Date'),
      'end_date' => $this->t('End Date'),
      'user_id' => $this->t('User ID'),
      'status' => $this->t('Status'),
      'reason' => $this->t('Reason'),
      'actions' => $this->t('Actions'),
    ];

    $rows = [];

    // Load vacation requests.
    $query = $this->entityTypeManager->getStorage('request')->getQuery()->accessCheck(FALSE)->condition('status', 'pending', '=');
    $request_ids = $query->execute();
    $requests = $this->entityTypeManager->getStorage('request')->loadMultiple($request_ids);

    foreach ($requests as $request) {
      // Build actions.
      $actions = $this->buildActions($request);

      $rows[] = [
        'id' => $request->id(),
        'start_date' => date('Y-m-d H:i:s', $request->get('start_date')->value),
        'end_date' => date('Y-m-d H:i:s', $request->get('end_date')->value),
        'user_id' => $request->get('user_id')->entity->getAccountName(),
        'status' => $request->get('status')->value,
        'reason' => $request->get('reason')->value,
        'actions' => [
          'data' => [
            '#type' => 'container',
            'approve' => $actions['approve'],
            'reject' => $actions['reject'],
          ],
        ],
      ];
    }

    return [
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No vacation requests found.'),
      ],
    ];
  }

  /**
   * Builds the actions for the vacation request.
   *
   * @param \Drupal\vacations_module\Entity\Request $request
   *   The vacation request entity.
   *
   * @return array
   *   The render array for actions.
   */
  protected function buildActions(\Drupal\vacations_module\Entity\Request $request) {
    $actions = [];

    // Add "Approve" button.
    $actions['approve'] = [
      '#type' => 'link',
      '#title' => $this->t('Approve'),
      '#url' => Url::fromRoute(
        'vacations_module.approve_action',
        ['request' => $request->id()]
      ),
    ];

// Add "Reject" button.
    $actions['reject'] = [
      '#type' => 'link',
      '#title' => $this->t('Reject'),
      '#url' => Url::fromRoute(
        'vacations_module.reject_action',
        ['request' => $request->id()]
      ),
      '#cache' => ['max-age' => 0],
    ];

    return $actions;
  }
  public function approveAction($request) {
    // Отримайте запит на відпустку.
    $entity = \Drupal::entityTypeManager()->getStorage('request')->load($request);
    \Drupal::messenger()->addMessage('requestik' . $entity->id());
    // Отримайте користувача і його сертифікати.
    $user = $entity->get('user_id')->entity;
    $certificate_query = \Drupal::entityQuery('certificate')
      ->accessCheck(FALSE)
      ->condition('staff_id', $user->id(), '=')
      ->sort('created');
    $certificates = Certificate::loadMultiple($certificate_query->execute());

    // Отримайте початкову та кінцеву дати відпустки.
    $start_date = DrupalDateTime::createFromTimestamp($entity->get('start_date')->value);
    $end_date = DrupalDateTime::createFromTimestamp($entity->get('end_date')->value);

    // Розрахуйте кількість днів від початку до кінця відпустки.
    $interval = $start_date->diff($end_date);
    $days_requested = $interval->days;

    // Отримайте кількість доступних днів у сертифікатах.
    $total_certificates_days = 0;
    foreach ($certificates as $certificate) {
      \Drupal::messenger()->addMessage('certificateteee' . $certificate->id());
      $total_certificates_days += $certificate->get('days')->value;
    }

    // Відніміть кількість запитаних днів від сертифікатів.
    if ($total_certificates_days >= $days_requested) {
      // Відніміть дні лише якщо їх достатньо.
      foreach ($certificates as $certificate) {
        $days_to_subtract = min($certificate->get('days')->value, $days_requested);
        $certificate->set('days', $certificate->get('days')->value - $days_to_subtract);
        $days_requested -= $days_to_subtract;
        if ($days_requested == 0) {
          break;
        }
      }

      // Збережіть зміни у сертифікатах та повідомте про успішну операцію.
      foreach ($certificates as $certificate) {
        $certificate->save();
      }
      \Drupal::messenger()->addMessage('Approved vacation request. Deducted ' . $days_requested . ' days from certificates.');
    } else {
      \Drupal::messenger()->addError('Insufficient days in certificates to approve the vacation request.');
    }
    $entity->set('status', 'approved');
    $entity->save();
    // Поверніть користувача на сторінку списку запитів.
    return $this->redirect('vacations_module.vacation_requests');
  }



  public function rejectAction($request) {
    \Drupal::messenger()->addMessage('reject');
    $entity = \Drupal::entityTypeManager()->getStorage('request')->load($request);
    $entity->set('status', 'rejected');
    $entity->save();

    // Заборона кешування для оновленої сутності.
    \Drupal\Core\Cache\Cache::invalidateTags([$entity->getEntityTypeId() . ':' . $entity->id()]);

    // Повертаємо користувача на сторінку списку запитів.
    return $this->redirect('vacations_module.vacation_requests');
  }

}

