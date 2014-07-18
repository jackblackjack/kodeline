<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopListEditNodeForm extends yaBaseFxShopListForm
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

    // Redefine annotation field.
    $this->setWidget('annotation', new jWidgetFormWysiBB());

    // Redefine detail field.
    $this->setWidget('detail', new jWidgetFormWysiBB());

    // Redefine parent_id field.
    $this->setWidget('parent_id', new sfWidgetFormInputHidden());

    /*
    // Redefine parent_id field.
    $this->widgetSchema['parent_id'] = new jWidgetFormJQuerySelect2(
      array(
        'placeholder'     => 'Родительская категория',
        'choices_query'   => Doctrine_Core::getTable($this->getModelName())->createQuery()->andWhere('is_category = ?', 1)
      ), 
      array()
    );
    */
  }
}
