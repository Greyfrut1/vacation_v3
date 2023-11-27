<?php

namespace Drupal\vacations_module\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\vacations_module\Form\RequestForm;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class RequestController.
 */
class RequestController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('form_builder')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getForm() {
    return \Drupal::formBuilder()->getForm(RequestForm::class);
  }

}
