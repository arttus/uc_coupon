<?php

namespace Drupal\uc_coupon;

use Drupal\views\EntityViewsData;

/**
 * Provides the views data for the uc_order entity type.
 */
class CouponViewsData extends EntityViewsData {

  /**
   * {@inheritdoc}
   */
  public function getViewsData() {
    $data = parent::getViewsData();

    $data['uc_coupons']['table']['group'] = $this->t('Coupon');
    $data['uc_coupons']['table']['base'] = array(
      'field' => 'id',
      'title' => $this->t('Coupons'),
    );

    
    $data['uc_coupons']['discount'] = array(
      'title' => $this->t('Coupon Discount'),
      'field' => array(
        'id' => 'discount',
      )
    );
    $data['uc_coupons']['id'] = array(
      'title' => $this->t('Coupon ID'),
      'help' => $this->t('The ID of the coupon.'),
      'field' => array(
        'id' => 'standard',
        'click sortable' => TRUE,
      ),
      'argument' => array(
        'id' => 'id',
        'name field' => 'name',
      ),
      'sort' => array(
        'id' => 'standard',
      ),
      'filter' => array(
        'id' => 'standard',
      ),
    );

    return $data;
  }

}
