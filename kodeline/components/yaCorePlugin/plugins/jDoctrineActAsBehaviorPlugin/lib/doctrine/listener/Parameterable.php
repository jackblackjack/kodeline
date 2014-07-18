<?php
/**
 * Listener for the Parameterable behavior which 
 * allow to adds extended params to table.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Parameterable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Parameterable extends Doctrine_Record_Listener
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