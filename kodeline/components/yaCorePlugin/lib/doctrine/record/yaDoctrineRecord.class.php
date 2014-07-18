<?php

abstract class yaDoctrineRecord extends sfDoctrineRecord
{
  protected static
    $eventDispatcher;

  protected
    $hasI18n      = false,
    $i18Table     = null,
    $i18nFallback = null;

  /**
   * Initializes internationalization.
   *
   * @see Doctrine_Record
   */
  public function construct()
  {
    $this->hasI18n = $this->hasRelation('Translation');

    if ($this->hasI18n)
    {
      // only add filter to each table once
      if (! $this->getTable()->getOption('has_symfony_i18n_filter'))
      {
        $this->getTable()
          ->unshiftFilter(new yaDoctrineRecordI18nFilter())
          ->setOption('has_symfony_i18n_filter', true)
        ;
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  public function preDqlDelete($event)
  {
    $query = clone $event->getQuery();
    $query->select();
    $items = $query->execute();
    $cache = self::getInstance()->getAttribute(Doctrine_Core::ATTR_RESULT_CACHE);

    $arItems = array_keys($items);
    $szItems = count($arKeys);
    for($i = 0; $i < $szItems; $i++)
    {
      $cache->delete($arItems[$i]);
    }
  }

  public static function getContext()
  {
    return yaContext::getInstance();
  }

  /**
   *
   */
  public function hasI18n()
  {
    return $this->hasI18n;
  }

  /**
   *
   */
  public function getI18nTable()
  {
    if (null == $this->i18Table)
    {
      $this->i18Table = $this->hasI18n() ? $this->getTable()->getRelation('Translation')->getTable() : false;
    }

    return $this->i18Table;
  }

  /**
   *
   */
  public function hasCurrentTranslation()
  {
    return $this->get('Translation')->contains(self::getDefaultCulture());
  }

  /**
   *
   */
  public function getCurrentTranslation()
  {
    return $this->get('Translation')->get(self::getDefaultCulture());
  }

  /**
   *
   */
  public function hasField($fieldName)
  {
    $table = $this->getTable();

    if ($table->hasColumn($fieldName))
    {
      return true;
    }

    if ($this->hasI18n() && $this->getI18nTable()->hasField($fieldName))
    {
      return true;
    }

    return false;
  }

  /**
   *
   */
  public function isSortable()
  {
    $table = $this->getTable();
    return ($table->hasTemplate('yaSortable') || $table->hasTemplate('Sortable'));
    //return ($table->hasTemplate('yaSortable') || $table->hasTemplate('Sortable')) && 'id' === $table->getPrimaryKey();
  }

  /**
   *
   */
  public function isVersionable()
  {
    $table = $this->getTable();
    return ($table->hasTemplate('yaVersionable') || $table->hasTemplate('yaVersionable') ||
           ($this->hasI18n() && ($this->getI18nTable()->hasTemplate('yaVersionable') || $this->getI18nTable()->hasTemplate('yaVersionable'))));
  }

  /**
   * Add page tree watcher registering
   */
  public function preSave($event)
  {
    parent::preSave($event);

    if ($this->isModified())
    {
      $this->notify($this->isNew() ? 'create' : 'update');
    }
    elseif($this->hasI18n() && $this->hasCurrentTranslation() && $this->getCurrentTranslation()->isModified())
    {
      $this->notify($this->isNew() ? 'create' : 'update');
    }
  }

  /**
   * Notify insertion
   */
  public function postInsert($event)
  {
    if ($ed = $this->getEventDispatcher())
    {
      $ed->notify(new sfEvent($this, 'ya.record.creation'));
    }
  }

  /**
   * Add page tree watcher registering
   */
  public function postDelete($event)
  {
    parent::postDelete($event);

    $this->notify('delete');
  }


  public function notify($type = 'update')
  {
    if ($ed = $this->getEventDispatcher())
    {
      $ed->notify(new sfEvent($this, 'ya.record.modification', array('type' => $type)));
    }
  }

  public function refresh($deep = false)
  {
    return parent::refresh($deep)->clearCache();
  }

  /**
   * add fluent interface to @see parent::fromArray
   * @return myDoctrineRecord $this ( fluent interface )
   */
  public function fromArray(array $array, $deep = true)
  {
    parent::fromArray($array, $deep);
    return $this;
  }

  /**
   * Return null if this record is new
   * @return myDoctrineRecord | null
   */
  public function orNull()
  {
    return $this->isNew() ? null : $this;
  }

  /**
   * Return array representation of this record
   *
   * @return array An array representation of the record.
   */
  public function toDebug()
  {
    return array(
      'state' => $this->state().'='.Doctrine_Lib::getRecordStateAsString($this->state()),
      'data' => $this->toArray()
    );
  }

  /**
   * Exports I18n-aware record as array
   *
   * @param boolean $deep
   * @param boolean $prefixKey
   *
   * @return array
   */
  public function toArrayWithI18n($deep = true, $prefixKey = false)
  {
    $array = $this->toArray(false, $prefixKey);

    if ($this->_state == self::STATE_LOCKED || $this->_state == self::STATE_TLOCKED) {
      return false;
    }

    $stateBeforeLock = $this->_state;
    $this->_state = $this->exists() ? self::STATE_LOCKED : self::STATE_TLOCKED;

    if ($this->hasI18n())
    {
      foreach($this->getI18nTable()->getFieldNames() as $field)
      {
        if (in_array($field, array('id')))
        {
          continue;
        }

        $array[$field] = $this->get($field);
      }
    }

    if ($deep) {
      foreach ($this->_references as $key => $relation)
      {
        if (! $relation instanceof Doctrine_Null && false !== ($a = $relation->toArrayWithI18n($deep, $prefixKey)))
        {
          $array[$key] = $relation->toArrayWithI18n($deep, $prefixKey);
        }
      }
    }

    $this->_state = $stateBeforeLock;

    return $array;
  }

  public function toIndexableString()
  {
    $index = '';

    foreach($this->_table->getIndexableColumns() as $columnName => $column)
    {
      $index .= ' '.$this->get($columnName);
    }

    return trim($index);
  }

  public function isFieldModified($field)
  {
    return array_key_exists($field, $this->getModified());
  }

  public function saveGet(Doctrine_Connection $conn = null)
  {
    $this->save($conn);

    return $this;
  }

  /**
   */
  public function isNew()
  {
    if (parent::isNew())
    {
      return true;
    }

    if (! $this->_table instanceof myDoctrineTable)
    {
      return false;
    }

    $arPrimaries = $this->_table->getPrimaryKeys();
    foreach ($arPrimaries as $pk)
    {
      if (! $this->get($pk))
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Retrieves relation field value.
   *
   * @param string  $relation
   * @param string  $fieldName
   * @param string  $default
   * @param boolean $load
   *
   * @return mixed
   */
  public function getRelationFieldValue($relation, $fieldName, $default = null, $load = false)
  {
    if (! $this->hasRelation($relation))
    {
      throw new Doctrine_Record_Exception('Uknown relation %s');
    }

    if ($r = $this->get($relation, $load))
    {
      return $r->get($fieldName, $load);
    }

    return $default;
  }

  /**
   * returns a value of a property or a related component
   *
   * @param mixed   $fieldName    name of the property or related component
   * @param boolean $load         whether or not to invoke the loading procedure
   *
   * @throws Doctrine_Record_Exception    if trying to get a value of unknown property / related component
   *
   * @return mixed
   */
  public function get($fieldName, $load = true)
  {
    $hasAccessor = $this->hasAccessor($fieldName);

    if ($hasAccessor || $this->_table->getAttribute(Doctrine_Core::ATTR_AUTO_ACCESSOR_OVERRIDE))
    {
      $componentName = $this->_table->getComponentName();

      $accessor = $this->hasAccessor($fieldName) ? $this->getAccessor($fieldName) : 'get' . yaString::camelize($fieldName);

      if ($hasAccessor || method_exists($this, $accessor))
      {
        /**
         * Special case.
         * For versionable tables, we don't want to use
         * the getVersion accessor when requesting 'version'.
         * This is because "Version" is a relation, and "version" is a fieldname.
         * The case is lost when using getVersion.
         */
        if ('getVersion' === $accessor && $this->getTable()->isVersionable())
        {
          return $this->_get($fieldName, $load);
        }

        $this->hasAccessor($fieldName, $accessor);
        return $this->$accessor($load, $fieldName);
      }
    }

    return $this->_get($fieldName, $load);
  }

  /**
   * Pure overload without parent::_get
   */
  public function _get($fieldName, $load = true)
  {
    $value = self::$_null;

    if (array_key_exists($fieldName, $this->_values)) {
      return $this->_values[$fieldName];
    }

    if (array_key_exists($fieldName, $this->_data)) {
      // check if the value is the Doctrine_Null object located in self::$_null)
      if ($this->_data[$fieldName] === self::$_null && $load) {
        $this->load();
      }

      if ($this->_data[$fieldName] === self::$_null) {
        $value = null;
      } else {
        $value = $this->_data[$fieldName];
      }

      return $value;
    }

    /*
     * Add i18n capabilities
     */
    if ($fieldName !== 'Translation' && $this->hasI18n() && array_key_exists('id', $this->_data))
    {
      $i18nTable = $this->getI18nTable();

      if ($i18nTable->hasField($fieldName))
      {
        return $this->_getI18n($fieldName, $load);
      }
      elseif (!ctype_lower($fieldName) && !ctype_upper($fieldName{0}))
      {
        $underscoredFieldName = yaString::underscore($fieldName);
        if (strpos($underscoredFieldName, '_') !== false && $i18nTable->hasField($underscoredFieldName))
        {
          return $this->_getI18n($underscoredFieldName, $load);
        }
      }
    }
    /*
     * i18n end
     */

    /*
     * Allow to get values by modulized fieldName
     * ex : _get('cssClass') returns _get('css_class')
     *
    if(!ctype_lower($fieldName) && !ctype_upper($fieldName{0}) && !$this->contains($fieldName))
    {
      $underscoredFieldName = yaString::underscore($fieldName);
      if (strpos($underscoredFieldName, '_') !== false && $this->contains($underscoredFieldName))
      {
        return $this->_get($underscoredFieldName, $load);
      }
    }
    *
     * end
     */

    try {
      if ( ! isset($this->_references[$fieldName]) && $load) {
        $rel = $this->_table->getRelation($fieldName);
        $this->_references[$fieldName] = $rel->fetchRelatedFor($this);
      }

      if ($this->_references[$fieldName] === self::$_null) {
        return null;
      }

      return $this->_references[$fieldName];
    } catch (Doctrine_Table_Exception $e) {
      $success = false;
      foreach ($this->_table->getFilters() as $filter) {
        try {
          $value = $filter->filterGet($this, $fieldName);
          $success = true;
        } catch (Doctrine_Exception $e) {}
      }
      if ($success) {
        return $value;
      } else {
        throw $e;
      }
    }
  }

  public function _getI18n($fieldName, $load = true)
  {
    $culture = self::getDefaultCulture();

    $translation = $this->get('Translation');

    // we have a translation
    if($translation->contains($culture))
    {
      $i18n = $translation->get($culture);
    }
    // record is new so we use (or create) the fallback culture
    elseif($this->isNew())
    {
      $i18n = $translation->get(sfConfig::get('sf_default_culture'));
    }
    // record exists, try to fetch its missing translation
    else
    {
      $i18n = $this->getI18nTable()->createQuery('t')
        ->where('t.id = ?', $this->get('id'))
        ->andWhere('t.lang = ?', $culture)
        ->fetchRecord();

      // existing translation fetched
      if ($i18n)
      {
        $translation->set($culture, $i18n);
      }
      // no translation for this culture, use fallback
      elseif ($i18nFallback = $this->getI18nFallback())
      {
        $i18n = $i18nFallback;
      }
      // no fallback available
      else
      {
        return null;
      }
    }

    return $i18n->get($fieldName, $load);
  }

  public function getI18nFallback()
  {
    if (null !== $this->i18nFallback)
    {
      return $this->i18nFallback;
    }

    if ($this->isNew())
    {
      return null;
    }

    $i18nFallback = $this->getI18nTable()->createQuery('t')
      ->where('t.id = ?', $this->get('id'))
      ->andWhere('t.lang = ?', sfConfig::get('sf_default_culture'))
      ->fetchRecord();

    $this->i18nFallback = $i18nFallback ? $i18nFallback : false;

    return $this->i18nFallback;
  }

  /**
   * Allow to set values by modulized fieldName
   * ex : _get('cssClass') returns _get('css_class')
   */
  public function _set($fieldName, $value, $load = true)
  {
    if(!ctype_lower($fieldName) && !ctype_upper($fieldName{0}) && !$this->contains($fieldName))
    {
      $underscoredFieldName = yaString::underscore($fieldName);
      if (strpos($underscoredFieldName, '_') !== false && $this->contains($underscoredFieldName))
      {
        return parent::_set($underscoredFieldName, $value, $load);
      }
    }

    return parent::_set($fieldName, $value, $load);
  }

  /**
   * Retrieves event dispatcher instance
   *
   * @return sfEventDispatcher
   */
  public function getEventDispatcher()
  {
    if (null == self::$eventDispatcher)
    {
      self::$eventDispatcher = ya::getEventDispatcher();
    }

    return self::$eventDispatcher;
  }

  /**
   * Hack to make Versionable behavior work with I18n tables
   * it will add a where clause on the current culture
   * to avoid selecting versions for all cultures.
   */
  public function getVersion()
  {
    if (! $this->getTable()->isVersionable())
    {
      return $this->_get('version');
    }

    if (! $this->hasI18n())
    {
      return $this->_get('Version');
    }

    return $this
      ->getTable()
      ->getI18nTable()
      ->getTemplate('yaVersionable')
      ->getPlugin()
      ->getTable()
      ->createQuery('v')
      ->where('v.id = ?', $this->get('id'))
      ->andWhere('v.lang = ?', self::getDefaultCulture())
      ->fetchRecords();
  }

  /**
   * Provides access to i18n methods
   *
   * @param  string $method    The method name
   * @param  array  $arguments The method arguments
   *
   * @return mixed The returned value of the called method
   */
  public function __call($method, $arguments)
  {
    try
    {
      return parent::__call($method, $arguments);
    }
    catch(Exception $parentException)
    {
      try
      {
        if ($this->hasI18n() && ($i18n = $this->getCurrentTranslation()))
        {
          return call_user_func_array(array($i18n, $method), $arguments);
        }
      }
      catch (Exception $e) {}

      throw $parentException;
    }
  }

  /**
   *
   */
  public function setData(array $data)
  {
    $this->_data = $data;
  }

  /**
   * dmMicroCache
   */
  protected
    $cache;

  protected function getCache($cacheKey)
  {
    if (isset($this->cache[$cacheKey]))
    {
      return $this->cache[$cacheKey];
    }

    return null;
  }

  protected function hasCache($cacheKey)
  {
    return isset($this->cache[$cacheKey]);
  }

  protected function setCache($cacheKey, $cacheValue)
  {
    return $this->cache[$cacheKey] = $cacheValue;
  }

  public function clearCache($cacheKey = null)
  {
    if (null === $cacheKey)
    {
      $this->cache = array();
    }
    elseif(isset($this->cache[$cacheKey]))
    {
      unset($this->cache[$cacheKey]);
    }

    return $this;
  }

  /** TABLE PROXY METHODS **/

  /**
   *
   */
  public function isSortableTableProxy()
  {
    return $this->isSortable();
  }

  /**
   *
   */
  public function isVersionableTableProxy()
  {
    return $this->isVersionable();
  }
}