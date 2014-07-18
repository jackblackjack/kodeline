<?php
/**
 * Listener for the watchable behavior 
 * which allows add watchers to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Watchable extends Doctrine_Record_Listener
{
  /**
   * Array of watchable options
   * @var array
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
}