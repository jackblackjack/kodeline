<?php
/**
 * yaContentStorageHelper
 *
 * @package     yaCorePlugin
 * @subpackage  lib
 * @category    helper
 * @author      chugarev
 * @version     $Id$
 */
class yaContentStorageHelper
{
  /**
   * Name of the parameter for content storages (in app.yml).
   */
  private static $appConfigPrefixPath = 'app_content_storages';

  /**
   * Current configuration of the storages.
   */
  private static $arStoreConf = array();  
    

  /**
   * Listens to the routing.load_configuration event.
   *
   * @param sfEvent An sfEvent instance
   */
  public static function getStorageConfig($storeName)
  {
    // Определение имени конфигурации хранилища файлов.
    $configStoreName = sprintf('%s_%s', self::$appConfigPrefixPath, (! empty($storeName) ? $storeName : 'default_store'));

    // Если конфигурация уже существует - возврат.
    if (! empty(self::$arStoreConf[$configStoreName])) return self::$arStoreConf[$configStoreName];
    
    // Если конфигурация не найдена вызов sfException.
    if (null == ($configStore = sfConfig::get($configStoreName, null)))
    {
      throw new sfException(sprintf('Configuration "%s" is not found (%s::%s)!', $configStoreName, __CLASS__, __METHOD__));
    }

    // Если список используемых типов обработки файлов не найден - вызов sfException.
    if (empty($configStore['types']))
    {
      throw new sfException(sprintf('Configuration types of "%s" is not found (%s::%s)!', $configStoreName, __CLASS__, __METHOD__));
    }

    // Подготовка типов конфигурации файлов.
    $sKeyTemporary = null;
    $sKeyDefault = null;
    foreach($configStore['types'] as $typeName => $typeConfig)
    {
      // Тип обработки по-умолчанию.
      if (empty($sKeyDefault) && isset($typeConfig['is_default']) && $typeConfig['is_default'])
      {
        $sKeyDefault = $typeName;
        self::$arStoreConf[$configStoreName]['default'] = $typeConfig;
        self::$arStoreConf[$configStoreName]['default']['rel_path'] = rtrim(substr($typeConfig['path'], strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
      }

      // Тип обработки для временного хранения.
      if (empty($sKeyTemporary) && isset($typeConfig['is_temporary']) && $typeConfig['is_temporary'])
      {
        $sKeyTemporary = $typeName;
        self::$arStoreConf[$configStoreName]['temporary'] = $typeConfig;
        self::$arStoreConf[$configStoreName]['temporary']['rel_path'] = rtrim(substr($typeConfig['path'], strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
      }

      // Попытка создать директории хранилища, если их нет.
      // TODO: следует перенести эту функциональность в класс хранилища.
      if (! empty($configStore['autocreate']) && $configStore['autocreate'])
      {
        if (! empty($typeConfig['autocreate']) && ! $typeConfig['autocreate']) continue;

        if (array_key_exists('path', $typeConfig))
        {
          if (! is_dir($typeConfig['path']))
          {
            // Define create flags for create directory.
            $chmodFlags = (! empty($configStore['autocreate_chmod']) ? $configStore['autocreate_chmod'] : 0775);
            if (! empty($typeConfig['autocreate_chmod'])) $chmodFlags = $typeConfig['autocreate_chmod'];

            // Create directories.
            mkdir($typeConfig['path'], $chmodFlags, true);
          }
        }
      }

      // Save config of the type.
      self::$arStoreConf[$configStoreName][$typeName] = $typeConfig;
      self::$arStoreConf[$configStoreName][$typeName]['rel_path'] = rtrim(substr($typeConfig['path'], strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    // Save list of the available types for storage.
    $arTypes = array_keys($configStore['types']);
    self::$arStoreConf[$configStoreName]['available_types'] = array_unique(array_merge($arTypes, array('default')));

    // Set default type if it not set.
    if (empty($sKeyDefault) && empty(self::$arStoreConf[$configStoreName]['default']))
    {
      $firstTypeName = array_shift($arTypes);
      self::$arStoreConf[$configStoreName]['default'] = self::$arStoreConf[$configStoreName][$firstTypeName];
      self::$arStoreConf[$configStoreName][$firstTypeName]['is_default'] = true;
    }

    // Set temporary type if it not set.
    if (empty($sKeyTemporary) && empty(self::$arStoreConf[$configStoreName]['temporary']))
    {
      self::$arStoreConf[$configStoreName]['temporary']['path'] = sfConfig::get('sf_upload_dir');
      self::$arStoreConf[$configStoreName]['temporary']['rel_path'] = rtrim(substr(sfConfig::get('sf_upload_dir'), strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
    }

    unset($configStore);
    return self::$arStoreConf[$configStoreName];
  }

  /**
   * Retrieve yaContentStorage class.
   * TODO: удалить true в конце (перенести возможность создания новых директорий в конфигурационный файл).
   */
  public static function getStorage($storeName)
  {
    return new yaContentStorage(self::getStorageConfig($storeName), true);
  }
}
