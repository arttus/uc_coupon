<?php

namespace Drupal\uc_coupon\Form;

use Drupal\Core\Entity\ContentEntityForm;
use Drupal\Core\Form\FormStateInterface;
use Drupal\uc_coupon\Entity\Coupon;

/**
 * Provides form for block instance forms.
 */

class CouponForm extends ContentEntityForm {

  public function getFormId() {
    return 'uc_coupon_form';
  }
  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    /* @var $entity \Drupal\content_entity_example\Entity\Contact */
    $form = parent::buildForm($form, $form_state);
    $entity = $this->entity;
    return $form;
  }


  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    $form_state->setRedirect('entity.uc_coupon.collection');
    $entity = $this->getEntity();
    $entity->save();
  }

}
