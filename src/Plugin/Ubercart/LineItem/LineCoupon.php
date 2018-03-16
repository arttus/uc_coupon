<?php

namespace Drupal\uc_coupon\Plugin\Ubercart\LineItem;

use Drupal\uc_order\LineItemPluginBase;

/**
 * Handles the tax line item.
 *
 * @UbercartLineItem(
 *   id = "coupon",
 *   title = @Translation("Coupon Discount"),
 *   weight = 5,
 *   stored = TRUE,
 *   calculated = TRUE
 * )
 */
class LineCoupon extends LineItemPluginBase {
}
