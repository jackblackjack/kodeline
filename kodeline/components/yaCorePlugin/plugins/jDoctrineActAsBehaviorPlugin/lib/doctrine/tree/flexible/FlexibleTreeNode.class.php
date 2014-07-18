<?php
/**
 * Node of the tree for Flexibletree behavior.
 * /*
 * TODO: 
 * Сделать добавление дочернего элемента без выборки родительского и вызова после addChild().
 * Нужно чтобы добавление шло посредством указания ID родительского элемента, 
 * а проверка существования родительского элемента осуществлялось уже в этом методе.
 * 
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Flexibletree
 * @category    Tree node
 * @author      chugarev@gmail.com
 */
class Doctrine_Node_FlexibleTree extends Doctrine_Node
{
  /**
   * Retrieve root node of the tree.
   * 
   * @param boolean $bQueryOnly Return only query for fetch root of the tree.
   * @return Doctrine_Query|Doctrine_Record|boolean
   */
  public function getRoot($bQueryOnly = false)
  {
    // Fetch record path.
    $recordPath = $this->record->get('path');

    // Define root node id.
    $arRecordPath = explode('.', $recordPath);
    $rootId = array_shift($arRecordPath);

    // Define a query for fetch root node.
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery()
              ->addWhere($baseAlias . ".id = ?", $rootId)
              ->addWhere($baseAlias . ".level = ?", 0);
     
    // Extend query for support hasManyRoots option.
    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());

    // Возвращает запрос.
    if ($bQueryOnly) return $query;

    $result = $query->fetchOne(array(), Doctrine_Core::HYDRATE_RECORD);
    return (count($result)) ? $result : false;
  }

  /**
   * Возвращает потомков узла первого уровня.
   */
  public function getChildren($bQueryOnly = false)
  {
    return $this->getDescendants(1, false, $bQueryOnly);
  }

  /**
   * Возвращает всех потомков узла.
   * 
   * @param integer $depth Требуемый уровень вложенности.
   * @param boolean $includeNode Незнаю ниху
   */
  public function getDescendants($depth = null, $includeNode = false, $bQueryOnly = false)
  {
    // Fetch record path.
    $recordPath = $this->record->get('path');

    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery()
              ->addWhere($baseAlias . ".path = ?", sprintf('%d', $this->record->get('id')))
              ->orWhere($baseAlias . ".path LIKE ?", sprintf('%s%d.%s', (empty($recordPath) ? null : $recordPath), $this->record->get('id'), '%'))
              ->addOrderBy($baseAlias . ".level ASC, " . $baseAlias . ".sibling ASC");
     
    if ($depth !== null)
    {
      $query->addWhere($baseAlias . ".level BETWEEN ? AND ?", array($this->record->get('level'), $this->record->get('level') + $depth));
    }
      
    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());

    // Возвращает запрос.
    if ($bQueryOnly) return $query;

    $result = $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
    return (count($result)) ? $result : false;
  }

  /**
   * Возвращает true, если есть предыдущий узел того же уровня.
   * @return bool            
   */
  public function hasPrevSibling()
  {
    return (1 < $this->record['sibling']);
  }

  /**
   * Возвращает true, если есть следующий узел того же уровня.
   * @return bool            
   */
  public function hasNextSibling()
  {
    $parent = $this->getParent();
    return ($parent['children'] > $this->record['sibling']);
  }

  /**
   * Возвращает true если количество потомков узла больше 0.
   * @return bool
   */
  public function hasChildren()
  {
    return (0 < $this->record['children']);
  }

  /**
   * Возвращает true если есть родительский узел.
   * @return bool
   */
  public function hasParent()
  {
    return (null != $this->record['parent_id']);
  }

  /**
   * Возвращает предыдущий узел того же уровня или false, если узла не существует.
   * @return Doctrine_Record            
   */
  public function getPrevSibling()
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere($baseAlias . ".path = ?", $record->get('path'))
          ->addWhere($baseAlias . ".level = ?", $record->get('level'))
          ->addWhere($baseAlias . ".sibling = ?", ($record->get('sibling') - 1));

    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());
    $result = $query->execute();

    if (count($result) <= 0)
    {
      return false;
    }
      
    if ($result instanceof Doctrine_Collection)
    {
      $sibling = $result->getFirst();
    }
    else if (is_array($result)) {
      $sibling = array_shift($result);
    }
      
    return $sibling;
  }

  /**
   * Возвращает следующий узел того же уровня или false, если узла не существует.
   * @return Doctrine_Record            
   */
  public function getNextSibling()
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere($baseAlias . ".path = ?", $record->get('path'))
          ->addWhere($baseAlias . ".level = ?", $record->get('level'))
          ->addWhere($baseAlias . ".sibling = ?", ($record->get('sibling') + 1));

    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());
    $result = $query->execute();

    if (count($result) <= 0)
    {
      return false;
    }
      
    if ($result instanceof Doctrine_Collection)
    {
      $sibling = $result->getFirst();
    }
    else if (is_array($result))
    {
      $sibling = array_shift($result);
    }
      
    return $sibling;
  }

  /**
   * Возвращает все узлы того же уровня что и текущий.
   * @return Doctrine_Record            
   */
  public function getSiblings($includeNode = false)
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere($baseAlias . ".path = ?", $this->record->get('path'))
          ->addWhere($baseAlias . ".level = ?", $this->record->get('level'))
          ->addOrderBy($baseAlias . ".sibling ASC");

    if (! $includeNode)
    {
      $query->addWhere($baseAlias . ".id != ?", $this->record->get('id'));
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * gets record of first child or empty record
   *
   * @return Doctrine_Record            
   */
  public function getFirstChild()
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere($baseAlias . ".parent_id = ?", $record->get('id'))
          ->addWhere($baseAlias . ".siblings = ?", 1)
          ->addWhere($baseAlias . ".level = ?", $record->get('level') + 1);

    $this->_tree->returnQueryWithRootId($query, $this->getRootValue());
    return $query->execute();
  }

  /**
   * gets record of last child or empty record
   *
   * @return Doctrine_Record            
   */
  public function getLastChild()
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere($baseAlias . ".parent_id = ?", $record->get('id'))
          ->addWhere($baseAlias . ".siblings = ?", $record->get('children'))
          ->addWhere($baseAlias . ".level = ?", $record->get('level') + 1);

    $this->_tree->returnQueryWithRootId($query, $this->getRootValue());
    return $query->execute();    
  }

  /**
   * gets record of parent or empty record
   *
   * @return Doctrine_Record            
   */
  public function getParent()
  {
    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    $query->addWhere("$baseAlias.id = ?", $this->record['parent_id'])
          ->addWhere("$baseAlias.level = ?", $this->record['level'] - 1);

    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());
    return $query->fetchOne();
  }

  /**
   * Возвращает родительские узлы текущего.
   *
   * @param integer $deth  The depth 'upstairs'.
   * @return mixed  The ancestors of the node or FALSE if the node has no ancestors (this 
   *                basically means it's a root node).                
   */
  public function getAncestors($depth = null)
  {
    // Return a empty doctrine collection if record is root.
    if ($this->isRoot()) return new Doctrine_Collection($this->record->getTable());

    $baseAlias = $this->_tree->getBaseAlias();
    $query = $this->_tree->getBaseQuery();

    // Explode ids.
    $arIds = explode('.', $this->record['path']);

    // Prepare query.
    $query->addWhere($baseAlias . '.id IN (' . implode(',', array_fill(0, count($arIds), '?')) . ')', $arIds)
          ->addOrderBy($baseAlias . ".level ASC, " . $baseAlias . ".sibling ASC");

    if ($depth !== null)
    {
      $query->addWhere("$baseAlias.level >= ?", $this->record['level'] - $depth);
    }

    $query = $this->_tree->returnQueryWithRootId($query, $this->getRootValue());

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * Return path to node from root, uses record::toString() method to get node names
   *
   * @param string     $seperator     path seperator
   * @param bool     $includeNode     whether or not to include node at end of path
   * @return string     string representation of path                
   */     
  public function getNodePath($includeRecord = false)
  {
    // Explode node path to IDs.
    $path = explode('.', $this->record['path']);

    $ancestors = $this->getAncestors();
    if ($includeRecord) $ancestors->add($this->record);

    return $ancestors;
  }

  /**
   * gets number of children (direct descendants)
   *
   * @return int            
   */     
  public function getNumberChildren()
  {
    return $this->record['children'];
  }

  /**
   * gets number of descendants (children and their children)
   *
   * @return int            
   */
  public function getNumberDescendants()
  {
    $descendants = $this->getDescendants();
    return count($descendants);
  }

  /**
   * inserts node as parent of dest record
   *
   * @return bool
   * @todo Wrap in transaction          
   */
  public function insertAsParentOf(Doctrine_Record $dest)
  {
    // cannot insert as parent of root
    if ($dest->getNode()->isRoot())
    {
      return false;
    }
      
    // cannot insert as parent of itself
    if ($dest === $this->record || ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier()))
    {
      throw new Doctrine_Tree_Exception("Cannot insert node as parent of itself");
      return false;
    }

    $conn = $this->record->getTable()->getConnection();
    try {
      $conn->beginInternalTransaction();
      
      //$query = $this->_tree->returnQueryWithRootId($query, $newRoot);
      $query->execute();

      $conn->commit();
    }
    catch (Exception $exception)
    {
      $conn->rollback();
      throw $exception;
    }

    return true;
  }

  /**
   * inserts node as previous sibling of dest record
   *
   * @return bool
   * @todo Wrap in transaction       
   */
  public function insertAsPrevSiblingOf(Doctrine_Record $dest)
  {
      // cannot insert as sibling of itself
      if (
      $dest === $this->record ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot insert node as previous sibling of itself");

          return false;
      }

      $newLeft = $dest->getNode()->getLeftValue();
      $newRight = $dest->getNode()->getLeftValue() + 1;
      $newRoot = $dest->getNode()->getRootValue();
      
      $conn = $this->record->getTable()->getConnection();
      try {
          $conn->beginInternalTransaction();
          
          $this->shiftRLValues($newLeft, 2, $newRoot);
          $this->record['level'] = $dest['level'];
          $this->insertNode($newLeft, $newRight, $newRoot);
          // update destination left/right values to prevent a refresh
          // $dest->getNode()->setLeftValue($dest->getNode()->getLeftValue() + 2);
          // $dest->getNode()->setRightValue($dest->getNode()->getRightValue() + 2);
          
          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }
                      
      return true;
  }

  /**
   * inserts node as next sibling of dest record
   *
   * @return bool
   * @todo Wrap in transaction           
   */    
  public function insertAsNextSiblingOf(Doctrine_Record $dest)
  {
      // cannot insert as sibling of itself
      if ($dest === $this->record || ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())) {
        throw new Doctrine_Tree_Exception("Cannot insert node as next sibling of itself");
        return false;
      }

      $newLeft = $dest->getNode()->getRightValue() + 1;
      $newRight = $dest->getNode()->getRightValue() + 2;
      $newRoot = $dest->getNode()->getRootValue();

      $conn = $this->record->getTable()->getConnection();
      try {
          $conn->beginInternalTransaction();
          
          $this->shiftRLValues($newLeft, 2, $newRoot);
          $this->record['level'] = $dest['level'];
          $this->insertNode($newLeft, $newRight, $newRoot);
          // update destination left/right values to prevent a refresh
          // no need, node not affected
          
          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }

      return true;
  }

  /**
   * inserts node as first child of dest record
   *
   * @return bool
   * @todo Wrap in transaction         
   */
  public function insertAsFirstChildOf(Doctrine_Record $dest)
  {
      // cannot insert as child of itself
      if (
      $dest === $this->record ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot insert node as first child of itself");

          return false;
      }

      $newLeft = $dest->getNode()->getLeftValue() + 1;
      $newRight = $dest->getNode()->getLeftValue() + 2;
      $newRoot = $dest->getNode()->getRootValue();

      $conn = $this->record->getTable()->getConnection();
      try {
          $conn->beginInternalTransaction();
          
          $this->shiftRLValues($newLeft, 2, $newRoot);
          $this->record['level'] = $dest['level'] + 1;
          $this->insertNode($newLeft, $newRight, $newRoot);
          
          // update destination left/right values to prevent a refresh
          // $dest->getNode()->setRightValue($dest->getNode()->getRightValue() + 2);
          
          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }

      return true;
  }

  /**
   * inserts node as last child of dest record
   *
   * @return bool
   * @todo Wrap in transaction            
   */
  public function insertAsLastChildOf(Doctrine_Record $parent)
  {   
    // cannot insert as child of itself
    if ($parent === $this->record || ($parent->exists() && $this->record->exists() && $parent->identifier() === $this->record->identifier()))
    {
      throw new Doctrine_Tree_Exception("Cannot insert node as last child of itself");
      return false;
    }

    $conn = $this->record->getTable()->getConnection();

    try {
      $conn->beginInternalTransaction();

      // If tree use hasManyRoots - set value of the root column.
      if ($this->_tree->getAttribute('hasManyRoots'))
      {
        $this->record->set($this->_tree->getAttribute('rootColumnName'), $parent->get($this->_tree->getAttribute('rootColumnName')));
      }    

      // Set values of child.
      $this->record['parent_id'] = $parent['id'];
      $this->record['level'] = $parent['level'] + 1;
      $this->record['sibling'] = $parent['children'] + 1;

      $sRecordPath = (empty($parent['path']) ? $parent['id'] . '.' : sprintf('%s%d.', $parent['path'], $parent['id']));
      $this->record['path'] = $sRecordPath;
      $this->record->save();

      // Set values of the parent node.
      $parent['children'] = $parent['children'] + 1;
      $parent->save();

      $conn->commit();

      // Trigger event 'flextree.add_child'.
      //sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent(null, 'flextree.add_child', array('id' => $this->record['id'])));
    }
    catch (Exception $exception)
    {
      $conn->rollback();
      throw $exception;
    }

    return true;
  }

  /**
   * Accomplishes moving of nodes between different trees.
   * Used by the move* methods if the root values of the two nodes are different.
   *
   * @param Doctrine_Record $dest
   * @param unknown_type $newLeftValue
   * @param unknown_type $moveType
   * @todo Better exception handling/wrapping
   */
  private function _moveBetweenTrees(Doctrine_Record $dest, $newLeftValue, $moveType)
  {
      $conn = $this->record->getTable()->getConnection();
          
      try {
          $conn->beginInternalTransaction();

          // Move between trees: Detach from old tree & insert into new tree
          $newRoot = $dest->getNode()->getRootValue();
          $oldRoot = $this->getRootValue();
          $oldLft = $this->getLeftValue();
          $oldRgt = $this->getRightValue();
          $oldLevel = $this->record['level'];

          // Prepare target tree for insertion, make room
          $this->shiftRlValues($newLeftValue, $oldRgt - $oldLft - 1, $newRoot);

          // Set new root id for this node
          $this->setRootValue($newRoot);
          $this->record->save();

          // Insert this node as a new node
          $this->setRightValue(0);
          $this->setLeftValue(0);

          switch ($moveType) {
              case 'moveAsPrevSiblingOf':
                  $this->insertAsPrevSiblingOf($dest);
              break;
              case 'moveAsFirstChildOf':
                  $this->insertAsFirstChildOf($dest);
              break;
              case 'moveAsNextSiblingOf':
                  $this->insertAsNextSiblingOf($dest);
              break;
              case 'moveAsLastChildOf':
                  $this->insertAsLastChildOf($dest);
              break;
              default:
                  throw new Doctrine_Node_Exception("Unknown move operation: $moveType.");
          }

          $diff = $oldRgt - $oldLft;
          $this->setRightValue($this->getLeftValue() + ($oldRgt - $oldLft));
          $this->record->save();

          $newLevel = $this->record['level'];
          $levelDiff = $newLevel - $oldLevel;

          // Relocate descendants of the node
          $diff = $this->getLeftValue() - $oldLft;
          $componentName = $this->_tree->getBaseComponent();
          $rootColName = $this->_tree->getAttribute('rootColumnName');

          // Update lft/rgt/root/level for all descendants
          $q = Doctrine_Core::getTable($componentName)
              ->createQuery()
              ->update()
              ->set($componentName . '.lft', $componentName.'.lft + ?', $diff)
              ->set($componentName . '.rgt', $componentName.'.rgt + ?', $diff)
              ->set($componentName . '.level', $componentName.'.level + ?', $levelDiff)
              ->set($componentName . '.' . $rootColName, '?', $newRoot)
              ->where($componentName . '.lft > ? AND ' . $componentName . '.rgt < ?', array($oldLft, $oldRgt));
          $q = $this->_tree->returnQueryWithRootId($q, $oldRoot);
          $q->execute();

          // Close gap in old tree
          $first = $oldRgt + 1;
          $delta = $oldLft - $oldRgt - 1;
          $this->shiftRLValues($first, $delta, $oldRoot);

          $conn->commit();
   
        return true;
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }
      
      return false;
  }

  /**
   * moves node as prev sibling of dest record
   * 
   */     
  public function moveAsPrevSiblingOf(Doctrine_Record $dest)
  {
      if (
      $dest === $this->record ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot move node as previous sibling of itself");

        return false;
      }

      if ($dest->getNode()->getRootValue() != $this->getRootValue()) {
          // Move between trees
          return $this->_moveBetweenTrees($dest, $dest->getNode()->getLeftValue(), __FUNCTION__);
      } else {
          // Move within the tree
          $oldLevel = $this->record['level'];
          $this->record['level'] = $dest['level'];
          $this->updateNode($dest->getNode()->getLeftValue(), $this->record['level'] - $oldLevel);
      }
      
      return true;
  }

  /**
   * moves node as next sibling of dest record
   *        
   */
  public function moveAsNextSiblingOf(Doctrine_Record $dest)
  {
      if (
      $dest === $this->record ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot move node as next sibling of itself");
          
          return false;
      }

      if ($dest->getNode()->getRootValue() != $this->getRootValue()) {
          // Move between trees
          return $this->_moveBetweenTrees($dest, $dest->getNode()->getRightValue() + 1, __FUNCTION__);
      } else {
          // Move within tree
          $oldLevel = $this->record['level'];
          $this->record['level'] = $dest['level'];
          $this->updateNode($dest->getNode()->getRightValue() + 1, $this->record['level'] - $oldLevel);
      }
      
      return true;
  }

  /**
   * moves node as first child of dest record
   *            
   */
  public function moveAsFirstChildOf(Doctrine_Record $dest)
  {
      if (
      $dest === $this->record || $this->isAncestorOf($dest) ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot move node as first child of itself or into a descendant");

        return false;
      }

      if ($dest->getNode()->getRootValue() != $this->getRootValue()) {
          // Move between trees
          return $this->_moveBetweenTrees($dest, $dest->getNode()->getLeftValue() + 1, __FUNCTION__);
      } else {
          // Move within tree
          $oldLevel = $this->record['level'];
          $this->record['level'] = $dest['level'] + 1;
          $this->updateNode($dest->getNode()->getLeftValue() + 1, $this->record['level'] - $oldLevel);
      }

      return true;
  }

  /**
   * moves node as last child of dest record
   *        
   */
  public function moveAsLastChildOf(Doctrine_Record $dest)
  {
      if (
      $dest === $this->record || $this->isAncestorOf($dest) ||
      ($dest->exists() && $this->record->exists() && $dest->identifier() === $this->record->identifier())
      ) {
          throw new Doctrine_Tree_Exception("Cannot move node as last child of itself or into a descendant");

        return false;
      }

      if ($dest->getNode()->getRootValue() != $this->getRootValue()) {
          // Move between trees
          return $this->_moveBetweenTrees($dest, $dest->getNode()->getRightValue(), __FUNCTION__);
      } else {
          // Move within tree
          $oldLevel = $this->record['level'];
          $this->record['level'] = $dest['level'] + 1;
          $this->updateNode($dest->getNode()->getRightValue(), $this->record['level'] - $oldLevel);
      }
      
      return true;
  }

  /**
   * Makes this node a root node. Only used in multiple-root trees.
   *
   * @todo Exception handling/wrapping
   */
  public function l___makeRoot($newRootId)
  {
      // TODO: throw exception instead?
      if ($this->getLeftValue() == 1 || ! $this->_tree->getAttribute('hasManyRoots')) {
          return false;
      }
      
      $oldRgt = $this->getRightValue();
      $oldLft = $this->getLeftValue();
      $oldRoot = $this->getRootValue();
      $oldLevel = $this->record['level'];
      
      $conn = $this->record->getTable()->getConnection();
      try {
          $conn->beginInternalTransaction();
          
          // Update descendants lft/rgt/root/level values
          $diff = 1 - $oldLft;
          $newRoot = $newRootId;
          $componentName = $this->_tree->getBaseComponent();
          $rootColName = $this->_tree->getAttribute('rootColumnName');
          $q = Doctrine_Core::getTable($componentName)
              ->createQuery()
              ->update()
              ->set($componentName . '.lft', $componentName.'.lft + ?', array($diff))
              ->set($componentName . '.rgt', $componentName.'.rgt + ?', array($diff))
              ->set($componentName . '.level', $componentName.'.level - ?', array($oldLevel))
              ->set($componentName . '.' . $rootColName, '?', array($newRoot))
              ->where($componentName . '.lft > ? AND ' . $componentName . '.rgt < ?', array($oldLft, $oldRgt));
          $q = $this->_tree->returnQueryWithRootId($q, $oldRoot);
          $q->execute();
          
          // Detach from old tree (close gap in old tree)
          $first = $oldRgt + 1;
          $delta = $oldLft - $oldRgt - 1;
          $this->shiftRLValues($first, $delta, $this->getRootValue());
          
          // Set new lft/rgt/root/level values for root node
          $this->setLeftValue(1);
          $this->setRightValue($oldRgt - $oldLft + 1);
          $this->setRootValue($newRootId);
          $this->record['level'] = 0;
          
          $this->record->save();
          
          $conn->commit();
          
          return true;
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }
      
      return false;
  }

  /**
   * Добавление потомка последним в ветке.
   * 
   * @param Doctrine_Record $record
   */
  public function addChild(Doctrine_Record $child)
  {
    return $child->getNode()->insertAsLastChildOf($this->getRecord());
  }

  /**
   * Determines if node is leaf
   * @return bool            
   */
  public function isLeaf()
  {
    $baseAlias = $this->_tree->getBaseAlias();
      $q = $this->_tree->getBaseQuery();
      $q->addWhere("$baseAlias.lft = ?", $this->getLeftValue() + 1);
      $this->_tree->returnQueryWithRootId($q, $this->getRootValue());
      $result = $q->execute();

      if (count($result) <= 0) {
          return false;
      }
      
      if ($result instanceof Doctrine_Collection) {
          $child = $result->getFirst();
      } else if (is_array($result)) {
          $child = array_shift($result);
      }

    return (($this->getRightValue() - $this->getLeftValue()) == 1);
  }

  /**
   * Возвращает true если узел является корневым.
   * @return bool            
   */
  public function isRoot()
  {
    return (null == $this->record['parent_id'] && null == $this->record['path'] && 0 == $this->record['level']);
  }

  /**
   * determines if node is equal to subject node
   *
   * @return bool            
   */    
  public function isEqualTo(Doctrine_Record $compare)
  {
    return (
            ($this->record['parent_id'] == $compare->getNode()->getParentId()) &&
            ($this->record['path'] == $compare->getNode()->getPath()) &&
            ($this->record['level'] == $compare->getNode()->getLevel())
           );
  }

  /**
   * determines if node is child of subject node
   *
   * @return bool
   */
  public function isDescendantOf(Doctrine_Record $subj)
  {
      return (($this->getLeftValue() > $subj->getNode()->getLeftValue()) &&
              ($this->getRightValue() < $subj->getNode()->getRightValue()) &&
              ($this->getRootValue() == $subj->getNode()->getRootValue()));
  }

  /**
   * determines if node is child of or sibling to subject node
   *
   * @return bool            
   */
  public function isDescendantOfOrEqualTo(Doctrine_Record $subj)
  {
      return (($this->getLeftValue() >= $subj->getNode()->getLeftValue()) &&
              ($this->getRightValue() <= $subj->getNode()->getRightValue()) &&
              ($this->getRootValue() == $subj->getNode()->getRootValue()));
  }

  /**
   * determines if node is ancestor of subject node
   *
   * @return bool            
   */
  public function isAncestorOf(Doctrine_Record $subj)
  {
      return (($subj->getNode()->getLeftValue() > $this->getLeftValue()) &&
              ($subj->getNode()->getRightValue() < $this->getRightValue()) &&
              ($subj->getNode()->getRootValue() == $this->getRootValue()));
  }

  /**
   * Detaches the node from the tree by invalidating it's lft & rgt values
   * (they're set to 0).
   */
  public function l___detach()
  {
      $this->setLeftValue(0);
      $this->setRightValue(0);
  }

  /**
   * Удаление узла и его потомков.
   * @todo Delete more efficiently. Wrap in transaction if needed.      
   */
  public function l___delete()
  {
    $conn = $this->record->getTable()->getConnection();

    try {
      $conn->beginInternalTransaction();
          
      // TODO: add the setting whether or not to delete descendants or relocate children
          $oldRoot = $this->getRootValue();
          $q = $this->_tree->getBaseQuery();

          $baseAlias = $this->_tree->getBaseAlias();
          $componentName = $this->_tree->getBaseComponent();

          $q = $q->addWhere("$baseAlias.lft >= ? AND $baseAlias.rgt <= ?", array($this->getLeftValue(), $this->getRightValue()));

          $q = $this->_tree->returnQueryWithRootId($q, $oldRoot);

          $coll = $q->execute();

          $coll->delete();

          $first = $this->getRightValue() + 1;
          $delta = $this->getLeftValue() - $this->getRightValue() - 1;
          $this->shiftRLValues($first, $delta, $oldRoot);
          
          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }
      
      return true; 
  }

  /**
   * move node's and its children to location $destLeft and updates rest of tree
   *
   * @param int     $destLeft    destination left value
   * @todo Wrap in transaction
   */
  private function l___updateNode($destLeft, $levelDiff)
  { 
      $componentName = $this->_tree->getBaseComponent();
      $left = $this->getLeftValue();
      $right = $this->getRightValue();
      $rootId = $this->getRootValue();

      $treeSize = $right - $left + 1;

      $conn = $this->record->getTable()->getConnection();
      try {
          $conn->beginInternalTransaction();
          
          // Make room in the new branch
          $this->shiftRLValues($destLeft, $treeSize, $rootId);

          if ($left >= $destLeft) { // src was shifted too?
              $left += $treeSize;
              $right += $treeSize;
          }

          // update level for descendants
          $q = Doctrine_Core::getTable($componentName)
              ->createQuery()
              ->update()
              ->set($componentName . '.level', $componentName.'.level + ?', array($levelDiff))
              ->where($componentName . '.lft > ? AND ' . $componentName . '.rgt < ?', array($left, $right));
          $q = $this->_tree->returnQueryWithRootId($q, $rootId);
          $q->execute();

          // now there's enough room next to target to move the subtree
          $this->shiftRLRange($left, $right, $destLeft - $left, $rootId);

          // correct values after source (close gap in old tree)
          $this->shiftRLValues($right + 1, -$treeSize, $rootId);

          $this->record->save();
          $this->record->refresh();
          
          $conn->commit();
      } catch (Exception $e) {
          $conn->rollback();
          throw $e;
      }
      
      return true;
  }

  /**
   * Возвращает значение уровня (вложенность) текущего узла.
   * @return integer
   */    
  public function getLevel()
  {
    return $this->record['level'];
  }

  /**
   * Возвращает ID дерева, если установлена опция hasManyRoots, и null в обратном случае.
   * @return integer|null
   */     
  public function getRootValue()
  {
    if (! $this->_tree->getAttribute('hasManyRoots'))
    {
      return null;
    }

    return $this->record->get($this->_tree->getAttribute('rootColumnName'));   
  }

  /**
   * Установка значения ID дерева.
   * @param integer
   */
  public function setRootValue($value)
  {
    if ($this->_tree->getAttribute('hasManyRoots'))
    {
      $this->record->set($this->_tree->getAttribute('rootColumnName'), $value);
    }
  }
}