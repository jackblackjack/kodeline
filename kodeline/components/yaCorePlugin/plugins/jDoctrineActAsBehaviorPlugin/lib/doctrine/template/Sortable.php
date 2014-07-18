<?php
/**
 * Template for the sortable behavior which add position for any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Sortable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Sortable extends Behavior_Template
{    
  /**
   * Array of Sortable options
   *
   * @var string
   */
  protected $_options = array('name'        =>  'position',
                              'alias'       =>  null,
                              'type'        =>  'integer',
                              'length'      =>  8,
                              'unique'      =>  true,
                              'options'     =>  array('notnull' => true, 'default' => 0),
                              'fields'      =>  array(),
                              'canUpdate'   =>  false,
                              'indexName'   =>  'sortable'
  );

  /**
   * __construct
   *
   * @param array $array 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  /**
   * Set table definition for sortable behavior
   * (borrowed and modified from Sluggable in Doctrine core)
   *
   * @return void
   */
  public function setTableDefinition()
  {
    // Call parent method.
    parent::setTableDefinition();

    // Define the name of new field.
    $name = $this->_options['name'];
    if ($this->_options['alias']) { $name .= ' as ' . $this->_options['alias']; }

    // Create columns.
    $this->hasColumn($name, $this->_options['type'], $this->_options['length'], $this->_options['options']);

    // Define list of the fields for index.
    $indexFields = array($this->_options['name']);

    if ($this->getTable()->hasTemplate('FlexibleTree'))
    {
      $indexFields[] = 'level';
      $indexFields[] = 'parent_id';

      if ($this->getTable()->getTree()->getAttribute('hasManyRoots'))
      {
        $rootColumnName = $this->getTable()->getTree()->getAttribute('rootColumnName');
        $indexFields[] = $rootColumnName;
      }
    }

    // Save name for index.
    $indexName = sprintf('%s_%s', strtolower($this->getTable()->getTableName()), $this->_options['indexName']);
    $this->option('indexName', $indexName);

    // Save fields for index.
    $this->option('fields', $indexFields);

    if (true === $this->_options['unique']) {
      $this->index($indexName, array('fields' => $indexFields, 'type' => 'unique'));  
    }
    else {
      $this->index($indexName, array('fields' => $indexFields));  
    }

    // Set listener for records.
    $this->addListener(new Doctrine_Template_Listener_Sortable($this->_options));
  }

  /**
   * Demotes a sortable object to a lower position
   *
   * @return void
   */
  public function demote()
  { 
    $object = $this->getInvoker();       
    $position = $object->position;

    if ($object->position < $object->getFinalPosition())
    {
      $object->moveToPosition($position + 1);
    }
  }


  /**
   * Promotes a sortable object to a higher position
   *
   * @return void
   */
  public function promote()
  {
    $object = $this->getInvoker();       
    $position = $object->position;
    
    if ($object->position > 1)
    {
      $object->moveToPosition($position - 1);
    }
  }

  /**
   * Sets a sortable object to the first position
   *
   * @return void
   */
  public function moveToFirst()
  {
    $object = $this->getInvoker();       
    $object->moveToPosition(1);
  }


  /**
   * Sets a sortable object to the last position
   *
   * @return void
   */
  public function moveToLast()
  {
    $object = $this->getInvoker();       
    $object->moveToPosition($object->getFinalPosition());
  }


  /**
   * Moves a sortable object to a designate position
   *
   * @param int $newPosition
   * @return void
   */
  public function moveToPosition($newPosition)
  {
    if (! is_int($newPosition))
    {
      throw new Doctrine_Exception('moveToPosition requires an Integer as the new position. Entered ' . $newPosition);
    }

    $object = $this->getInvoker();       
    $position = $object->position;

    // Position is required to be unique. Blanks it out before it moves others up/down.
    if(!$object->setPosition(null)){
      throw new Doctrine_Exception('Failed to set the position to null on your '.get_class($object));
    }
    $object->save();

    // if(!$object->save()){
    //   throw new Doctrine_Exception('Failed to save your '.get_class($object).' with a blank position.');
    // }    
    if ($position > $newPosition)
    {
      $q = Doctrine_Query::create()
                         ->update(get_class($object))
                         ->set('position', 'position + 1')
                         ->where('position < ' . $position)
                         ->andWhere('position >= ' . $newPosition)
                         ->orderBy('position DESC');
      
      /*                
      foreach ($this->_options['uniqueBy'] as $field)
      {
        $q->addWhere($field.' = '.$object[$field]);
      }
      */
                        
      if(!$q->execute()){
        throw new Doctrine_Exception('Failed to run the following query: '.$q->getSql());
      }
    }
    elseif ($position < $newPosition)
    {

      $q = Doctrine_Query::create()
                         ->update(get_class($object))
                         ->set('position', 'position - 1')
                         ->where('position > ?', $position)
                         ->andWhere('position <= ' . $newPosition);
      /*
      foreach($this->_options['uniqueBy'] as $field)
      {
        $q->addWhere($field . ' = ' . $object[$field]);
      }
      */

      if(!$q->execute()){
        throw new Doctrine_Exception('Failed to run the following query: '.$q->getSql());
      }
    }
    
    if(!$object->setPosition($newPosition)){
      throw new Doctrine_Exception('Failed to set the position on your '.get_class($object));
    }
    $object->save();
    // if(!$object->save()){
    //   throw new Doctrine_Exception('Failed to save your '.get_class($object));
    // }
  }


  /**
   * Send an array from the sortable_element tag (symfony+prototype)and it will 
   * update the sort order to match
   *
   * @param string $order
   * @return void
   * @author Travis Black
   */
  public function sortTableProxy($order)
  {
    /*
      TODO 
        - Make this a transaction.
        - Add proper error messages.
    */

    $class = get_class($this->getInvoker()); 

    foreach ($order as $position => $id) 
    {
      $newObject = Doctrine::getTable($class)->findOneById($id);

      if ($newObject->position != $position + 1)
      {
        $newObject->moveToPosition($position + 1);
      }
    }
  }


  /**
   * Finds all sortable objects and sorts them based on position attribute
   * Ascending or Descending based on parameter
   *
   * @param string $order
   * @return $query
   */
  public function findAllSortedTableProxy($order = 'ASC')
  {
    $order = $this->formatAndCheckOrder($order);

    $class = get_class($this->getInvoker()); 
    $query = Doctrine_Query::create()
                           ->from($class . ' od')
                           ->orderBy('od.position ' . $order);

    return $query->execute();
  }


  /**
   * Finds and returns records sorted where the parent (fk) in a specified
   * one to many relationship has the value specified
   *
   * @param string $parent_value
   * @param string $parent_column_value
   * @param string $order
   * @return $query
   */
  public function findAllSortedWithParentTableProxy($parent_value, $parent_column_name = null, $order = 'ASC')
  {
    $order = $this->formatAndCheckOrder($order);
    
    $object = $this->getInvoker();
    $class  = get_class($object);
    
    if (!$parent_column_name)
    {
      $parents = get_class($object->getParent());

      if (count($parents) > 1)
      {
        throw new Doctrine_Exception('No parent column name specified and object has mutliple parents');
      }
      elseif (count($parents) < 1)
      {
        throw new Doctrine_Exception('No parent column name specified and object has no parents');
      }
      else
      {
        $parent_column_name = $parents[0]->getType();
        exit((string) $parent_column_name);
        exit(print_r($parents[0]->toArray()));
      }
    }
    
    $query = Doctrine_Query::create()
                           ->from($class . ' od')
                           ->where('od.' . $parent_column_name . ' = ?', $parent_value)
                           ->orderBy('position ' . $order);

    return $query->execute();
  }


  /**
   * Formats the ORDER for insertion in to query, else throws exception
   *
   * @param string $order
   * @return $order
   */
  public function formatAndCheckOrder($order)
  {
    $order = strtolower($order);

    if ($order == 'ascending' || $order == 'asc')
    {
      $order = 'ASC';
    }
    elseif ($order == 'descending' || $order == 'desc')
    {
      $order = 'DESC';
    }
    else
    {
      throw new Doctrine_Exception('Order parameter value must be "asc" or "desc"');
    }
    
    return $order;
  }

  /**
   * Get the final position of a model
   *
   * @return $position
   */
  public function getFinalPosition()
  {
    // Define invoker.
    $invoker = $this->getInvoker();

    // Prepare query.
    $query = Doctrine::getTable($invoker->getTable()->getComponentName())->createQuery('st')
              ->select('st.position')
              ->orderBy('st.position DESC')
              ->limit(1);

    // If object has template FlexibleTree include level.
    if ($invoker->getTable()->hasTemplate('FlexibleTree'))
    {
      if ((int) $invoker['level'])
      {
        $query->andWhere('st.level = ?', $invoker['level']);
      }

      if ((int) $invoker['parent_id'])
      {
        $query->andWhere('st.parent_id = ?', $invoker['parent_id']);
      }

      // If FlexibleTree behavior use hasManyRoots - include root column.
      if ($invoker->getTable()->getTree()->getAttribute('hasManyRoots'))
      {
        $rootColumnName = $invoker->getTable()->getTree()->getAttribute('rootColumnName');

        if ((int) $invoker[$rootColumnName])
        {
          $query->andWhere('st.' . $rootColumnName . ' = ?', $invoker[$rootColumnName]);
        }
      }
    }
   
    return (int) $query->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
  }
}
