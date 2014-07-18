<?php
/**
 * Форма добавления значения для
 * поля типа "прикрепление" (attach) для документов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueDocumentForm extends BaseParameterableParamValueDocumentForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    parent::configure();

    self::fetchConfiguration();

    // Виджет для загрузки файла-аватара.
    $sImageSrc = null;
    
    // Redefine value field.
    $this->setWidget('value', new jWidgetFormInputFilePlupload(array(
      'form'          => __CLASS__,
      'edit_mode'     => false,
      'file_src'      => $sImageSrc,
      'is_image'      => true,
      'delete_label'  => 'Удалить',
    )));

    // Redefine value validator.
    $this->setValidator('value', new sfValidatorFile(array(
      'path'        => sfConfig::get('sf_upload_dir'),
      'required'    => false
    )));
  }

  /**
   * @inheritDoc
   */
  public function getJavaScripts()
  {
    return array_merge(
              parent::getJavaScripts(),
              array('jDoctrineActAsBehaviorPlugin/js/libs/plupload/plupload.full.js')
    );
  }
}