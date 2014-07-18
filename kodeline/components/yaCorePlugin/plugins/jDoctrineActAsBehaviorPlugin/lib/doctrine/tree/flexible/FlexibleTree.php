<?php
/**
 * Tree object for Flexibletree behavior.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Flexibletree
 * @category    Tree
 * @author      chugarev@gmail.com
 */
class Doctrine_Tree_FlexibleTree extends Doctrine_Tree implements Doctrine_Tree_Interface
{
  /**
   * Base query of the tree.
   * 
   * @var Doctrine_Query
   */
  private $_baseQuery;

  /**
   * Alias for fetch data.
   * 
   * @var string
   */
  private $_baseAlias = "base";

  /**
   * Name of the table, which support tree relationships when
   * database server is not supported foreign keys for self table.
   * 
   * @var string
   */
  private $_relationTreeTableName = "";

  /**
   * Constructor.
   *
   * @param Doctrine_Table $table Объект таблицы, используемой как дерево.
   * @param array $options Параметры.
   */
  public function __construct(Doctrine_Table $table, $options)
  {
    // Определение содержит ли таблица множество деревьев или одно.
    $options['hasManyRoots'] = isset($options['hasManyRoots']) ? $options['hasManyRoots'] : false;

    if ($options['hasManyRoots'])
    {
      // Setup root field.
      $options['rootColumnName'] = (isset($options['rootColumnName']) ? $options['rootColumnName'] : 'root_id');
      $table->hasColumn($options['rootColumnName'], 'integer', null, array('notnull' => false));     
    }

    // Save _relationTreeTableName.
    $this->_relationTreeTableName = sprintf('%s_pc_relation', $table->getTableName());

    // Call parent constructor.
    parent::__construct($table, $options);
  }

  /**
  * {@inheritDoc}
  */
  public function setUp()
  {
    parent::setup();

    /*
   public function exportTable(Doctrine_Table $table)
    {
        try {
            $data = $table->getExportableFormat();

            $this->conn->export->createTable($data['tableName'], $data['columns'], $data['options']);
        } catch(Doctrine_Connection_Exception $e) {
            // we only want to silence table already exists errors
            if ($e->getPortableCode() !== Doctrine_Core::ERR_ALREADY_EXISTS) {
                throw $e;
            }
        }
    }
    */

    /*
    Ругается что нет такого класса.
    if (myDoctrineTable::existsTable(Doctrine_Manager::getInstance()->getCurrentConnection(), $this->_relationTreeTableName))
    {
      die('ok!');
    }
    else {
      die('no exists');
    }
    */

    /*

    if ('Pgsql' !== $this->table->getDriverName())
    {
      // If database server driver's is not supported foregn key to self table
      //then create extended table for saving relationships between parent and children nodes of the tree.

      
      try { 
        $conn->execute("DESC " . $this->_relationTreeTableName);
      }
      catch (Exception $exception)
      { 
        var_dump(get_class($exception)); die;
        return false;
      }
    }
    else {
      // If driver is Pgsql then create 
      //foreign key for self table.

      // Setup relation for Parent.
      $this->table->hasOne($this->getBaseComponent() . ' as Parent', array('local' => 'parent_id', 'foreign' => 'id', 'owningSide' => true));
      //$this->table->hasOne($this->getBaseComponent() . ' as Parent', array('local' => 'parent_id', 'owningSide' => true));

      // Setup relation for Children.
      $this->table->hasMany($this->getBaseComponent() . ' as Children', array('local' => 'id', 'foreign' => 'parent_id', 'owningSide' => true));
    }
    */
  }

  /**
  * {@inheritDoc}
  */
  public function setTableDefinition()
  {
    // Setup root column name.
    if (($root = $this->getAttribute('rootColumnName')) && (! $this->table->hasColumn($root)))
    {
      $this->table->setColumn($root, 'integer');
    }

    // Setup parent id column (Adjacency List tree algo)
    if ($parentName = $this->getAttribute('parentColumnName'))
    {
      $this->table->setColumn($parentName . ' AS parent_id', 'integer', 4, array('notnull' => false, 'default' => null));
    }
    else {
      $this->table->setColumn('parent_id', 'integer', 4, array('notnull' => false, 'default' => null));
    }

    // Setup path column (Materialized Path tree algo)
    if ($pathName = $this->getAttribute('pathColumnName'))
    {
      $this->table->setColumn($pathName . ' AS path', 'clob', null, array('notnull' => false, 'default' => null));
    }
    else {
      $this->table->setColumn('path', 'clob', null, array('notnull' => false, 'default' => null));
    }

    // Setup level column (Materialized Path tree algo)
    if ($levelName = $this->getAttribute('levelColumnName'))
    {
      $this->table->setColumn($levelName . ' AS level', 'integer', 4, array('notnull' => true, 'default' => 0));
    }
    else {
      $this->table->setColumn('level', 'integer', 4, array('notnull' => true, 'default' => 0));
    }

    // Setup sibling agregate column.
    if ($siblingName = $this->getAttribute('siblingColumnName'))
    {
      $this->table->setColumn($levelName . ' AS sibling', 'integer', 4, array('notnull' => true, 'default' => 1));
    }
    else {
      $this->table->setColumn('sibling', 'integer', 4, array('notnull' => true, 'default' => 1));
    }

    // Setup children agregate column.
    if ($childrenName = $this->getAttribute('childrenColumnName'))
    {
      $this->table->setColumn($childrenName . ' AS children', 'integer', 4, array('notnull' => true, 'default' => 0));
    }
    else {
      $this->table->setColumn('children', 'integer', 4, array('notnull' => true, 'default' => 0));
    }
  }

  /**
   * Creates root node from given record or from a new record.
   *
   * Note: When using a tree with multiple root nodes (hasManyRoots), you MUST pass in a
   * record to use as the root. This can either be a new/transient record that already has
   * the root id column set to some numeric value OR a persistent record. In the latter case
   * the records id will be assigned to the root id. You must use numeric columns for the id
   * and root id columns.
   *
   * @param object $record        instance of Doctrine_Record
   */
  public function createRoot(Doctrine_Record $record = null)
  {
    // Initiate new record.
    if ( ! $record)
    {
      $record = $this->getTable()->create();
    }

    // Fetch columns of the table in database.
    $arColumns = $record->getTable()->getColumns();

    if ($this->getAttribute('hasManyRoots') && ! $record->getNode()->getRootValue())
    {
      // Throw exception if root column is not integer and is not set.
      if ('integer' !== $arColumns[$this->getAttribute('rootColumnName')]['type'])
      {
        throw new Doctrine_Tree_Exception("Node must have a root id set.");
      }
      else {
        // Fetch max root id value.
        $maxRootId = (int) $this->getBaseQuery()
                            ->select('MAX(' . $this->_baseAlias . '.' . $this->getAttribute('rootColumnName') .')')
                            ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        $record->getNode()->setRootValue(($maxRootId + 1));
      }
    }

    $record->set('parent_id', null);
    $record->set('path', null);
    $record->set('level', 0);
    $record->save();

    // Bug: в режиме таска ошибка: The "default" context does not exist.
    // Trigger event 'flextree.new_root'.
    //sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'flextree.new_root', array('id' => $record['id'])));

    return $record;
  }

  /**
   * Выборка корневого узла указанного дерева.
   *
   * @param integer $rootId
   * @todo Better $rootid = null and exception if $rootId == null && hasManyRoots?
   *       Fetching with id = 1 is too magical and cant work reliably anyway.
   */
  public function fetchRoot($rootId = null)
  {
    // Определение запроса выборки коренных узлов дерева.
    $query = $this->getBaseQuery()
                  ->addWhere($this->_baseAlias . '.parent_id IS NULL')
                  ->addWhere($this->_baseAlias . '.path IS NULL')
                  ->andWhere($this->_baseAlias . '.level = ?', 0);

    if (null != $rootId)
    {
      $query = $this->returnQueryWithRootId($query, $rootId);
    }

    $data = $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);

    if (count($data) <= 0) { return false; }

    if ($data instanceof Doctrine_Collection)
    {
      $root = $data->getFirst();
    }
    else if (is_array($data))
    {
      $root = array_shift($data);
    }
    else {
      throw new Doctrine_Tree_Exception("Unexpected data structure returned.");
    }

    return $root;
  }

  /**
   * Fetch tree and retrieve as  
   * Hierarchy Doctrine Collection elements.
   * 
   * @param array $options Options for the fetch data.
   * @param integer $hydrationMode Mode for hydrate sql query result.
   * @return Doctrine_Collection
   */
  public function fetchNestedTree(array $options = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD_HIERARCHY)
  {
    // Fetch branch of the tree.
    $query = $this->getTreeQuery($options);

    if (! $query)
    {
      return new Doctrine_Collection($this->table);
    }

    return $query->execute(array(), $hydrationMode);

    /*
    switch($this->getLevel())
    {
      case 0:
        $array_tree[$this->getId()] = $this->getName();
        break;

      default:
        $array_tree[$this->getId()] = str_repeat('-', $this->getLevel()) . $this->getName();
        break;
    }

    if (! $this->getNode()->isLeaf()) {
      foreach($this->getNode()->getDescendants() as $child) {
        $array_tree = $child->fetchNestedTree($array_tree);
      }
    }

    return $array_tree;
    */
  }

  /**
   * Build and retrieve tree sql query.
   *
   * @param array $options  Options
   * @return Doctrine_Query
   */
  private function getTreeQuery(array $options = array())
  {
    // Определение запроса по выборке дерева.
    $query = $this->getBaseQuery();

    // Оределение уровня вложенности.
    $iDepth = isset($options['depth']) ? $options['depth'] : null;

    // if tree has many roots, then specify root id
    $rootId = isset($options['root_id']) ? $options['root_id'] : null;
    if (null != $rootId)
    {
      $query = $this->returnQueryWithRootId($query, $rootId);
    }

    if ( ! is_null($iDepth))
    { 
      $query->addWhere($this->_baseAlias . ".level BETWEEN ? AND ?", array(0, $iDepth)); 
    }

    return $query;
  }

  /**
   * Выборка всего дерева.
   * При выборке всего дерева используется алгоритм Adjacency List.
   *
   * @param array $options  Options
   * @param integer $hydrationMode  One of the Doctrine_Core::HYDRATE_* constants.
   * @return mixed          The tree or FALSE if the tree could not be found.
   */
  public function fetchTree($options = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
  {
    return $this->getTreeQuery($options)->execute(array(), $hydrationMode);
  }

  /**
   * Build and retrieve branch sql query.
   *
   * @param array $options  Options
   * @return Doctrine_Query
   */
  private function getBranchQuery($pk, array $options = array())
  {
    // Fetch record of branch root.
    $record = $this->table->find($pk);

    if ( ! ($record instanceof Doctrine_Record) || !$record->exists())
    {
      // TODO: if record doesn't exist, throw exception or similar?
      return false;
    }

    // Define branch select query.
    $query = $this->getBaseQuery();
    $recordPath = $record->get('path');
    $query->addWhere($this->_baseAlias . ".path = ?", sprintf('%d', $record->get('id')))
          ->orWhere($this->_baseAlias . ".path LIKE ?", sprintf('%s%d.%s', (empty($recordPath) ? null : $recordPath), $record->get('id'), '%'))
          ->addOrderBy($this->_baseAlias . ".level ASC");

    // Change query relate supported behavior templates.
    $query = $this->getBehaviorsQuery($query);

    // Define depth of select.
    $depth = isset($options['depth']) ? $options['depth'] : null;

    if ( ! is_null($depth))
    { 
      $query->addWhere($this->_baseAlias . ".level BETWEEN ? AND ?", array($record->get('level'), $record->get('level') + $depth)); 
    }

    $query = $this->returnQueryWithRootId($query, $record->getNode()->getRootValue());

    return $query;
  }

  /**
   * Fetch branch and retrieve a 
   * hierarchy Doctrine Collection of the tree.
   * 
   * @param integer $pk Permanent key of the tree's node.
   * @param array $options Options for the fetch data.
   * @param integer $hydrationMode Mode for hydrate sql query result.
   * @return Doctrine_Collection
   */
  public function fetchNestedBranch($pk, array $options = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD_HIERARCHY)
  {
    // Fetch branch of the tree.
    $query = $this->getBranchQuery($pk, $options);

    if (! $query)
    {
      return new Doctrine_Collection($this->table);
    }

    return $query->execute(array(), $hydrationMode);
  }

  /**
   * Fetches a branch of a tree.
   *
   */
  public function fetchBranch($pk, $options = array(), $hydrationMode = Doctrine_Core::HYDRATE_RECORD)
  {
    return $this->getBranchQuery($pk, $options)->execute(array(), $hydrationMode);
  }

  /**
   * Возвращает выборку всех корневых узлов.
   *
   * @return mixed  The root nodes.
   */
  public function fetchRootsSql()
  {
    return $this->getBaseQuery()
                ->addWhere($this->_baseAlias . '.parent_id IS NULL')
                ->addWhere($this->_baseAlias . '.path IS NULL')
                ->andWhere($this->_baseAlias . '.level = ?', 0);
  }

  /**
   * Возвращает выборку всех корневых узлов.
   *
   * @return mixed  The root nodes.
   */
  public function fetchRoots($hydrationMode = Doctrine_Core::HYDRATE_RECORD)
  {
    return $this->fetchRootsSql()->execute(array(), $hydrationMode);
  }

  /**
   * Returns parsed query with root id column 
   * where clause added if applicable.
   *
   * @param object    $query    Doctrine_Query
   * @param integer   $root_id  id of destination root
   * @return Doctrine_Query
   */
  public function returnQueryWithRootId($query, $rootId = 1)
  {
    if ($this->getAttribute('hasManyRoots'))
    {
      if ($rootColumn = $this->getAttribute('rootColumnName'))
      {
        if (is_array($rootId))
        {
          $rootId = array_filter($rootId);
          if (count($rootId))
          {
            $query->addWhere($this->_baseAlias . '.' . $rootColumn . ' IN (' . implode(',', array_fill(0, count($rootId), '?')) . ')', $rootId);
          }
        }
        else if(null != $rootId) {
          $query->addWhere($this->_baseAlias . '.' . $rootColumn . ' = ?', $rootId);
        }
      }
    }

    return $query;
  }

  /**
   * Enter description here...
   *
   * @param array $options
   * @return unknown
   */
  public function getBaseQuery()
  {
    if ( ! isset($this->_baseQuery))
    {
      $this->_baseQuery = $this->_createBaseQuery();
    }
    return $this->_baseQuery->copy();
  }

  /**
   * Enter description here...
   *
   */
  public function getBaseAlias()
  {
    return $this->_baseAlias;
  }

  /**
  * Enter description here...
  *
  */
  private function _createBaseQuery()
  {
    return Doctrine_Core::getTable($this->getBaseComponent())
                            ->createQuery($this->_baseAlias)
                            ->select($this->_baseAlias . '.*');
  }

  /**
   * Enter description here...
   *
   * @param Doctrine_Query $query
   */
  public function setBaseQuery(Doctrine_Query $query)
  {
    $this->_baseAlias = $query->getRootAlias();
    $query->addSelect($this->_baseAlias . ".parent_id, " . $this->_baseAlias . ".path, ". $this->_baseAlias . ".level");

    if ($this->getAttribute('rootColumnName'))
    {
      $query->addSelect($this->_baseAlias . "." . $this->getAttribute('rootColumnName'));
    }

    $this->_baseQuery = $query;
  }

  /**
   * Enter description here...
   *
   */
  public function resetBaseQuery()
  {
    $this->_baseQuery = $this->_createBaseQuery();
  }

  /**
   * Change query relate supported behaviors.
   * 
   * @param Doctrine_Query $query
   * @return Doctrine_Query
   */
  public function getBehaviorsQuery($query)
  {
    // Sortable behavior.
    if ($this->table->isSortable())
    {
      $query->addOrderBy($this->_baseAlias . '.position DESC');
    }

    return $query;
  }
}
