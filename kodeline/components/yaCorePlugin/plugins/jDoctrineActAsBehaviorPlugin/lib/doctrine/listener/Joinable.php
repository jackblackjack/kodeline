<?php
/**
 * Template for the joinable behavior 
 * which allows make links between components.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Joinable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Joinable extends Doctrine_Record_Listener
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