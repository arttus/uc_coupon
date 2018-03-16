<?php

namespace Drupal\uc_coupon\Plugin\Ubercart\Coupon;

use Drupal\Core\Form\FormStateInterface;
use Drupal\uc_order\OrderInterface;
use Drupal\uc_coupon\CouponPluginBase;

/**
 * Defines a free order payment method.
 *
 * @UbercartCoupon(
 *   id = "percent_discount",
 *   name = @Translation("Percent Discount"),
 *   no_ui = TRUE
 * )
 */
class PercentDiscount extends PercentDiscountPluginBase {

  /**
   * {@inheritdoc}
   */
  public function cartDetails(OrderInterface $order, array $form, FormStateInterface $form_state) {
    return array(
      '#markup' => $this->t('Continue with checkout to complete your order.'),
    );
  }

  

}
