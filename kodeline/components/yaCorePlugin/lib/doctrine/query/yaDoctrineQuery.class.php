<?php
/**
 */
abstract class yaDoctrineQuery extends Doctrine_Query
{
  protected $rootRelations = null;

  protected $rootAssociationRelations = null;

  protected $_startClause = false;

  /**
   * @todo: переделать.
   */
  public function getQueryComponentAlias($componentName)
  {
    // Build query.
    if ( ! $this->_queryComponents) {
      $this->getSqlQuery(array(), false);
    }

    foreach ($this->getTableAliasMap() as $alias)
    {
      $component = $this->getQueryComponent($alias);
      if (! empty($component) && array_key_exists('table', $component))
      {
        if ($componentName == $component['table']->getComponentName())
        {
          return $alias;
        }
      }
    }

    return null;
  }

  
  
  /**
* This function begins an AND clause wrapped in parenthesis
* Requires a call to endClause()
*
* @return $this
*/
  public function andClause()
  {
    if ($this->_hasDqlQueryPart('where')) {
        $this->_addDqlQueryPart('where', 'AND', true);
    }

    $this->_addDqlQueryPart('where', '(', true);

    $this->_startClause = true;

    return $this;
  }

  /**
* This function begins an OR clause wrapped in parenthesis.
* Requires a call to endClause()
*
* @return $this
*/
  public function orClause()
  {
    if ($this->_hasDqlQueryPart('where')) {
        $this->_addDqlQueryPart('where', 'OR', true);
    }

    $this->_addDqlQueryPart('where', '(', true);

    $this->_startClause = true;

    return $this;
  }

  /**
* This function ends a clause
*
* @return $this
*/
  public function endClause()
  {
    if ($this->_startClause)
    {
      $this->_startClause = false;
      
      // Remove last two elements (open parenthesis and the "AND or OR" before it)
      array_pop($this->_dqlParts['where']);
      array_pop($this->_dqlParts['where']);
    }
    else
    {
      $this->_addDqlQueryPart('where', ')', true);
    }

    return $this;
  }
  
  protected function _addDqlQueryPart($queryPartName, $queryPart, $append = false)
  {
    if ($queryPartName == 'where' && $this->_startClause && ($queryPart == 'AND' || $queryPart == 'OR'))
    {
      $this->_startClause = false;
      return $this;
    }
    
    return parent::_addDqlQueryPart($queryPartName, $queryPart, $append);
  }

  /**
   * This function will wrap the current dql where statement in a clause
   *
   * @return $this
   */
  public function whereWrap()
  {
    $where = $this->_dqlParts['where'];

    if (count($where) > 0)
    {
      array_unshift($where, '(');
      array_push($where, ')');

      $this->_dqlParts['where'] = $where;
    }

    return $this;
  }

  public function preQuery()
  {
    //die(var_dump($this->getDql()));

    //$q->getQuery()->setDQL(str_replace('WHERE', 'INDEX BY yourIndexValue WHERE', $q->getDQL()))
  }

  /**
   * @todo
   * - Смотреть Doctrine_Query::load и реализовать вставку INDEXBY.
   * - Смотреть Doctrine_Core::ATTR_COLL_KEY
   *
   * @return $this
   */
  public function indexBy($column)
  {
    $this->_dqlParts['from'][0] .= ' INDEXBY '. $column;
    return $this;

    echo '<pre>'; var_dump($this->_dqlParts); echo '</pre>';
    echo '<pre>';
     die(var_dump($this->_sqlParts));

    $this->buildIndexBy($this->getRootAlias(), $column);
    return $this;
    
    
    /*
    return $this;

    die(var_dump($this->getDql()));
    return $this;
        if (empty($this->_dqlParts['from']) && empty($this->_sqlParts['from'])) {
            throw new Doctrine_Query_Exception('You must have at least one component specified in your from.');
        }
    */
    $componentAlias = $this->getRootAlias();

    $found = false;

    if ($componentAlias !== false && $componentAlias !== null) {
      $table = $this->_queryComponents[$componentAlias]['table'];

      // check column existence
      if ($table->hasField($clausePart)) {
        $found = true;

        $def = $table->getDefinitionOf($clausePart);
        $clausePart = $table->getColumnName($clausePart);

        if (isset($def['owner'])) {
            $componentAlias = $componentAlias . '.' . $def['owner'];
        }

        $tableAlias = $this->getSqlTableAlias($componentAlias);

        if ($this->getType() === Doctrine_Query::SELECT) {
          $clausePart = $this->_conn->quoteIdentifier($tableAlias) . '.' . $this->_conn->quoteIdentifier($clausePart);
        }
        else {
        // build sql expression
          $clausePart = $this->_conn->quoteIdentifier($clausePart);
        }
      }
      else {
        $found = false;
      }
    }

    if ( ! $found) {
      $clausePart = $this->getSqlAggregateAlias($clausePart);
    }

    $this->buildIndexBy($componentAlias, $clausePart);
    return $this;

    //$q  = $q->getQuery()->setDQL(str_replace('WHERE', 'INDEX BY yourIndexValue WHERE', $q->getDQL()))

    //die(var_dump($clausePart));
    /*
    $index = $this->_dqlParts['indexby'];

    die(var_dump($index));

    if (count($where) > 0)
    {
      array_unshift($where, '(');
      array_push($where, ')');

      $this->_dqlParts['where'] = $where;
    }

    return $this;
    */
  }

  /**
   * Retrieves all relation objects defined on root table of this query.
   *
   * @return array
   */
  public function getRootRelations()
  {
    if (null == $this->rootRelations)
    {
      $this->rootRelations = $this->getRoot()->getRelations();
    }

    return $this->rootRelations;
  }

  /**
   *
   *
   */
  public function getRootAssociations()
  {
    if (null !== $this->rootAssociationRelations)
    {
      return $this->rootAssociationRelations;
    }

    $this->rootAssociationRelations = array();

    foreach ($this->getRootRelations() as $alias => $relation)
    {
      if ($relation instanceof Doctrine_Relation_Association)
      {
        $this->rootAssociationRelations[$alias] = $relation;
      }
    }

    return $this->rootAssociationRelations;
  }

  /**
   *
   *
   */
  public function getRootAssociationByClass($class)
  {
    foreach ($this->getRootAssociations() as $association)
    {
      if ($association->getClass() === $class)
      {
        return $association;
      }
    }

    return null;
  }

  /**
   *
   *
   */
  public function getRootAssociationByRefClass($class)
  {
    foreach ($this->getRootAssociations() as $association)
    {
      if ($association->getAssociationTable()->getComponentName() === $class)
      {
        return $association;
      }
    }

    return null;
  }

  /**
   *
   *
   */
  public function getRootRelation($alias)
  {
    return yaArray::get($this->getRootRelations(), $alias);
  }

  /**
   *
   *
   */
  public function joinAll()
  {
    $rootAlias = $this->getRootAlias();

    foreach($this->getRootRelations() as $relation)
    {
      $alias = $relation->getAlias();

      if ($alias === 'Version')
      {
        continue;
      }
      elseif ($alias === 'Translation')
      {
        $this->withI18n();
      }
      else
      {
        if ($relation instanceof Doctrine_Relation_ForeignKey)
        {
          if ($this->getRootAssociationByRefClass($relation->getClass()))
          {
            continue;
          }
        }

        $joinAlias = yaString::lcfirst($relation->getAlias());
        $this->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $joinAlias));

        if ($relation->getTable()->hasRelation('Translation'))
        {
          $joinI18nAlias = $joinAlias.'Translation';
          $this->leftJoin(sprintf('%s.%s %s WITH %s.lang = ?', $joinAlias, 'Translation', $joinI18nAlias, $joinI18nAlias), yaDoctrineRecord::getDefaultCulture());
        }
      }
    }

    return $this;
  }

  /**
   * Will join named relations
   */
  public function joinRelations(array $aliases, $withI18n = false, $culture = null)
  {
    $rootAlias = $this->getRootAlias();

    if (null == $culture)
    {
      $culture = yaDoctrineRecord::getDefaultCulture();
    }

    foreach ($aliases as $alias)
    {
      if (! $relation = $this->getRootRelation($alias))
      {
        throw new yaException(sprintf('%s is not a valid alias for the table %s', $alias, $this->getRoot()->getComponentName()));
      }

      if ($relation->getAlias() === 'Translation')
      {
        $this->withI18n();
      }
      else
      {
        $joinAlias = yaString::lcfirst($relation->getAlias());
        $this->leftJoin(sprintf('%s.%s %s', $rootAlias, $relation->getAlias(), $joinAlias));

        if ($withI18n && $relation->getTable()->hasRelation('Translation'))
        {
          $joinTranslationAlias = $joinAlias.'Translation';
          $this->leftJoin($joinAlias.'.Translation '.$joinTranslationAlias.' ON '.$joinAlias.'.id = '.$joinTranslationAlias.'.id AND '.$joinTranslationAlias.'.lang = ?', $culture);
        }
      }
    }

    return $this;
  }

  /**
   * Join translation results if they exist
   * if $model is specified, will verify that it has I18n
   * return @myDoctrineQuery $this
   */
  public function withI18n($culture = null, $model = null, $rootAlias = null, $joinSide = 'left')
  {
    if (null !== $model)
    {
      if (! yaDb::table($model)->hasI18n())
      {
        return $this;
      }
    }

    if (null === $rootAlias)
    {
      // refresh query for introspection
      $this->buildSqlQuery();

      $rootAlias        = $this->getRootAlias();
      $translationAlias = $rootAlias.'Translation';

      // i18n already joined
      if ($this->hasAliasDeclaration($translationAlias))
      {
        return $this;
      }
    }
    else
    {
      $translationAlias = $rootAlias.'Translation';
    }

    $culture = null === $culture ? myDoctrineRecord::getDefaultCulture() : $culture;

    $joinMethod = $joinSide.'Join';

    return $this->$joinMethod($rootAlias.'.Translation '.$translationAlias.' ON '.$rootAlias.'.id = '.$translationAlias.'.id AND '.$translationAlias.'.lang = ?', $culture);
  }

  public function whereIsActive($boolean = true, $model = null)
  {
    if (null !== $model)
    {
      $table = yaDb::table($model);

      if (!$table->hasField('is_active'))
      {
        return $this;
      }
    }

    return $this->addWhere($this->getRootAlias().'.is_active = ?', (bool) $boolean);
  }

  public function whereIsEnabled($boolean = true, $model = null)
  {
    if (null !== $model)
    {
      $table = yaDb::table($model);

      if (! $table->hasField('enabled'))
      {
        return $this;
      }
    }

    return $this->addWhere($this->getRootAlias().'.enabled = ?', (bool) $boolean);
  }

  /**
   * Will restrict results to $model records
   * associated with $descendant record
   */
  public function whereDescendant(myDoctrineRecord $descendantRecord, $model)
  {
    return $this->whereDescendantId(get_class($descendantRecord), $descendantRecord->get('id'), $model);
  }

  /**
   * Add asc order by position field.
   * If $model is specified, will verify that it has 'sort_order' field.
   *
   * @param string  $model
   *
   * @return myDoctrineQuery $this
   */
  public function orderByField($model = null, $fieldOrder = 'sort_order', $bDesc = true)
  {
    if (null !== $model)
    {
      if (! yaDb::table($model)->hasField($fieldOrder))
      {
        return $this;
      }
    }

    $me = $this->getRootAlias();

    return $this->addOrderBy("$me.$fieldOrder " . ($bDesc ? 'DESC' : 'ASC'));
  }

  /**
   * returns join alias for a given relation alias, if joined
   * ex: "Elem e, e.Categ my_categ"
   * alias for joined relation Categ = my_categ
   * getJoinAliasForRelationAlias('Elem', 'Categ') ->my_categ
   */
  public function getJoinAliasForRelationAlias($model, $relationAlias)
  {
    $this->buildSqlQuery();

    foreach ($this->getQueryComponents() as $joinAlias => $queryComponent)
    {
      if (isset($queryComponent['relation'])
          && $relationAlias == $queryComponent['relation']['alias']
          && $model == $queryComponent['relation']['localTable']->getComponentName())
      {
        return $joinAlias;
      }
    }

    return null;
  }

  /**
   * @return myDoctrineCollection|null the fetched collection
   */
  public function fetchRecords($params = array())
  {
    return $this->execute($params, Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * Add limit(1) to the query,
   * then execute $this->fetchOne()
   * @return myDoctrineRecord|null the fetched record
   */
  public function fetchRecord($params = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
  {
    return $this->limit(1)->fetchOne($params, $hydrationMode);
  }

  public function fetchValue($params = array())
  {
    return $this->execute($params, Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }

  public function fetchValues($params = array())
  {
    return $this->execute($params, Doctrine_Core::HYDRATE_SCALAR);
  }

  public function fetchOneArray($params = array())
  {
    return $this->fetchOne($params, Doctrine_Core::HYDRATE_ARRAY);
  }

  /**
   * fetch brutal PDO array with numeric keys
   * @return array PDO result
   */
  public function fetchPDO($params = array())
  {
    return $this->execute($params, DOCTRINE::HYDRATE_NONE);
  }

  public function exists()
  {
    return $this->count() > 0;
  }

  public function toDebug()
  {
    return $this->getSqlQuery();
  }
}