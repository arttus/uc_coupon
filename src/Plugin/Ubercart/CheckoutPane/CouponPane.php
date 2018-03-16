<?php

namespace Drupal\uc_coupon\Plugin\Ubercart\CheckoutPane;

use Drupal\Component\Plugin\Exception\PluginException;
use Drupal\Component\Utility\Html;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\uc_cart\CheckoutPanePluginBase;
use Drupal\uc_order\OrderInterface;
use Drupal\uc_coupon\Entity\Coupon;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Ajax\InvokeCommand;
/**
 * Allows the user to add coupon and preview the line items.
 *
 * @CheckoutPane(
 *   id = "coupon",
 *   title = @Translation("Coupon"),
 *   weight = 6
 * )
 */


class CouponPane extends CheckoutPanePluginBase implements ContainerFactoryPluginInterface
{
    
    
    /**
     * Constructs a CouponPane object.
     *
     * @param array $configuration
     *   A configuration array containing information about the plugin instance.
     * @param string $plugin_id
     *   The plugin ID for the plugin instance.
     * @param array $plugin_definition
     *   The plugin implementation definition.
     *   The coupon manager.
     */
    
    
    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition)
    {
        return new static($configuration, $plugin_id, $plugin_definition);
    }
    
    /**
     * {@inheritdoc}
     */
    public function view(OrderInterface $order, array $form, FormStateInterface $form_state)
    {

        $tempstore = \Drupal::service('user.private_tempstore')->get('uc_coupon');
        $coupon    = $tempstore->get('coupon');
        $contents['coupon_status']['#markup'] = '<div id="coupon-status"></div>';
        $contents['coupon_status']['#weight']=3;
        $contents['coupon']['#prefix'] = '<div id ="coupon-review">';
        $contents['coupon']['#suffix'] = '</div>';
        
            $contents['coupon']['code']['#suffix'] = '</div>';
            $contents['remove']          = array(
                '#type' => 'button',
                '#value' => 'Remove',
                '#submit' =>array($this, 'removeCoupon'),
                '#ajax' => array(
                    'callback' => array(
                        $this,
                        'removeCoupon'
                    ),
                    'wrapper' => 'coupon-review'
                ),
                '#limit_validation_errors' => array()
            );
            
       
            $contents['coupon']['coupon']  = array(
                '#type' => 'textfield',
                '#title' => $this->t('Coupon'),
                '#default_value' => ''
                
            );
            
            $contents['add']     = array(
                '#type' => 'button',
                '#value' => 'Apply',
                '#ajax' => array(
                    'callback' => array(
                        $this,
                        'ajaxRender'
                    ),
                    'wrapper' => 'coupon-review'
                ),
                '#limit_validation_errors' => array()
            );
        
        if ($coupon) {
            $discount                              = $coupon->getDiscountValue(uc_coupon_get_subtotal()) * -1;
            $contents['coupon']['code']['#prefix'] = '<div class="coupon-code-applied" ><b>Coupon: </b>' . $coupon->getCode() . '</div><div class="display-none margin-right">';
            hide($contents['coupon']['coupon']);
            $contents['add']['#attributes']['class'][] = 'display-none';
          }
          else {
            hide($contents['coupon']['code']);
            $contents['remove']['#attributes']['class'][] = 'display-none';
          }
        if ($form_state->getTriggeringElement()) {
            $this->prepare($order, $form, $form_state);
        }
        
        
        
        
        return $contents;
    }
    
    
    
    /**
     * {@inheritdoc}
     */
    public function review(OrderInterface $order)
    {
        
    
    }
    
    public function removeCoupon(array $form, FormStateInterface $form_state)
    {
        $tempstore = \Drupal::service('user.private_tempstore')->get('uc_coupon');
        $coupon = $tempstore->get('coupon');
        if($coupon){
          $message='<div class="success">Coupon is removed.</div>';
             hide($form['panes']['coupon']['coupon']['code']);
             show($form['panes']['coupon']['coupon']['coupon']);

        
        }

         $tempstore->delete('coupon');
        
        $order  = $form_state->get('order');
        $order->save();
        $response = new AjaxResponse();
        $response->addCommand(new ReplaceCommand('#payment-preview', drupal_render($form['panes']['payment']['line_items'])));
        
        $response->addCommand(new ReplaceCommand('#coupon-review', drupal_render($form['panes']['coupon']['coupon'])));
         $response->addCommand(new ReplaceCommand('#coupon-status', '<div id="coupon-status">'.$message.'</div>'));
         $response->addCommand(new InvokeCommand('#edit-panes-coupon-remove', 'addClass', array('display-none')));
        $response->addCommand(new InvokeCommand('#edit-panes-coupon-add', 'removeClass', array('display-none')));
        return $response;
    }
    public function applyCoupon(array $form, FormStateInterface $form_state){
       
      
    }
    /**
     * Ajax callback to re-render the coupon pane.
     */
    public function ajaxRender(array $form, FormStateInterface $form_state)
    {
         $code = $form_state->getValue(array(
            'panes',
            'coupon',
            'coupon',
            'coupon'
        ));
        if ($code) {
            $order  = $form_state->get('order');
            $coupon = uc_coupon_is_valid($code);
            if ($coupon) {
                $message='<div class="success">Coupon is applied.</div>';
                $tempstore = \Drupal::service('user.private_tempstore')->get('uc_coupon');
                $tempstore->set('coupon', $coupon);
                $order->save();
                
            } else {
                $message='<div class="error">Sorry, that coupon is invalid.</div>';
            }
        }
        $response = new AjaxResponse();

        $response->addCommand(new ReplaceCommand('#payment-preview', render($form['panes']['payment']['line_items'])));
        if($coupon){
          $discount                              = $coupon->getDiscountValue(uc_coupon_get_subtotal()) * -1;
            $form['panes']['coupon']['coupon']['code']['#prefix'] = '<div class="coupon-code-applied" ><b>Coupon: </b>' . $coupon->getCode() . '</div><div class="display-none margin-right">';
             show($form['panes']['coupon']['coupon']['code']);

             hide($form['panes']['coupon']['coupon']['coupon']);
        }
        $response->addCommand(new ReplaceCommand('#coupon-review', drupal_render($form['panes']['coupon']['coupon'])));
        $response->addCommand(new ReplaceCommand('#coupon-status', '<div id="coupon-status">'.$message.'</div>'));
        if($coupon){
        $response->addCommand(new InvokeCommand('#edit-panes-coupon-add', 'addClass', array('display-none')));
        $response->addCommand(new InvokeCommand('#edit-panes-coupon-remove', 'removeClass', array('display-none')));
      }
   
        return $response;
    }
 
    
}
