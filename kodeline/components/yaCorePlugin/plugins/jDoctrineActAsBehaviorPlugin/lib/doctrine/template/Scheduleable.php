<?php
/**
 * Template for the scheduleable behavior 
 * which allows add schedule to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Scheduleable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Scheduleable extends Behavior_Template
{
  /**
   * Array of scheduleable options
   * 
   * @var array
   */
  protected $_options = array('alias' => '', 'name' => 'is_scheduled');

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
   * Call set table definition for the Scheduleable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {    
    // Definition of the column.
    $columnName = $this->_options['name'];
    
    if (! empty($this->_options['alias'])) {
      $columnName .= ' as ' . $this->_options['alias'];
    }
    
    $this->hasColumn($columnName, 'integer', 1, array('nonull' => true, 'default' => 0));
    $this->addListener(new Doctrine_Template_Listener_Scheduleable($this->_options));
  }
}
