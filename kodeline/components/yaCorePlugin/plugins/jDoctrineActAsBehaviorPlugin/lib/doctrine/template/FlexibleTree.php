<?php
/**
 * Template for the FlexibleTree behavior which 
 * adds support flexy tree, implements 
 * Materialized path and Adjacency list algorithms for trees.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  FlexibleTree
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_FlexibleTree extends Behavior_Template
{
  /**
   * Array of complaintable options
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
   * Set up flexibletree template
   *
   * @return void
   */
  public function setUp()
  {
    $this->_table->setOption('treeOptions', $this->_options);
    $this->_table->setOption('treeImpl', 'FlexibleTree');
  }

  /**
   * Call set table definition for the FlexibleTree behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    $this->_table->getTree()->setTableDefinition();

    $this->addListener(new Doctrine_Template_Listener_FlexibleTree($this->_options));
  }

  /**
   * @see Doctrine_Node_FlexibleTree::hasChildren
   */
  public function hasChildren()
  {
    return $this->getInvoker()->getNode()->hasChildren();
  }

  /**
   * @see Doctrine_Node_FlexibleTree::getChildren
   */
  public function getChildrenRecords()
  {
    return $this->getInvoker()->getNode()->getChildren();
  }
}
