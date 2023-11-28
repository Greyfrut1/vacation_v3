<?php

namespace Drupal\vacations_module\Controller;

use Drupal\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
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
    $query = $this->entityTypeManager->getStorage('request')->getQuery()->accessCheck(FALSE);
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
    ];

    return $actions;
  }
  public function approveAction($request) {
    // Ваш код для затвердження запиту.
    // Наприклад, зміна статусу запиту на "Approved".
    \Drupal::messenger()->addMessage('approve');
    // Повертаємо користувача на сторінку списку запитів.
    return $this->redirect('vacations_module.vacation_requests');
  }

  public function rejectAction($request) {
    // Ваш код для відхилення запиту.
    // Наприклад, зміна статусу запиту на "Rejected".
    \Drupal::messenger()->addMessage('reject');
    // Повертаємо користувача на сторінку списку запитів.
    return $this->redirect('vacations_module.vacation_requests');
  }

}

