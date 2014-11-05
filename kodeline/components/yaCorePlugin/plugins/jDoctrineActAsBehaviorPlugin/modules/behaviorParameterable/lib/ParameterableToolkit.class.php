<?php
/**
 * Parameterable toolkit for managment parameters.
 * Use extended tables.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    toolkit
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableToolkit
{
  /**
   * Name of the component for use for save complex (booleans) values.
   * 
   * @var string
   * @constant
   */
  const COMPLEX_TYPE_COMPONENT_NAME = "jParameterableIntegerValue";

  /**
   * Set values for component's object.
   * 
   * @param string $componentName Name of the component.
   * @param array $arDataWithValues Array of the objects and it extended parameters values.
   */
  public static function setExtendedParametersValues($sComponentName, array $arDataWithValues)
  {
    // If object has not parameters - return true.
    if (! count($arDataWithValues)) return true;

    // Create record of the component.
    $record = Doctrine::getTable($sComponentName)->getRecordInstance();

    // Fetch invoker component id.
    $iComponentId = $record->fetchComponentId($sComponentName);

    foreach($arDataWithValues as $iObject => $arParameters)
    {
      // Fetch id keys for object's parameters.
      $arParametersId = array_keys($arParameters);

      // Fetch parameters schema by all parameters for save.
      $arParametersSchema =  Doctrine_Core::getTable('jParameterableSchema')->createQuery()
                                ->whereIn('id', $arParametersId)
                                ->indexBy('id')
                                ->fetchArray();

      $szParametersId = count($arParametersId);

      for ($i = 0; $i < $szParametersId; $i++)
      {
        $iParameter = $arParametersId[$i];
        $mValue = $arParameters[$arParametersId[$i]];

        switch($arParametersSchema[$iParameter]['type'])
        {
          //
          // Processing values for 
          // parameters which supports values list:
          // enum, checkbox, radio, library
          //
          case PluginjParameterableSchema::ENUM_TYPE_NAME:
          case PluginjParameterableSchema::RADIO_TYPE_NAME:
          case PluginjParameterableSchema::LIBLINK_TYPE_NAME:
          case PluginjParameterableSchema::CHECKBOX_TYPE_NAME:

            // Convert values to array.
            if (! is_array($mValue)) $mValue = array($mValue);

            try {

              // Write info to log.
              sfContext::getInstance()->getLogger()->info("Create transaction for complex values.");

              // Create a transaction.
              $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
              $connection->beginTransaction();

              // Remove previous saved parameter's values.
              $values = Doctrine::getTable(self::COMPLEX_TYPE_COMPONENT_NAME)->createQuery()
                  ->where('component_id = ?', $iComponentId)
                  ->andWhere('object_id = ?', $iObject)
                  ->andWhere('parameter_id = ?', $iParameter)
                  ->execute();

              // Create collection for save values.
              $collection = new Doctrine_Collection(self::COMPLEX_TYPE_COMPONENT_NAME);

              // Save each value for type.
              foreach($mValue as $mValue) {
                
                $valueSchema = new jParameterableIntegerValue();
                $valueSchema['component_id'] = $iComponentId;
                $valueSchema['object_id'] = $iObject;
                $valueSchema['parameter_id'] = $iParameter;
                $valueSchema['value'] = (int) $mValue;

                $collection->add($valueSchema);
              }

              //$values->replace($collection);
              $values->delete();

              // Save new collection values for list.
              //$values->save();
              $collection->save();

              // Commit transaction for save new values.
              $connection->commit();
            }
            // Catch any exceptions.
            catch(Exception $exception) {

              // Write info to log.
              sfContext::getInstance()->getLogger()
                    ->err(sprintf("Cannot create complex type values. Error: %s", $exception->getMessage()));

              // Rollback current transaction.
              $connection->rollback();

              // Rethrow exception.
              throw $exception;

              // Return false of processing.
              return false;
            }
          break;

          //
          // Processing values for other types parameters:
          // integer, decimal, string, time, timestamp
          //
          default:
            // Define component name for save values.
            $sComponentValueName = 'jParameterable' . sfInflector::camelize($arParametersSchema[$iParameter]['type']) . 'Value';

            // Write info to log.
            sfContext::getInstance()->getLogger()
                  ->info(sprintf("Selected component for save value: %s", $sComponentValueName));

            // Fetch exists values for object's parameter.
            $recordExists = Doctrine::getTable($sComponentValueName)->createQuery()
                                ->where('component_id = ?', $iComponentId)
                                ->andWhere('object_id = ?', $iObject)
                                ->andWhere('parameter_id = ?', $iParameter)
                                ->fetchOne();

            // If record with value is exists - save new value.
            if ($recordExists) {

              // Write info to log.
              sfContext::getInstance()->getLogger()
                  ->info(sprintf("Prevoius value is: %s, new set: %s", $recordExists->get('value'), $mValue));

              // Save new value in the exists record.
              $recordExists->set('value', $mValue);
              $recordExists->save();
            }
            else {

              // Write info to log.
              sfContext::getInstance()->getLogger()
                  ->info(sprintf("Create new value: %s", $mValue));

              // Create new parameter's value.
              $newValue = new $sComponentValueName();
              $newValue['component_id'] = $iComponentId;
              $newValue['object_id'] = $iObject;
              $newValue['parameter_id'] = $iParameter;
              $newValue['value'] = $mValue;
              $newValue->save();
            }
          break;
        }
      }

      return true;
    }

    // By default.
    return false;
  }

  /**
   * Добавляет расширенный параметр для объекта компонента.
   * 
   * @param string $modelName Имя компонента (модели)
   * @param array $options Параметры создания параметра
   * 
   * @return jParameterableSchema|null
   */
  public static function createExtendedParameter($modelName, array $options)
  {
    // Define method name to get options for parameter.
    $optionsMethodName = 'getExtended' . sfInflector::camelize($options['type']) . 'ParameterOptions';

    // Check create parameter method exists.
    if (! method_exists('ParameterableToolkit', $optionsMethodName))
    {
      throw new sfException(sprintf(sfContext::getInstance()
        ->getI18N()->__('Метод "ParameterableToolkit::%s" не найден!', null, 'behavior-parameterable'), $optionsMethodName));
    }

    // Initiate new record of the target table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    if ($record instanceof Doctrine_Record)
    {
      // Add new parameter definition.
      $parameter = $record->createExtendedParameter(
        // Наименование параметра.
        $options['name'],

        // Параметры создаваемого поля.
        call_user_func_array(array('ParameterableToolkit', $optionsMethodName), array($options, $modelName)),
               
        // Родительская группа создаваемого параметра или группы параметров.
        (isset($options['parent_id']) && 0 < (int) $options['parent_id'] ? $options['parent_id'] : null),
        
        // Заголовок поля.
        array(
          sfContext::getInstance()->getUser()->getCulture() => array(
            'title' => (isset($options['title']) ? $options['title'] : 'Unknown'),
            'hint'  => (isset($options['hint']) ? $options['hint'] : null)
          )
        ),

        // Принадлежность к узлу дерева текущей модели.
        (isset($options['belong']) && strlen($options['belong']) ? $options['belong'] : null)
      );

      // Define method name to execute extended actions for created parameter.
      $postMethodName = 'post' . sfInflector::camelize($options['type']) . 'Execute';

      // Check create parameter method exists.
      if (method_exists('ParameterableToolkit', $postMethodName))
      {
        call_user_func_array(array('ParameterableToolkit', $postMethodName), array($parameter, $options, $modelName));
      }

      // Return parameter record.
      return $parameter;
    }

    return null;
  }

  /**
   * Возвращает список опций для создания параметра типа число (number).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedNumberParameterOptions(array $options, $modelName = null)
  {
    if (isset($options['is_decimal']) && $options['is_decimal'])
    {
      return array(
        'type'        => PluginjParameterableSchema::DECIMAL_TYPE_NAME,
        'length'      => (isset($options['length']) ? $options['length'] : PluginjParameterableSchema::DECIMAL_TYPE_LENGTH),
        'scale'       => (isset($options['scale']) ? $options['scale'] : PluginjParameterableSchema::DECIMAL_TYPE_SCALE),
        'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
        'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
        'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
      );
    }

    return array(
      'type'        => PluginjParameterableSchema::INTEGER_TYPE_NAME,
      'length'      => (isset($options['length']) ? $options['length'] : PluginjParameterableSchema::DECIMAL_TYPE_LENGTH),
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Возвращает список опций для создания параметра типа текст (string).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedStringParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::STRING_TYPE_NAME,
      'length'      => (isset($options['length']) ? $options['length'] : null),
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Возвращает список опций для создания параметра типа список (enum).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedEnumParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::ENUM_TYPE_NAME,
      'length'      => (isset($options['length']) ? $options['length'] : null),
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_dynamic'  => (isset($options['is_dynamic']) ? (bool) $options['is_dynamic'] : false)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "enum" (список).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postEnumExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    if (! empty($options['items']))
    {
      $arItems = array_filter($options['items']);
      if (! count($arItems)) continue;

      $itemsCollection = new Doctrine_Collection('jParameterableStringValue');
      foreach ($arItems as $item)
      {
        $itemRecord = new jParameterableStringValue();
        $itemRecord->set('component_name', $modelName);
        $itemRecord->set('object_id', $parameter['belong']);
        $itemRecord->set('parameter_id', $parameter['id']);
        $itemRecord->set('value', $item);
        $itemsCollection->add($itemRecord);
      }
      $itemsCollection->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "date" (дата).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedDateParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::DATE_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "date" (дата).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postDateExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['min']))
    {
      $minParamLimit = new jParameterableOption();
      $minParamLimit->set('parameter_id', $parameter['id']);
      $minParamLimit->set('name', 'min');
      $minParamLimit->set('value', $options['min']);
      $minParamLimit->save();
    }

    // Set maximum parameter limit.
    if (! empty($options['max']))
    {
      $maxParamLimit = new jParameterableOption();
      $maxParamLimit->set('parameter_id', $parameter['id']);
      $maxParamLimit->set('name', 'max');
      $maxParamLimit->set('value', $options['max']);
      $maxParamLimit->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "time" (время).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedTimeParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::TIME_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "time" (время).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postTimeExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['min']))
    {
      $minParamLimit = new jParameterableOption();
      $minParamLimit->set('parameter_id', $parameter['id']);
      $minParamLimit->set('name', 'min');
      $minParamLimit->set('value', $options['min']);
      $minParamLimit->save();
    }

    // Set maximum parameter limit.
    if (! empty($options['max']))
    {
      $maxParamLimit = new jParameterableOption();
      $maxParamLimit->set('parameter_id', $parameter['id']);
      $maxParamLimit->set('name', 'max');
      $maxParamLimit->set('value', $options['max']);
      $maxParamLimit->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "checkbox" (флаг).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedCheckboxParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::CHECKBOX_TYPE_NAME,
      'default'     => (isset($options['is_enabled']) ? (int) $options['is_enabled'] : 0),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Возвращает список опций для создания параметра типа "radio" (переключатель).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedRadioParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::RADIO_TYPE_NAME,
      'default'     => (isset($options['is_enabled']) ? (int) $options['is_enabled'] : 0),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_dynamic'  => (isset($options['is_dynamic']) ? (bool) $options['is_dynamic'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "radio" (переключатель).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postRadioExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    if (! empty($options['items']))
    {
      $arItems = array_filter($options['items']);
      if (! count($arItems)) continue;

      $itemsCollection = new Doctrine_Collection('jParameterableStringValue');
      foreach ($arItems as $item)
      {
        $itemRecord = new jParameterableStringValue();
        $itemRecord->set('component_name', $modelName);
        $itemRecord->set('object_id', $parameter['belong']);
        $itemRecord->set('parameter_id', $parameter['id']);
        $itemRecord->set('value', $item);
        $itemsCollection->add($itemRecord);
      }
      $itemsCollection->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "timestamp" (дата и время).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedTimestampParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::TIMESTAMP_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "timestamp" (дата и время).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postTimestampExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['min']))
    {
      $minParamLimit = new jParameterableOption();
      $minParamLimit->set('parameter_id', $parameter['id']);
      $minParamLimit->set('name', 'min');
      $minParamLimit->set('value', $options['min']);
      $minParamLimit->save();
    }

    // Set maximum parameter limit.
    if (! empty($options['max']))
    {
      $maxParamLimit = new jParameterableOption();
      $maxParamLimit->set('parameter_id', $parameter['id']);
      $maxParamLimit->set('name', 'max');
      $maxParamLimit->set('value', $options['max']);
      $maxParamLimit->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "liblink" (связка со словарем).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedLiblinkParameterOptions($modelName, array $options)
  {
    // Check library exists.
    if (empty($options['library']))
    {
      throw new sfException(sfContext::getInstance()
        ->getI18N()->__('ID словаря не указан', null, 'behavior-parameterable'));
    }

    return array(
      'type'        => PluginjParameterableSchema::LIBLINK_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "liblink" (связка со словарем).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postLiblinkExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Save library Id for parameter.
    $libraryLnk = new jParameterableOption();
    $libraryLnk->set('parameter_id', $parameter['id']);
    $libraryLnk->set('name', 'library');
    $libraryLnk->set('value', $options['library']);
    $libraryLnk->save();
  }

  /**
   * Возвращает список опций для создания параметра типа "image" (прикрепление изображения).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedImageParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::IMAGE_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "image" (прикрепление изображения).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postImageExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['extensions']))
    {
      $parameterOption = new jParameterableOption();
      $parameterOption->set('parameter_id', $parameter['id']);
      $parameterOption->set('name', 'extensions');
      $parameterOption->set('value', join(',', array_filter($options['extensions'])));
      $parameterOption->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "document" (прикрепление документа).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedDocumentParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::DOCUMENT_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий после успешного создания параметра типа "document" (прикрепление документа).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postDocumentExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['extensions']))
    {
      $parameterOption = new jParameterableOption();
      $parameterOption->set('parameter_id', $parameter['id']);
      $parameterOption->set('name', 'extensions');
      $parameterOption->set('value', join(',', array_filter($options['extensions'])));
      $parameterOption->save();
    }
  }

  /**
   * Возвращает список опций для создания параметра типа "file" (прикрепление файла).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedFileParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::FILE_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }

  /**
   * Метод дополнительных действий 
   * после успешного создания параметра типа "file" (прикрепление файла).
   * 
   * @param Doctrine_Record $parameter Запись в таблице о параметре.
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function postFileExecute(Doctrine_Record $parameter, array $options, $modelName)
  {
    // Set minimum parameter limit.
    if (! empty($options['extensions']))
    {
      $parameterOption = new jParameterableOption();
      $parameterOption->set('parameter_id', $parameter['id']);
      $parameterOption->set('name', 'extensions');
      $parameterOption->set('value', join(',', array_filter($options['extensions'])));
      $parameterOption->save();
    }
  }

  /**
   * Возвращает список опций 
   * для создания параметра типа "adress" (географический адрес).
   * 
   * @param array $options Опции создания параметра.
   * @param string $modelName Имя компонента (модели).
   * @return array
   */
  public static function getExtendedAddressParameterOptions(array $options, $modelName = null)
  {
    return array(
      'type'        => PluginjParameterableSchema::ADDRESS_TYPE_NAME,
      'default'     => (isset($options['default_value']) ? $options['default_value'] : null),
      'is_require'  => (isset($options['is_require']) ? $options['is_require'] : null),
      'is_many'     => (isset($options['is_many']) ? (bool) $options['is_many'] : false),
      'is_group'    => (isset($options['is_group']) ? $options['is_group'] : null)
    );
  }
}
