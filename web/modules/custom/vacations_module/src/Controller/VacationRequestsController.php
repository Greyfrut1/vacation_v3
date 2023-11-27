<?php
// vacations_module/src/Controller/VacationRequestsController.php

namespace Drupal\vacations_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBuilderInterface;
use Drupal\Core\Session\AccountProxyInterface;
use Drupal\Core\Url;
use Drupal\Core\Link;
use Drupal\vacations_module\Form\VacationRequestActionForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
   * The form builder.
   *
   * @var \Drupal\Core\Form\FormBuilderInterface
   */
  protected $formBuilder;

  /**
   * The current user.
   *
   * @var \Drupal\Core\Session\AccountProxyInterface
   */
  protected $currentUser;

  /**
   * Constructs a new VacationRequestsController.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\Form\FormBuilderInterface $form_builder
   *   The form builder.
   * @param \Drupal\Core\Session\AccountProxyInterface $current_user
   *   The current user.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, FormBuilderInterface $form_builder, AccountProxyInterface $current_user) {
    $this->entityTypeManager = $entity_type_manager;
    $this->formBuilder = $form_builder;
    $this->currentUser = $current_user;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager'),
      $container->get('form_builder'),
      $container->get('current_user')
    );
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
//      'actions' => $this->t('Actions'),
    ];

    $rows = [];

    // Load vacation requests.
    $query = \Drupal::entityTypeManager()->getStorage('request')->getQuery();
    $request_ids = $query
      ->condition('user_id', $this->currentUser->id())
      ->accessCheck(FALSE)
      ->execute();

    $requests = $this->entityTypeManager->getStorage('request')->loadMultiple($request_ids);

    foreach ($requests as $request) {
      $rows[] = [
        'id' => $request->id(),
        'start_date' => date('Y-m-d H:i:s', $request->get('start_date')->value),
        'end_date' => date('Y-m-d H:i:s', $request->get('end_date')->value),
        'user_id' => $request->get('user_id')->entity->id(), // Assuming user_id is an entity reference field
        'status' => $request->get('status')->value,
        'reason' => $request->get('reason')->value,
//        'actions' => $this->buildActions($request),
      ];
    }

    $form = $this->formBuilder->getForm('\Drupal\vacations_module\Form\VacationRequestActionForm');

    return [
      'table' => [
        '#type' => 'table',
        '#header' => $header,
        '#rows' => $rows,
        '#empty' => $this->t('No vacation requests found.'),
      ],
      'form' => $form,
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
    $actions['approve'] = Link::createFromRoute(
      $this->t('Approve'),
      'vacations_module.request_action_form',
      [
        'request' => $request->id(),
        'action' => 'approve',
      ]
    );

    // Add "Reject" button.
    $actions['reject'] = Link::createFromRoute(
      $this->t('Reject'),
      'vacations_module.request_action_form',
      [
        'request' => $request->id(),
        'action' => 'reject',
      ]
    );

    return $actions;
  }
}
