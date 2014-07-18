<?php
/**
 * @package jDoctrineRateablePlugin
 * @subpackage lib
 * @category doctrine listener
 */
class Doctrine_Template_Listener_Rateable extends Doctrine_Record_Listener
{
  /**
   * {@inhericDoc}
   */
  protected $_options = array();

  /**
   * {@inhericDoc}
   */
  public function __construct(array $options)
  {
    $this->_options = $options;
  }
}