<?php

namespace Drupal\uc_coupon\Plugin;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\uc_order\OrderInterface;
use Drupal\uc_coupon\Annotation\UbercartCoupon;
use Drupal\uc_coupon\Entity\Coupon;
use Drupal\uc_coupon\CouponPluginInterface;

/**
 * Manages discovery and instantiation of coupons.
 */
class CouponManager extends DefaultPluginManager {

  /**
   * Constructs a CouponManager object.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Ubercart/Coupon', $namespaces, $module_handler, CouponPluginInterface::class, UbercartCouponMethod::class);
    $this->alterInfo('uc_coupon');
    $this->setCacheBackend($cache_backend, 'uc_coupons');
  }

  /**
   * Returns an instance of the coupon plugin for a specific order.
   *
   * @param \Drupal\uc_order\OrderInterface $order
   *   The order from which the plugin should be instantiated.
   *
   * @return \Drupal\uc_coupon\CouponPluginInterface
   *   A fully configured plugin instance.
   */
  public function createFromOrder(OrderInterface $order) {
    return Coupon::load($order->getCouponId())->getPlugin();
  }

  /**
   * Populates a key-value pair of available coupons.
   *
   * @return array
   *   An array of coupon labels, keyed by ID.
   */
  public function listOptions() {
    $options = array();
    foreach ($this->getDefinitions() as $key => $definition) {
      $options[$key] = $definition['name'];
    }
    return $options;
  }

}
