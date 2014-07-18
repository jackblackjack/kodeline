<?php
/**
 * Listener for the FlexibleTree behavior which 
 * adds support flexy tree, implements 
 * Materialized path and Adjacency list algorithms for trees.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  FlexibleTree
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_FlexibleTree extends Doctrine_Record_Listener
{
  /**
   * Array of flexibletree options
   *
   * @var string
   */
  protected $_options = array();

  /**
   * __construct
   *
   * @param string $options 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  /**
   * Update record before insert
   *
   * @param Doctrine_Event $event
   * @return void
   * @author Brent Shaffer
   */
  public function preInsert(Doctrine_Event $event)
  {
    $object = $event->getInvoker();

    /*
    // Update sibling position of the node tree.
    if ($object->getNode()->isRoot() && $object->getTable()->getTree()->getAttribute('hasManyRoots'))
    {
      // Fetch max number of sibling
      $rootColumnName = $object->getTable()->getTree()->getAttribute('rootColumnName');
      $maxSibling = (int) $object->getTable()->getTree()->getBaseQuery()->select('MAX(sibling)')
                          ->andWhere($rootColumnName . ' = ?', $object[$rootColumnName])
                          ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      $object['sibling'] = ($maxSibling + 1);
    }
    */
  }

  /**
   * Set the geocodes automatically when a locatable object's locatable fields are modified
   *
   * @param Doctrine_Event $event
   * @return void
   * @author Brent Shaffer
   */
/*  public function preSave(Doctrine_Event $event)
  {
    $object = $event->getInvoker();
    $modified = array_keys($object->getModified());
    if (array_intersect($this->_options['fields'], $modified)) $object->refreshGeocodes();
  }
*/
}
