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
    // Define quantity of the forms for setting parameters values.
    $szParams = $this->getOption('quantity_params', 1);
    $arParams = $this->getDefaults();
    
    // Embed form by the each parameter.
    for($i = 0; $i < $szParams; $i++) {

      // If parameter has value - embed form with parameters for configure.
      if (isset($arParams[$i]) && is_array($arParams[$i])) {
        $this->embedForm($i, new FxShopFilterNewRuleParamForm($arParams[$i]), array('in_logic_as_hidden' => (0 == $i)));
      }
      // If parameter has not value - embed form without parameters.
      else {
        $this->embedForm($i, new FxShopFilterNewRuleParamForm(null, array('in_logic_as_hidden' => (0 == $i))));
      }
    }
  }
}
