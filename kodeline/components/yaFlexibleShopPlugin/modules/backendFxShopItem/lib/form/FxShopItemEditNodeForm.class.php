<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopItemEditNodeForm extends yaBaseFxShopItemForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call the parent method.
    parent::configure();

    // Definition list of uses fields of the form.
    $this->useFields(array('id', 'title', 'annotation', 'detail', 'is_active', 'parent_id'));

    // Redefine field "is_active".
    $this->setWidget('is_active', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_active', new sfValidatorBoolean());

    // Redefine field "parent_id".
    $this->setWidget('parent_id', new sfWidgetFormInputHidden());

    // Redefine field "annotation".
    $this->setWidget('annotation', new jWidgetFormWysiBB());

    // Redefine field "detail".
    $this->setWidget('detail', new jWidgetFormWysiBB());
  }
}