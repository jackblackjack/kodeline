<?php
/**
 * Doctrine_Template_Listener object for Restrictable behavior.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Restrictable
 * @category    listener
 * @author      chugarev@gmail.com
 */
class Doctrine_Template_Listener_Restrictable extends Doctrine_Record_Listener
{
  /**
   * Array of commentable options
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
}