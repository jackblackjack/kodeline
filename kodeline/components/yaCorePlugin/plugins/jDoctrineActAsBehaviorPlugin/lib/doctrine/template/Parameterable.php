<?php
/**
 * Parameterable behavior template for records.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Parameterable
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Parameterable extends Behavior_Template
{
  /**
   * Array of parameterable options
   * 
   * @var array
   */
  protected $_options = array();

  /**
   * __construct
   *
   * @param array $options 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  /**
   * {@inheritDoc}
   */
  public function setTableDefinition()
  {
    // Setup relation Schema.
    //$this->table->hasOne('jParameterableSchema as Schema', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));

    // Setup relations of values.
    /*
    $this->table->hasMany('jParameterableStringValue as StringValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    $this->table->hasMany('jParameterableIntegerValue as IntegerValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    $this->table->hasMany('jParameterableDecimalValue as DecimalValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    $this->table->hasMany('jParameterableDateValue as DateValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    $this->table->hasMany('jParameterableTimeValue as TimeValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    $this->table->hasMany('jParameterableTimestampValue as TimestampValues', array('local' => 'id', 'foreign' => 'object_id', 'owningSide' => true));
    */

    /*
    // Define component id.
    $iComponent = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());

    if (null !== $iComponent)
    {
      // Fetch versionable value.
      $VersionableValue = $this->getOption('versionable_value', null);

      // Fetch parameters list for component.
      $arExtendedParams = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
                            ->where('psc.component_id = ?', $iComponent)
                            ->andWhere('psc.typeof = ?', $VersionableValue)
                            ->fetchArray();
             
      foreach($arExtendedParams as $arParam)
      {
        $this->hasColumn(
          $arParam['name'], 
          $arParam['type'], 
          $arParam['length'], 
          array('notnull' => (bool) $arParam['is_null'], 'default' => $arParam['default_value'])
        );
      }
    }
    */

    // Sets listener.
    $this->addListener(new Doctrine_Template_Listener_Parameterable($this->_options));
  }

  /**
   * Возвращает возможные значения для указанного параметра.
   * Работает только с типами radio (переключатель), checkbox (флаг) и enum (список).
   * 
   * @param integer $iParameterId Parameter id for fetch values.
   * @param string $sCulture Language for fetch values.
   * @param integer $iLimit Limit for fetch.
   * @param integer $iOffset Offset for fetch.
   * @param integer $iBelongBy Id of belonged node for fetch parameter.
   * @param string $sComponentName Component name for fetch.
   * 
   * @return array
   */
  public function fetchExtendedParameterValues($iParameterId, $sCulture = null, $iLimit = null, $iOffset = null, $iBelongBy = null, $sComponentName = null)
  {
    die(__METHOD__);

    // Initiate new object record instance.
    $invoker = (empty($sComponentName) ? $this->getInvoker() : new $sComponentName());

    // Prepare query for fetch type of parameter.
    $query = Doctrine::getTable('jParameterableSchema')
              ->createQuery('psc')
              ->select('psc.type, psc.default_value, psc.belong')
              ->where('psc.id = ?', $iParameterId)
              ->andWhere('psc.component_id = ?', $this->fetchComponentId((! empty($sComponentName) ? $sComponentName : $invoker->getTable()->getComponentName())));

    // Add belong by node id.
    if (0 < ($iBelongBy = (int) $iBelongBy)) { $query->andWhere('psc.belong = ?', $iBelongBy); }                     

    // Fetch type of parameter.
    $parameterData = $query->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

    // Throw exception if parameter is not found.
    if (! $parameterData['type'])
    {
      throw new behaviorException(sprintf(
        $this->getContext()->getI18N()->__('Параметер #%d компонента "%s" не найден!', null, 'behavior-parameterable'),
        $iParameterId, $invoker->getTable()->getComponentName())
      );
    }

    // Define component values type.
    $sComponentValuesType = (
      ($parameterData['type'] !== PluginjParameterableSchema::RADIO_TYPE_NAME 
        && $parameterData['type'] !== PluginjParameterableSchema::CHECKBOX_TYPE_NAME 
        && $parameterData['type'] !== PluginjParameterableSchema::ENUM_TYPE_NAME) ? $parameterData['type'] : 'string');

    // Fetch predefined values for enum list.
    die('asd');
    $query = Doctrine_Core::getTable('jParameterable' . ucfirst($sComponentValuesType). 'Value')
                ->createQuery('prevals')
                ->select('prevals.id')
                ->indexBy('prevals.id')
                ->where('prevals.component_name = ?', $sComponentName)
                ->andWhere('prevals.parameter_id = ?', $iParameterId)
                ->orderBy('prevals.position DESC');

    if ('string' === $sComponentValuesType)
    {
      // Define culture.
      $sCulture = (!empty($sCulture) ? $sCulture : sfContext::getInstance()->getUser()->getCulture());
      $query->addSelect('prevalstr.value')->leftJoin('prevals.Translation as prevalstr WITH prevalstr.lang = ?', $sCulture);
    }

    // Limit to query.
    //if ($iLimit) $query->limit($iLimit);

    // Offset to query.
    //if ($iOffset) $query->offset($iOffset);

    return array(
      'default' => $parameterData['default_value'], 
      'limit'   => $iLimit,
      'offset'  => $iOffset,
      'values'  => $query->fetchArray()
    );
  }

  /**
   * Возвращает список значений относительно указанного параметра.
   * 
   * @param integer $iParameter
   * @param string|null $parameterType
   * @param string|null $lang
   * 
   * @return string|integer|float|array
   */
  public function fetchExtendedParameterValue($iParameter, $parameterType = null, $lang = null)
  {
    // If record is new - throw exception.
    if ($this->getInvoker()->isNew()) {
      
      throw new sfException(
        sprintf(sfContext::getInstance()
          ->getI18N()->__('Значения параметров "%s" не поддерживает расширение Parameterable!', null, 'flexible-tree'), 
          $this->getInvoker()->getTable()->getComponentName()
        )
      );
    }
    // If records is exists.
    else {
      
      // Fetch invoker component id.
      $invokerComponentId = $this->fetchComponentId(
        $this->getInvoker()->getTable()->getComponentName());

      // Component name.
      if ('enum' === $parameterType || 
          'liblink' === $parameterType || 
          'checkbox' === $parameterType ||
          'radio' === $parameterType) $parameterType = 'integer';
      $componentValueName = 'jParameterable' . sfInflector::camelize($parameterType) . 'Value';

      // Fetch value of the parameter.
      $pvQuery = Doctrine::getTable($componentValueName)->createQuery('pv');

      if ('string' === strtolower($parameterType)) {
        // Define lang for the fetch records.
        $transLang = (is_null($lang) ? sfContext::getInstance()->getUser()->getCulture() : $lang);

        $pvQuery->innerJoin('pv.Translation as pvtr WITH pvtr.lang = ?' , $transLang);
      }

      $paramValue = $pvQuery
                      ->andWhere('pv.component_id = ?', $invokerComponentId)
                      ->andWhere('pv.object_id = ?', $this->getInvoker()->getId())
                      ->andWhere('pv.parameter_id = ?', $iParameter)
                      ->fetchOne(array(), Doctrine_Core::HYDRATE_ARRAY);

      return ('string' === strtolower($parameterType)) ? $paramValue['Translation'][$transLang]['value'] : $paramValue['value'];
    }
  }

  /**
   * Recursive collect types of the parameters in the schema.
   * 
   * @param Doctrine_Collection|array (Hierarchy) list of the parameters.
   * @return array
   */
  private function & collectParametersTypes($arSchema)
  {
    $arResult = array();

    // List of collectedable types.
    $arCollectedableTypes = array(
      PluginjParameterableSchema::DECIMAL_TYPE_NAME,
      PluginjParameterableSchema::INTEGER_TYPE_NAME,
      PluginjParameterableSchema::STRING_TYPE_NAME,
      PluginjParameterableSchema::DATE_TYPE_NAME,
      PluginjParameterableSchema::TIME_TYPE_NAME,
      PluginjParameterableSchema::TIMESTAMP_TYPE_NAME,
    );

    foreach ($arSchema as $parameter)
    {
      // Skip not collectedable types.
      if (! in_array('needle', $arCollectedableTypes)) continue;

      $arResult[$parameter['type']][$parameter['id']] = &$parameter;

      if (count($parameter['__children'])) {
        array_merge($arResult, $this->collectParametersTypes($parameter['__children']));
      }
    }

    return $arResult;
  }

  /**
   * Fetch parameters values for integer type.
   * @param array List ids of parameters.
   * @param string Parameter component name.
   * @return array
   */
  private function __fetchValuesForIntegerType(array $arParameters, $sComponentName)
  {
    return Doctrine_Core::getTable('jParameterableIntegerValue')
                              ->createQuery('prevals')
                              ->select('prevals.id')
                              ->indexBy('prevals.parameter_id')
                              ->where('prevals.component_name = ?', $sComponentName)
                              ->andWhereIn('prevals.parameter_id', array_keys($arParameters))
                              ->orderBy('prevals.parameter_id ASC, prevals.position DESC')
                              ->fetchArray();

  }

  /**
   * Fetch parameters of the object with current and probably values.
   * 
   * @return array
   */
  public function fetchExtendedParameters($paramValue = false, $sComponentName = null, $bWithTrans = true, $lang = null)
  {
    // Fetch parameters schema.
    $arParametersSchema = $this->fetchExtendedParametersSchema($paramValue, $sComponentName, $bWithTrans, $lang);

    // Define list of parameters types.
    $arParametersTypes = $this->collectParametersTypes($arParametersSchema);

    $arValues = array();

    foreach($arParametersTypes as $type => $arParameters)
    {
      // Fetch predefined values for enum list.
      $arValues = Doctrine_Core::getTable('jParameterable' . ucfirst($type). 'Value')
                                ->createQuery('prevals')
                                ->select('prevals.id')
                                ->indexBy('prevals.parameter_id')
                                ->where('prevals.component_name = ?', $sComponentName)
                                ->andWhereIn('prevals.parameter_id', array_keys($arParameters))
                                ->orderBy('prevals.parameter_id ASC, prevals.position DESC')
                                ->fetchArray();

      foreach($arParameters as $parameter)
      {
        if (array_key_exists($parameter['id'], $arValues))
        {

        }
      }
    }

    return $arParametersSchema;
  }

  /**
   * Fetch hierarchy schema of the extended parameters.
   * 
   * @param string|null $modelName Name of the model, which support Parameterable behavior.
   * @param boolean $bWithTrans Fetch translation for parameter's name.
   * @param string|null $lang Language for fetch translation.
   * 
   * @return Doctrine_Collection
   */
  public function fetchExtendedParametersSchema($paramValue = false, $modelName = null, $bWithTrans = true, $lang = null)
  {
    return $this->baseFetchExtendedParameters($paramValue, $modelName, $bWithTrans, $lang, Doctrine_Core::HYDRATE_RECORD_HIERARCHY);
  }

  /**
   * Fetch hierarchy schema of the extended parameters.
   * 
   * @param string|null $modelName Name of the model, which support Parameterable behavior.
   * @param boolean $bWithTrans Fetch translation for parameter's name.
   * @param string|null $lang Language for fetch translation.
   * 
   * @return Doctrine_Collection
   */
  public function fetchExtendedParameterNames($paramValue = false, $modelName = null, $bWithTrans = true, $lang = null)
  {
    return $this->baseFetchExtendedParameters($paramValue, $modelName, $bWithTrans, $lang, Doctrine_Core::HYDRATE_ARRAY);
  }

  /**
   */
  private function baseFetchExtendedParameters($belongBy = false, $modelName = null, $bWithTrans = true, $lang = null, $hydrateMode = Doctrine_Core::HYDRATE_ARRAY)
  {
    // Define current invoker object.
    $invoker = (null === $modelName ? $this->getInvoker() : Doctrine::getTable($this->modelName)->getRecordInstance());

    // Check if table has extended by Parameterable behavior.
    if (! $invoker->getTable()->hasTemplate('Parameterable'))
    {
      throw new behaviorException(sprintf(sfContext::getInstance()
        ->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable!', null, 'flexible-tree'), 
        $invoker->getTable()->getComponentName()));
    }

    // Fetch component id of the object.
    $iComponentId = $this->fetchComponentId($invoker->getTable()->getComponentName());

    // Define current lang for the fetch translation records.
    $transLang = (is_null($lang) ? sfContext::getInstance()->getUser()->getCulture() : $lang);

    // Check column exists in the table.
    $query = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
              ->innerJoin('psc.Component as pscmps')
              ->innerJoin('psc.Translation as psctr WITH psctr.lang = ?' , $transLang);

    // If model has been specified column' value for typeof.
    if ($invoker->getTable()->getTemplate('Parameterable')->getOption('versionable_value'))
    {
      $query->andWhere('psc.typeof = ?', $invoker->getTable()->getTemplate('Parameterable')->getOption('versionable_value'));
    }

    // If options has specified column name for value.
    // TODO:
    // 1. Добавлять distinct innerJoin выборку относительно поля "param_value_col" для
    //    модели, параметры которой рассматриваются: узнавать имя Relation по полю, и, если есть Relation
    //    - делать дополнительную выборку по Relation.
    //    Если список - перечислять через запятую, если нет - просто выводить.
    //    Назвать "Extended param".
    //
    // 2. В myDoctrineCollection добавить выборку схемы параметров когда объектов много: 
    //    сделать 1 раз выборку по типу объекта и накидать значения по иерархии для каждого объекта.
    //
    if (false !== $belongBy)
    {
      // Check column exists in the table.
      $query->innerJoin('psc.belong = ?', $belongBy);
    }
    //
    elseif(null !== $this->getOption('param_value_col', null) && isset($invoker[$this->getOption('param_value_col')]))
    {
      // If invoker supports template "FlexibleTree" - 
      // fetch parameters of the parents nodes and private parameters of the node.
      if (! $invoker->isNew() && $invoker->getTable()->hasTemplate('FlexibleTree'))
      {
        //$pathColumnName = $invoker->getTable()->getTree()->getAttribute('pathColumnName', 'path');
        $pathColumnName = 'path';
        $arPath = array_merge(array_filter(explode('.', $invoker[$pathColumnName])), array($invoker['id']));

        // Fetch id of the tree nodes for collect all params of the tree.
        $arParamValues = $invoker->getTable()->getTree()->getBaseQuery()->select($this->getOption('param_value_col'))
                          ->andWhereIn('id', $arPath)
                          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        if (count($arParamValues))
        { 
          $query->andWhereIn('psc.belong', $arParamValues);
        }
      }
      // If invoker table has not 
      // support template "FlexibleTree" - fetch parameters for invoker only.
      else
      {
        $query->andWhere('psc.belong = ?', $invoker[$this->getOption('param_value_col')]); 
      }
    }

    // Fetch param of the component.
    return $query->execute(array(), $hydrateMode);
  }

  /**
   */
  public function fetchExtendedParameterById($iParameterId, $modelName = null, $bWithTrans = true, $lang = null)
  {
    // Define lang for the fetch records.
    $transLang = (is_null($lang) ? sfContext::getInstance()->getUser()->getCulture() : $lang);

    // Define component id.
    $iComponent = (is_null($modelName) ? $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName()) : $this->fetchComponentId($modelName));

    // Check column exists in the table.
    $query = Doctrine::getTable('jParameterableSchema')->createQuery('psc');

    if ($bWithTrans)
    {
      $query->innerJoin('psc.Translation as psctr WITH psctr.lang = ?' , $transLang);
    }

    return $query
            ->andWhere('psc.id = ?', $iParameterId)
            ->andWhere('psc.component_id = ?', $iComponent)
            ->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * Добавляет предопределенное значение параметра для модели.
   * Работает только с типами radio (переключатель), checkbox (флаг) и enum (список).
   * 
   * @param integer $iParameterId Parameter for modify.
   * @param array $arValues Values for parameter for adds.
   * @return
   */
  public function addExtendedParameterValue($iParameterId, $arValue, $belongBy = null, $componentName = null)
  {
    // Initiate new object record instance.
    $invoker = (empty($componentName) ? $this->getInvoker() : new $componentName());

    // Prepare query for fetch type of parameter.
    $query = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
              ->select('psc.type, psc.belong')
              ->where('psc.id = ?', $iParameterId)
              ->andWhere('psc.component_id = ?', $this->fetchComponentId((! empty($componentName) ?: $invoker->getTable()->getComponentName())));

    // Add belong by node id.
    if (0 < ($belongBy = integer($belongBy))) {
      $query->andWhere('psc.belong = ?', $belongBy);
    }                     

    // Fetch type of parameter.
    $parameterData = $query->fetchArray();

    // Throw exception if parameter is not found.
    if (! $parameterData['type']) {
      
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Параметер #%d компонента "%s" не найден!', null, 'behavior-parameterable'),
        $iParameterId, $invoker->getTable()->getComponentName())
      );
    }

    // Supports types only: RADIO_TYPE_NAME, CHECKBOX_TYPE_NAME, ENUM_TYPE_NAME
    if ($parameterData['type'] !== PluginjParameterableSchema::RADIO_TYPE_NAME || 
        $parameterData['type'] !== PluginjParameterableSchema::CHECKBOX_TYPE_NAME || 
        $parameterData['type'] !== PluginjParameterableSchema::ENUM_TYPE_NAME) {
      
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Тип параметра #%s не поддерживается!', null, 'behavior-parameterable'), $parameterData['type']));
    }

    // Prepare adding value.
    $arValue = is_array($arValue) ?: array($arValue);
    $arValue = array_filter($arValue);

    if (! count($arValue)) {
      
      throw new sfException($this->getContext()
        ->getI18N()->__('Нельзя добавить пустое значение!', null, 'behavior-parameterable'));
    }

    // Create collection of values.
    $valuesCollection = new Doctrine_Collection('jParameterableStringValue');
    
    foreach ($arValue as $value) {

      $valueRecord = new jParameterableStringValue();
      $valueRecord->set('component_name', $invoker->getTable()->getComponentName());
      $valueRecord->set('object_id', $parameterData['belong']);
      $valueRecord->set('parameter_id', $iParameterId);
      $valueRecord->set('value', (string) $value);
      $valuesCollection->add($valueRecord);
    }

    // Save collection.
    return $valuesCollection->save();
  }

  /**
   * Set value for extended parameter of the model.
   * 
   * @param string $columnName Column name of the parameter.
   */
  public function setExtendedParameters($arValues, $modelName = null)
  {
    // Initiate new object record instance.
    $invoker = (is_null($modelName) ? $this->getInvoker() : new $this->modelName());

    // Fetch parameters by name.
    $params = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
                      ->andWhereIn('psc.name', array_keys($arValues))
                      ->andWhere('psc.component_id = ?', $this->fetchComponentId($invoker->getTable()->getComponentName()))
                      ->fetchArray();

    // Throw exception if parameter is not found.
    if (! $params)
    {
      throw new sfException(sprintf(
        $this->getContext()->getI18N()->__('Параметры %s для модели "%s" не найден!', null, 'behavior-parameterable'), 
        implode(',', array_keys($arValues)), $modelName));
    }

    // Update value of the parameters.
    $query = Doctrine_Query::create()->update($invoker->getTable()->getComponentName());

    $_ =& $arValues;
    foreach($arValues as $field => $value)
    {
      $query->set($field, '?', $value);
    }

    if (is_null($modelName))
    {
      $query->where('id = ?', $invoker['id']);
    }

    return $query->execute();
  }

  /**
   * Add new extended parameter for model.
   */
  public function createExtendedParameter($name, array $options, $parent_id = null, array $strings = array(), $belong = null)
  {
    // Define parameter options.
    $columnName = $name;

    // Begin transaction for the changes.
    $this->getInvoker()->getTable()->getConnection()->beginTransaction();

    try {
      // Check parent parameter if parent has been set.
      if (! is_null($parent_id))
      {
        // Fetch parent object.
        $parent = Doctrine::getTable('jParameterableSchema')->createQuery()->where('id = ?', $parent_id)->fetchOne();

        if (! $parent)
        {
          throw new sfException(sprintf(sfContext::getInstance()
            ->getI18N()->__('Родительский параметер с ID=%d не найден!', null, 'behavior-parameterable'), $parent_id));
        }
      }

      // Fetch component ID.
      $iComponentId = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());

      // Check column exists in the table.
      $schemaParam = Doctrine::getTable('jParameterableSchema')->createQuery()
                      ->where('name = ?', $columnName)
                      ->andWhere('component_id = ?', $iComponentId)
                      ->fetchOne();

      if (! $schemaParam)
      {
        // Create new parameter.
        $schemaParam = new jParameterableSchema();  
        $schemaParam->set('component_id', $iComponentId);
        $schemaParam->set('name', $columnName);
        $schemaParam->set('type', $options['type']);

        // Set options for creates parameter.
        if (isset($options['length'])) $schemaParam->set('length', $options['length']);
        if (isset($options['scale'])) $schemaParam->set('scale', $options['scale']);
        if (isset($options['is_null'])) $schemaParam->set('is_null', $options['is_null']);
        if (isset($options['out_format'])) $schemaParam->set('out_format', $options['out_format']);
        if (isset($options['default_value'])) $schemaParam->set('default_value', $options['default_value']);
        if (isset($options['is_many'])) $schemaParam->set('is_many', (int) $options['is_many']);
        if (isset($options['is_dynamic'])) $schemaParam->set('is_dynamic', (int) $options['is_dynamic']);
        if (isset($options['is_require'])) $schemaParam->set('is_require', (int) $options['is_require']);
        if (isset($options['is_hidden'])) $schemaParam->set('is_hidden', (int) $options['is_hidden']);
        if (isset($options['is_group'])) $schemaParam->set('is_group', (int) $options['is_group']);

        // Sets strings for parameter.
        if (! empty($strings))
        {
          $arLangs = array_keys($strings);
          $szLangs = count($arLangs);

          for($i = 0; $i < $szLangs; $i++)
          {
            $schemaParam['Translation'][$arLangs[$i]]['title'] = (isset($strings[$arLangs[$i]]['title']) ? $strings[$arLangs[$i]]['title'] : $columnName);
            $schemaParam['Translation'][$arLangs[$i]]['hint'] = (isset($strings[$arLangs[$i]]['hint']) ? $strings[$arLangs[$i]]['hint'] : null);
          }
        }

        // If value of param is not null.
        if (! is_null($belong))
        {
          $schemaParam['belong'] = $belong;
        }
        elseif (strlen($this->getOption('param_value_col')))
        {
          $invoker = $this->getInvoker();
          $schemaParam['belong'] = $invoker[$this->getOption('param_value_col')];
        }

        // Save jParameterableSchema record.
        if (! is_null($parent_id) && $parent)
        {
          $parent->getNode()->addChild($schemaParam);
        }
        else {
          $schemaParam->getTable()->getTree()->createRoot($schemaParam);
        }

        // If options has specified column name for versionable.
        if ($this->getOption('versionable_value')) {
          $schemaParam['typeof'] = $this->getOption('versionable_value');
        }

        $schemaParam->save();
      }

      // Commit transaction of alter table.
      $this->getInvoker()->getTable()->getConnection()->commit();

      // Send event for update models.
      sfContext::getInstance()->getEventDispatcher()->notify(
        new sfEvent(null, 'parameterable.update.baseclasses', array('component' => $this->getInvoker()->getTable()->getComponentName()))
      );

      return $schemaParam;
    }
    // Catch Doctrine_Export_Exception
    catch(Doctrine_Export_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }
    // Catch Doctrine_Connection_Exception
    catch(Doctrine_Connection_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }
    // Catch Doctrine_Export_Exception
    catch(sfException $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }

    return false;
  }

  /**
   * Add new parameter to the table.
   * 
   * @param jComment $comment
   */
  public function createExtendedParameterOld($name, array $options = array(), $parent_id = null, $title = null, $lang = null, $is_public = true, $param = null)
  {
    // Define parameter options.
    $columnName = $name;
    $fieldTitle = (is_null($title) ? $columnName : $title);
    $fieldTitleLang = (is_null($lang) ? sfContext::getInstance()->getUser()->getCulture() : $lang);

    // Begin transaction for the changes.
    $this->getInvoker()->getTable()->getConnection()->beginTransaction();

    try {
      // Check parent parameter if parent has been set.
      if (! is_null($parent_id))
      {
        // Fetch parent object.
        $parent = Doctrine::getTable('jParameterableSchema')->createQuery()->where('id = ?', $parent_id)->fetchOne();

        if (! $parent)
        {
          throw new sfException(sprintf(sfContext::getInstance()
            ->getI18N()->__('Родительский параметер с ID=%d не найден!', null, 'behavior-parameterable'), $parent_id));
        }
      }

      // Fetch component ID.
      $iComponentId = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());

      // Check column exists in the table.
      $fieldRecord = Doctrine::getTable('jParameterableSchema')->createQuery()
                      ->where('name = ?', $columnName)
                      ->andWhere('component_id = ?', $iComponentId)
                      ->fetchOne();

      if (! $fieldRecord)
      {
        // Set parameter title.
        $schemaParam = new jParameterableSchema();
        $schemaParam->set('component_id', $iComponentId);
        $schemaParam->set('name', $columnName);
        $schemaParam->set('type', $options['type']);
        $schemaParam->set('length', $options['length']);
        $schemaParam->set('default_value', $options['default']);
        $schemaParam['is_public'] = (int) $is_public;
        $schemaParam['Translation'][$fieldTitleLang]['title'] = $fieldTitle;

        // If value of param is not null.
        if (! is_null($param))
        {
          $schemaParam['param'] = $param;
        }
        elseif (strlen($this->getOption('param_value_col'))) {
          $invoker = $this->getInvoker();
          $schemaParam['param'] = $invoker[$this->getOption('param_value_col')];
        }

        // Save jParameterableSchema record.
        if (! is_null($parent_id) && $parent)
        {
          $parent->getNode()->addChild($schemaParam);
        }
        else {
          $schemaParam->getTable()->getTree()->createRoot($schemaParam);
        }

        // If options has specified column name for versionable.
        if ($this->getOption('versionable_value')) {
          $schemaParam['typeof'] = $this->getOption('versionable_value');
        }
        $schemaParam->save();
      }

      // Add column to table in the database.
      $bAlter = $this->getInvoker()->getTable()->getConnection()->export->alterTable(
        $this->getInvoker()->getTable()->getTableName(), array('add' => array($columnName => $options))
      );

      // Commit transaction of alter table.
      $this->getInvoker()->getTable()->getConnection()->commit();

      // Send event for update models.
      sfContext::getInstance()->getEventDispatcher()->notify(
        new sfEvent(null, 'parameterable.update.baseclasses', array('component' => $this->getInvoker()->getTable()->getComponentName()))
      );

      return true;      
    }
    // Catch Doctrine_Export_Exception
    catch(Doctrine_Export_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }
    // Catch Doctrine_Connection_Exception
    catch(Doctrine_Connection_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }
    // Catch Doctrine_Export_Exception
    catch(sfException $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      throw $Exception;
    }

    return false;
  }

  /**
   * Delete parameter from table.
   * 
   * @param string $columnName Column name of the parameter.
   */
  public function deleteExtendedParameter($columnName)
  {
    try {
      // Fetch component ID.
      $iComponentId = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());

      // Fetch column definition record.
      $fieldRecord = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
                      ->where('psc.name = ?', $columnName)
                      ->andWhere('psc.component_id = ?', $iComponentId)
                      ->fetchOne();

      if (! $fieldRecord)
      {
        throw new sfException(sprintf(sfContext::getInstance()
                    ->getI18N()->__('Описание поля "%s" не найдено!', null, 'behavior-parameterable'), $columnName));
      }

      // Begin transaction for the changes.
      $this->getInvoker()->getTable()->getConnection()->beginTransaction();

      /*
      $this->getInvoker()->getTable()->getConnection()->export->alterTable(
        $this->getInvoker()->getTable()->getTableName(), array('remove' => array($columnName => array()))
      );
      */
    
      $fieldRecord->delete();
      $this->getInvoker()->getTable()->getConnection()->commit();

      // Send event for update models.
      sfContext::getInstance()->getEventDispatcher()->notify(
        new sfEvent(null, 'parameterable.update.baseclasses', 
                      array('component' => $this->getInvoker()->getTable()->getComponentName()))
      );

      return true;
    }
    // Catch Doctrine_Export_Exception
    catch(Doctrine_Export_Exception $Exception)
    {
      // Rollback changes if transaction has been started.
      if ($this->getInvoker()->getTable()->getConnection()->getTransactionLevel()) {
        $this->getInvoker()->getTable()->getConnection()->rollback();  
      }

      die($Exception->getMessage());
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }
    // Catch Doctrine_Connection_Exception
    catch(Doctrine_Connection_Exception $Exception)
    {
      // Rollback changes if transaction has been started.
      if ($this->getInvoker()->getTable()->getConnection()->getTransactionLevel()) {
        $this->getInvoker()->getTable()->getConnection()->rollback();  
      }

      die($Exception->getMessage());
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }
    // Catch Doctrine_Export_Exception
    catch(sfException $Exception)
    {
      // Rollback changes if transaction has been started.
      if ($this->getInvoker()->getTable()->getConnection()->getTransactionLevel()) {
        $this->getInvoker()->getTable()->getConnection()->rollback();  
      }

      die($Exception->getMessage());
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }

    return false;
  }

  /**
   * Rename parameter in the table.
   * 
   * @param string $oldName Column name of the parameter.
   * @param string $newName Column name of the parameter.
   */
  public function renameParameter($oldName, $newName)
  {
    die('no realize');
    //Doctrine_Export::getDeclaration

    // Begin transaction for the changes.
    //$this->getInvoker()->getTable()->getConnection()->beginTransaction();

    try {
      // Fetch component ID.
      $iComponentId = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());

      // Check column exists in the table.
      $fieldRecord = Doctrine::getTable('jParameterableSchema')->createQuery()
                      ->where('name = ?', $oldName)
                      ->andWhere('component_id = ?', $iComponentId)
                      ->fetchOne();

      if (! $fieldRecord) {
        throw new sfException(sprintf(sfContext::getInstance()->getI18N()->__('Параметра "%s" не найдено!', null, 'behavior-parameterable'), $oldName));
      }

      $columnName = $this->getColumnName($fieldName);
      //if ($this->hasField($this->getFieldName($e2[0]))) {

      //$this->getInvoker()->getTable()->getConnection()->export->alterTable($this->getInvoker()->getTable()->getTableName(), array('remove' => array($columnName => array())));
      //$this->getInvoker()->getTable()->getConnection()->commit();
    }
    // Catch Doctrine_Export_Exception
    catch(Doctrine_Export_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }
    // Catch Doctrine_Connection_Exception
    catch(Doctrine_Connection_Exception $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }
    // Catch Doctrine_Export_Exception
    catch(sfException $Exception)
    {
      // Rollback changes.
      $this->getInvoker()->getTable()->getConnection()->rollback();
      sfContext::getInstance()->getUser()->setFlash('error', $Exception->getMessage());
    }
  }

  private function refreshBaseClass()
  {
    die('no realize');
    
    $config = $this->getCliConfig();
    $builderOptions = $this->configuration->getPluginConfiguration('sfDoctrinePlugin')->getModelBuilderOptions();

    $stubFinder = sfFinder::type('file')->prune('base')->name('*'.$builderOptions['suffix']);
    $before = $stubFinder->in($config['models_path']);

    $schema = $this->prepareSchemaFile($config['yaml_schema_path']);

    $import = new Doctrine_Import_Schema();
    $import->setOptions($builderOptions);
    $import->importSchema($schema, 'yml', $config['models_path']);

    // cleanup new stub classes
    $after = $stubFinder->in($config['models_path']);
    $this->getFilesystem()->replaceTokens(array_diff($after, $before), '', '', $tokens);

    // cleanup base classes
    $baseFinder = sfFinder::type('file')->name('Base*'.$builderOptions['suffix']);
    $baseDirFinder = sfFinder::type('dir')->name('base');
    $this->getFilesystem()->replaceTokens($baseFinder->in($baseDirFinder->in($config['models_path'])), '', '', $tokens);

    $this->reloadAutoload();

  }
}
/*
     *                                    'rename' => array(
     *                                        'sex' => array(
     *                                            'name' => 'gender',
     *                                            'definition' => array(
     *                                                'type' => 'text',
     *                                                'length' => 1,
     *                                                'default' => 'M',
     *                                            ),
     *                                        )
     *                                    )
*/