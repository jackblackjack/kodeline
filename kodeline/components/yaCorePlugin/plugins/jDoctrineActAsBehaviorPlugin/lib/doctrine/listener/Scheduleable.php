<?php
/**
 * Scheduleable behavior with Doctrine Objects
 *
 * @package jDoctrineActAsBehaviorPlugin
 * @author Alexey G. Chugarev <chugarev@gmail.com>
 */
class Doctrine_Template_Listener_Scheduleable extends Doctrine_Record_Listener
{
  /**
   * Array of scheduleable options
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
  public function __construct(array $options)
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }
}