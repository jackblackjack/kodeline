<?php
/**
 * Template for the Ignoreable behavior which allows to 
 * ignoring records by foreign components.
 *
 * @package jDoctrineActAsBehaviorPlugin
 * @subpackage Ignoreable
 * @category template
 */
class Doctrine_Template_Ignoreable extends Behavior_Template
{
  /**
   * Array of ignoreable options
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
}