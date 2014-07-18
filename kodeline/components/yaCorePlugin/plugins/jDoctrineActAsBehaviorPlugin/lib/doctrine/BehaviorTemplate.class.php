<?php
/**
 * Base class for behaviors.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviors
 * @category    base
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class Behavior_Template extends Doctrine_Template
{
  /**
   * Array of the types for models.
   * @var array
   */
  protected static $componentsCache = array();

  /**
   * Return id of the component.
   * 
   * @param string $componentName Component name
   * @return integer|null
   */
  final public function fetchComponentId($componentName)
  {
    // Check table exists.
    if (! $this->getInvoker()->getTable()->existsTable(
      $this->getInvoker()->getTable()->getConnection(), $this->getInvoker()->getTable()->getTableName()))
    {
      return null;
    }

    return BehaviorTemplateToolkit::getComponentIdByName($componentName);
  }
}
