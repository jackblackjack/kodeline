<?php
/**
 * Форма добавления значения для
 * поля типа "прикрепление" (attach) для изображения.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueImageForm extends BaseParameterableParamValueImageForm
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
      //'path'        => ((isset(self::$configUpload['draft']) && isset(self::$configUpload['draft']['path'])) ? rtrim(self::$configUpload['draft']['path'], DIRECTORY_SEPARATOR) : null),
      'required'    => false,
      'mime_types'  => 'web_images'
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