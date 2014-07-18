<?php
/**
 */
abstract class yaDoctrineTable extends Doctrine_Table
{
  /**
   */
  protected $_queryFilters = array();

  protected $_queryInitFilters = array();

  /**
   * Return true if table support behaviors yaSortable or Sortable.
   * @return boolean
   */
  public function isSortable()
  {
    return ($this->hasTemplate('yaSortable') || $this->hasTemplate('Sortable'));
  }

  public function isVersionable()
  {
    return ($this->hasTemplate('yaVersionable') || $this->hasTemplate('Versionable'));
  }

  /**
   * Return true if all components in the list exist in the system.
   * 
   * @param array $componentNames List of components.
   * @return boolean
   */
  public function componentsExists($componentNames)
  {
    if (! is_array($componentNames)) $componentNames = array($componentNames);
    return (count($componentNames) == count(array_intersect($componentNames, array_keys($this->getConnection()->getTables()))));
  }


  /**
   * Return true if all the given tables exist.
   *
   * @todo Not works probably.
   * 
   * @param array $tableNames
   * @return bool
   */
  public function tablesExist($tableNames)
  {
    $tables = array_keys($this->getConnection()->getTables());
    var_dump($tables); die;
    //getTables

    foreach ($tableNames as $key => $tableName) {
          if (strpos($tableName, '.') !== false) {
              $tableName = explode('.', $tableName, 2);
              $tableNames[$key] = $this->_getPortableTableDefinition(array('schema_name'=>$tableName[0], 'table_name'=>$tableName[1]));
          }
      }
      return parent::tablesExist($tableNames);

      //$this->connection->getSchemaManager()->tablesExist(array($this->migrationsTableName))
  }


  /**
   * Retrieve true if tablename exists.
   * 
   * @param $conn = Doctrine_Manager::connection();
   * @param string $tableName Name of the table for check exists.
   * @return boolean
   */
  public static function existsTable($conn, $tableName)
  {
    if (! class_exists(sfInflector::camelize($tableName))) return false;
    
    try {
      Doctrine_Query::create($conn)->from(sfInflector::camelize($tableName))->execute();
    }
    catch (Doctrine_Exception $exception) {
      return false;
    }
    catch (Doctrine_Connection_Exception $e) {
      if ($e->getPortableCode() !== Doctrine_Core::ERR_NOSUCHTABLE) {
        throw new Doctrine_Export_Exception($e->getMessage());
      }
      return false;
    }
    
    return true;
  }

  /**
   * Return list of filters.
   * @return array()
   */
  public function getQueryFilters() { 
    return $this->_queryFilters;
  }

  public function getPrimaryKeys()
  {
    $arPrimaryColumns = array();

    $tableColumns = $this->getColumns();
    foreach ($tableColumns as $columnName => $definition) {
      if ( isset($definition['primary']) && $definition['primary']) {
        $arPrimaryColumns[] = $columnName;
      }
    }

    return $arPrimaryColumns;
  }

  /**
   * Return filter for query.
   * @return SQLFilter
   */
  public function getQueryFilter($name)
  {
    if ('dev' == sfConfig::get('sf_environment')) {
      sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(
        sprintf('Apply filter "%s" for "%s"', $name, $this->getComponentName()), 'priority' => sfLogger::INFO)));
    }

    // Search filter name in the list of filters.
    if (! array_key_exists($name, $this->_queryFilters)) {
      throw new yaException(sprintf('Query filter "%s" is not found!', $name));
    }

    // Initiated filter class if need it.
    if (! array_key_exists($name, $this->_queryInitFilters))
    {
      $filter = new ReflectionClass($this->_queryFilters[$name]);
      if (! $filter->isSubclassOf('SQLFilter'))
      {
        throw new yaException(sprintf('Filter "%s" is not instance of "%s"!', $this->_queryFilters[$name], 'SQLFilter'));
      }

      $this->_queryInitFilters[$name] = $filter->newInstance($this);
      unset($filter);
    }

    return $this->_queryInitFilters[$name];
  }

  /**
   * Add filter for query of the table.
   * 
   * @param string $name Name of the filter.
   * @param string $class Class name of the filter.
   * @return $this
   */
  public function addQueryFilter($name, $class)
  {
    $this->_queryFilters[$name] = $class;

    if ('dev' == sfConfig::get('sf_environment'))
    {
      if (sfContext::hasInstance() && sfContext::getInstance())
      {
        sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(
          sprintf('Add filter "%s" for %s as "%s"', $name, $this->getComponentName(), $class), 'priority' => sfLogger::INFO)));
      }
    }

    return $this;
  }

  /**
   * Returns changed query with added expression for the current model.
   * 
   * @param Doctrine_Query $query Prepared query.
   * @param string $filterName Name of the filter.
   * @param array $arParameters Parameters for the filter.
   * @return Doctrine_Query Changed query.
   */
  public function applyFilter($query, $filterName, $arParameters = array())
  {
    $filter = $this->getQueryFilter($filterName);
    $filter->setParameters($arParameters);
    return $filter->addFilterConstraint($query);
  }

  /**
   * Returns changed query with added expression for the current model.
   * 
   * @param Doctrine_Query $query Prepared query.
   * @param string $filterName Name of the filter.
   * @param array $arParameters Parameters for the filter.
   * @return Doctrine_Query Changed query.
   */
  public function applyFilterIfExists($query, $filterName, $arParameters = array())
  {
    if (sfConfig::get('sf_logging_enabled'))
    {
      if (sfContext::hasInstance())
      {
        sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(
          sprintf('Request to apply filter "%s" for %s', $filterName, $this->getComponentName()), 'priority' => sfLogger::INFO)));
      }
    }

    try {
      $filter = $this->getQueryFilter($filterName);
      $filter->setParameters($arParameters);
      return $filter->addFilterConstraint($query);
    }
    catch(sfException $exception)
    {
    }
    return $query;
  }
}