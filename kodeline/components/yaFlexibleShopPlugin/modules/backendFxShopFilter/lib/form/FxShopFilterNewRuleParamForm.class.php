<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewRuleParamForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine filter_id field.
    $this->setWidget('filter_id', new sfWidgetFormInputHidden());
    $this->setValidator('filter_id', new sfValidatorInteger(array('required' => false)));

    // Redefine component_id field.
    $this->setWidget('component_id', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'jBehaviorComponent')));
    $this->setValidator('component_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'jBehaviorComponent', 'required' => true)));

    // Redefine parameter_id field.
    $this->setWidget('parameter_id', new sfWidgetFormDoctrineChoice(array('multiple' => false, 'model' => 'jParameterableSchema')));
    $this->setValidator('parameter_id', new sfValidatorDoctrineChoice(array('multiple' => false, 'model' => 'jParameterableSchema', 'required' => true)));

    // Definition list of filter rules form.
    $szConditions = $this->getOption('quantity_conditions', 1);
    $arConditions = ($this->hasDefault('quantity_conditions') ? $this->getDefault('quantity_conditions') : array());
    $this->embedForm('conditions', new FxShopFilterNewRuleParamConditionsForm($arConditions, array('quantity_conditions' => $szConditions)));
  }
}
