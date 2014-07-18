<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewRuleParamConditionForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define list categories field.
    $this->widgetSchema['categories'] = new jWidgetFormJQuerySelect2(
      array(
        'choices_query'   => Doctrine_Core::getTable('FxShopItem')->createQuery()->where('is_category = ?', 1)
      ), 
      array()
    );
    $this->setValidator('categories', new sfValidatorPass(array('required' => false)));

    // Define logic field.
    $this->setWidget('logic', new sfWidgetFormSelectRadio(array('choices' => self::getLogicChoices())));
    $this->setValidator('logic', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getLogicChoices()))));

    // Define filter_id field.
    $this->setWidget('compare', new sfWidgetFormSelect(array('choices' => self::getCompareChoices())));
    $this->setValidator('compare', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCompareChoices()))));

    // Define embed form 'value'.
    $this->setWidget('value', new sfWidgetFormInputText());
    $this->setValidator('value', new sfValidatorPass(array('required' => true)));

    // Set name of widget.
    $this->getWidgetSchema()->setNameFormat('rule[%s]');
  }

  public static function getLogicChoices()
  {
    $i18n = sfContext::getInstance()->getI18N();
    return array('and' => $i18n->__('И', null, 'flexible-shop'), 'or' => $i18n->__('ИЛИ', null, 'flexible-shop'));
  }

  public static function getCompareChoices()
  {
    $i18n = sfContext::getInstance()->getI18N();
    return array(
      'min' => $i18n->__('больше либо равно чем', null, 'flexible-shop'),
      'max' => $i18n->__('меньше либо равно чем', null, 'flexible-shop'),
      'eq'  => $i18n->__('равно', null, 'flexible-shop'),
      'ne'  => $i18n->__('не равно', null, 'flexible-shop'),
      'le'  => $i18n->__('меньше чем', null, 'flexible-shop'),
      'ge'  => $i18n->__('больше чем', null, 'flexible-shop')
    );
  }
}