<?php
/**
 * Базовая форма добавления значения для
 * поля типа "прикрепление" (attach) для файлов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueFileForm extends BaseParameterableParamValueForm
{
  /**
   * Configuration of image's paths and dimensions for upload.
   * @static array
   */
  public static $configUpload = array();
  
  /**
   * Fetch configuration.
   */
  public static function fetchConfiguration()
  {
    if (null !== ($configStore = sfConfig::get('app_behaviorParameterable_images_store', null)))
    {
      if (! empty($configStore['use_types']))
      {
        foreach($configStore['use_types'] as $type)
        {
          if (empty($configStore[$type])) {
            // @todo: 404 exception?
            throw new sfException(sprintf('Type "%s" for "app_user_data_image_store" is not found!', $type));
          }

          self::$configUpload[$type] = $configStore[$type];
          self::$configUpload[$type]['rel_path'] = rtrim(substr($configStore[$type]['path'], strlen(sfConfig::get('sf_web_dir'))), '/') . '/';
        }
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    parent::configure();
    self::fetchConfiguration();

    // Виджет для загрузки файла-аватара.
    /*
    $sImageSrc = (array_key_exists('picture', $arProfile) ? 
      self::$configUpload['draft']['rel_path'] . $arProfile['picture'] : 
      '/images/concourse-start-ava.png');
    */

    $sImageSrc = null;
    
    // Redefine title field.
    $this->setWidget('value', new sfWidgetFormInputFileEditable(array(
      'edit_mode'     => false,
      'file_src'      => $sImageSrc,
      'is_image'      => true,
      'delete_label'  => 'Удалить',
    )));

    $this->setValidator('value', new sfValidatorFile(array(
      'path'        => ((isset(self::$configUpload['draft']) && isset(self::$configUpload['draft']['path'])) ? rtrim(self::$configUpload['draft']['path'], DIRECTORY_SEPARATOR) : null),
      'required'    => false,
      'mime_types'  => 'web_images'
    )));
  }
}