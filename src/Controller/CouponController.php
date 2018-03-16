<?php

namespace Drupal\uc_coupon\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\uc_coupon\CouponInterface;

/**
 * Route controller for coupons.
 */

class CouponController extends ControllerBase {

  /**
   * Build the coupon instance add form.
   *
   * @param string $plugin_id
   *   The plugin ID for the coupon.
   *
   * @return array
   *   The coupon instance edit form.
   */
  public function addForm() {
 

    $entity = $this->entityTypeManager()->getStorage('uc_coupon')->create();

    return $this->entityFormBuilder()->getForm($entity);
  }
  
  /**
   * Performs an operation on the coupon entity.
   *
   * @param \Drupal\uc_coupon\CouponInterface $uc_coupon
   *   The coupon entity.
   * @param string $op
   *   The operation to perform, usually 'enable' or 'disable'.
   *
   * @return \Symfony\Component\HttpFoundation\RedirectResponse
   *   A redirect back to the coupon listing page.
   */
  public function performOperation(CouponInterface $uc_coupon, $op) {
   
   
    if ($op == 'enable') {
      $uc_coupon->enable();
      $uc_coupon->save();

      drupal_set_message($this->t('The %label coupon has been enabled.', ['%label' => $uc_coupon->label()]));
    }
    elseif ($op == 'disable') {
      $uc_coupon->disable();
      $uc_coupon->save();
      drupal_set_message($this->t('The %label coupon has been disabled.', ['%label' => $uc_coupon->label()]));
    }
  
    $url = $uc_coupon->toUrl('collection');
    return $this->redirect($url->getRouteName(), $url->getRouteParameters(), $url->getOptions());
  }

}
