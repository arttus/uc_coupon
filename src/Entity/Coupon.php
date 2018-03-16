<?php

namespace Drupal\uc_coupon\Entity;

use Drupal\Core\Entity\EntityChangedTrait;
use Drupal\uc_order\OrderInterface;
use Drupal\uc_coupon\CouponInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Drupal\Core\Field\BaseFieldDefinition;
use Drupal\Core\Entity\ContentEntityBase;

/**
 * Defines the coupon entity class.
 *
 * @ContentEntityType(
 *   id = "uc_coupon",
 *   label = @Translation("Coupon"),
 *   label_singular = @Translation("coupon"),
 *   label_plural = @Translation("coupons"),
 *   label_count = @PluralTranslation(
 *     singular = "@count coupon",
 *     plural = "@count coupons",
 *   ),
 *   base_table = "uc_coupons",
 *   module = "uc_coupon",
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\uc_coupon\CouponListBuilder",
 *     "views_data" = "Drupal\uc_coupon\CouponViewsData",
 *     "form" = {
 *       "delete" = "Drupal\uc_coupon\Form\CouponDeleteForm",
 *       "add" = "Drupal\uc_coupon\Form\CouponForm",
 *       "edit" = "Drupal\uc_coupon\Form\CouponForm",
 *     }
 *   },
 *   admin_permission = "administer store",
 *   entity_keys = {
 *     "id" = "id",
 *     "code" = "code",
 *   },
 *   links = {
 *     "canonical" = "/admin/store/coupon/{uc_coupon}",
 *     "edit-form" = "/admin/store/coupon/{uc_coupon}/edit",
 *     "enable" = "/admin/store/coupon/{uc_coupon}/enable",
 *     "disable" = "/admin/store/coupon/{uc_coupon}/disable",
 *     "delete-form" = "/admin/store/coupon/{uc_coupon}/delete",
 *     "collection" = "/admin/store/coupons"
 *   }
 * )
 */


class Coupon extends ContentEntityBase implements CouponInterface {
  use EntityChangedTrait;
  /**
   * The coupon ID.
   *
   * @var string
   */
  protected $id;
  /**
   * The coupon code
   * @var string
   */
  protected $code;

  /**
   * The coupon label.
   *
   * @var string
   */
  protected $type;
  
  public function setCouponType($coupon_type_arg) {
    $this->type = $coupon_type_arg;
    return $this;
  }


  /**
   *  
   */
  public function label(){
    return $this->code;
  }
  public function getCouponType(){
    return $this->type;
  }

  public function getCode(){
    return $this->code;
  }
  public function getMaxUses(){
    if(!$this->max_uses->value || $this->max_uses->value==0){
      return 'Unlimited';
    }
    else {
      return $this->max_uses->value;
    }
  }
  public function getDiscount(){
    if($this->type == "percent"){
      return $this->value->value.' %';
    }
    else {
      return uc_currency_format($this->value->value);
    }
  }
  public function getDiscountValue($total){
    if($this->type == "percent"){
      return ($total*($this->value->value/100));
    }
    else {
      return $this->value->value;
    }
  }
  public function isActive(){

    if($this->status->value==1){
      return true;
    }
    else {
      return false;
    }

  }
  public function enable(){
    $this->status->value = 1;

  }
  public function disable(){
$this->status->value = 0;
  }
  

  public static function baseFieldDefinitions(EntityTypeInterface $entity_type) {

    // Name field for the contact.
    // We set display options for the view as well as the form.
    // Users with correct privileges can change the view and edit configuration.
    $fields['id'] = BaseFieldDefinition::create('integer')
      ->setLabel(t('Coupon ID'))
      ->setDescription(t('The coupon ID.'))
      ->setReadOnly(TRUE)
      ->setSetting('unsigned', TRUE);
    $fields['code'] = BaseFieldDefinition::create('string')
      ->setLabel(t('Code'))
      ->setDisplayOptions('form', array(
        'type' => 'string_textfield',
        'weight' => -6,
      ))->addConstraint('UniqueField')->setRequired(true);

          $fields['type'] = BaseFieldDefinition::create('list_string')
      ->setLabel(t('Type'))
      ->setSettings(array(
        'allowed_values' => array(
          'percent' => 'Percent',
          'fixed' => 'Fixed',
        ),
        'weight'=> -3,
      ))
      ->setDisplayOptions('form', array(
        'type' => 'options_select',
        'weight' => -2,
      ))->setDefaultValue(['value'=>'percent'])->setRequired(true);
      $fields['value'] = BaseFieldDefinition::create('decimal')
      ->setLabel(t('Value'))
      ->setDisplayOptions('form', array(
        'type' => 'decimal_textfield',
        'weight' => -1,
      ))->setRequired(true);
      
      $fields['status'] = BaseFieldDefinition::create('boolean')
      ->setLabel(t('Active?'))->setDisplayOptions('form', array(
        'type' => 'boolean_checkbox',
        'weight' => 10,
      ))->setDefaultValue(['value' => 1]);

       $fields['created'] = BaseFieldDefinition::create('created')
      ->setLabel(t('Created'))
      ->setDescription(t('The time that the entity was created.'));

    $fields['changed'] = BaseFieldDefinition::create('changed')
      ->setLabel(t('Changed'))
      ->setDescription(t('The time that the entity was last edited.'));

     return $fields;
    }
}
