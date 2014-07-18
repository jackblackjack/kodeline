<?php
/**
 * Listener for the Complaintable behavior which 
 * allow to adds complaints to tables records.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Credentialable
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Complaintable extends Doctrine_Record_Listener
{
  /**
   * Array of complaintable options
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