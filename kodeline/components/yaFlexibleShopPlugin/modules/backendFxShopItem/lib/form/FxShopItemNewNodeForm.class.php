<?php
/**
 * Class for create new node of FxShop tree.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     Alexey Chugarev <chugarev@gmail.com>
 * @version    $Id$
 */
class FxShopItemNewNodeForm extends yaBaseFxShopItemForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent method.
    parent::configure();

    // Definition list of uses fields of the form.
    $this->useFields(array('id', 'title', 'annotation', 'detail', 'parent_id'));

    // Redefine annotation field.
    $this->setWidget('annotation', new jWidgetFormWysiBB(array('imgupload' => true, 
      'img_uploadurl' => sfContext::getInstance()->getRouting()->generate('j_file_attach_post_ajax', 
        array('form' => __CLASS__, 'field' => 'annotation', 'sf_format' => 'json', 'editor' => 'wysibb'), true))));

    // Redefine detail field.
    $this->setWidget('detail', new jWidgetFormWysiBB(array('imgupload' => true, 
      'img_uploadurl' => sfContext::getInstance()->getRouting()->generate('j_file_attach_post_ajax', 
        array('form' => __CLASS__, 'field' => 'detail', 'sf_format' => 'json', 'editor' => 'wysibb'), true))));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'title'       => 'Наименование',
      'annotation'  => 'Аннотация',
      'detail'      => 'Детальное описание'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'title'       => 'Основное название, которое выводится в списках',
      'annotation'  => 'Необязательное поле для заполнения',
      'detail'      => 'Необязательное поле для заполнения'
    ));

    // Redefine parent_id field.
    $this->setWidget('parent_id', new sfWidgetFormInputHidden());
  }
}