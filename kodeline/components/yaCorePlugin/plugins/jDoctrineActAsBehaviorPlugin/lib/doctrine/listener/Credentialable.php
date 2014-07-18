<?php
/**
 * Listener for the Credentialable behavior which 
 * automatically sets the credentials of the user to record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Credentialable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Credentialable extends Doctrine_Record_Listener
{
  /**
   * Array of credentialable options
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

  /**
   * Set the created and updated Watchdogable columns when a record is inserted
   *
   * @param Doctrine_Event $event
   * @return void
   */
  public function preInsert(Doctrine_Event $event)
  {
    // TODO: Save current credentials of creator.
  }
}