<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopFilterNewParamValueForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define filter_id field.
    $this->setWidget('value', new sfWidgetFormInputText());
    $this->setValidator('value', new sfValidatorString(array('required' => false)));
  }
}