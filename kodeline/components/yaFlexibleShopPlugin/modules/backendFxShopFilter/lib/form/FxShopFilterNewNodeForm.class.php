<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewNodeForm extends BaseFxShopEditFilterForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    // Definition list of uses fields of the form.
    $this->useFields(array('id', 'title', 'annotation', 'detail', 'is_active', 'parent_id'));

    // Redefine is_active field.
    $this->setWidget('is_active', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_active', new sfValidatorBoolean());

    // Redefine parent_id field.
    $this->widgetSchema['parent_id'] = new jWidgetFormJQuerySelect2(
      array(
        'placeholder'     => 'Родительский фильтр',
        'choices_query'   => Doctrine_Core::getTable($this->getModelName())->createQuery()
//        'url'         => $this->getUrl(sfContext::getInstance()->getRouting()->generate('backend_product_ajax_categories', array('action' => 'list'))  . '?name=%name%'),
      ), 
      array()
    );

    // Definition list of filter rules form.
    $szRules = $this->getOption('quantity_params', 1);
    $arRules = ($this->hasDefault('quantity_params') ? $this->getDefault('quantity_params') : array());
    $this->embedForm('rules', new FxShopFilterNewRulesForm($arRules, array('quantity_params' => $szRules)));
  }
}
