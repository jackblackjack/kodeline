<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class FxShopItemNewCategoryForm extends yaBaseFxShopItemCategoryForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    // Definition list of uses fields of the form.
    $this->useFields(array('id', 'title', 'annotation', 'detail', 'slug', 'parent_id'));

    // Redefine parent_id field.
    $this->setWidget('parent_id', new sfWidgetFormInputHidden());

    // Redefine annotation field.
    $this->setWidget('annotation', new jWidgetFormWysiBB());

    // Redefine detail field.
    $this->setWidget('detail', new jWidgetFormWysiBB());

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'title'       => 'Наименование',
      'annotation'  => 'Аннотация',
      'detail'      => 'Детальное описание',
      'slug'        => 'Идентификатор',
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'title'       => 'Обязательное поле для заполнения',
      'annotation'  => 'Необязательное поле для заполнения',
      'detail'      => 'Необязательное поле для заполнения',
      'slug'        => 'Уникальный идентификатор категории. Необязательное поле для заполнения'
    ));

    // Set name of widget.
    $this->getWidgetSchema()->setNameFormat('product_category[%s]');
  }
}
