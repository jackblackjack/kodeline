<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewRuleParamConditionsForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Generate list of forms for choose parameter's rules of filter.
    $szConditions = $this->getOption('quantity_conditions', 1);
    $arConditions = $this->getDefaults();
    
    for($i = 0; $i < $szConditions; $i++)
    {
      if (isset($arConditions[$i]) && is_array($arConditions[$i]))
      {
        $this->embedForm($i, new FxShopFilterNewRuleParamConditionForm($arConditions[$i]));
      }
      else
      {
        $this->embedForm($i, new FxShopFilterNewRuleParamConditionForm());
      }
    }
  }
}