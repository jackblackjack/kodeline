<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewRulesForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Generate list of forms for choose parameters of filter.
    $szParams = $this->getOption('quantity_params', 1);
    $arParams = $this->getDefaults();
    
    for($i = 0; $i < $szParams; $i++)
    {
      if (isset($arParams[$i]) && is_array($arParams[$i]))
      {
        $this->embedForm($i, new FxShopFilterNewRuleParamForm($arParams[$i]));
      }
      else
      {
        $this->embedForm($i, new FxShopFilterNewRuleParamForm());
      }
    }
  }
}
