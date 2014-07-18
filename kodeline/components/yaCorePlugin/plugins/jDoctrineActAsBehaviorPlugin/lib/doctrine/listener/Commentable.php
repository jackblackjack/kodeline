<?php
/**
 * Listener for the commentable behavior 
 * which allows add comments to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Commentable extends Doctrine_Record_Listener
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

  /**
    * Set the created and updated Timestampable columns when a record is inserted
    *
    * @param Doctrine_Event $event
    * @return void
    */
  public function preInsert(Doctrine_Event $event)
  {
    $fieldName = $event->getInvoker()->getTable()->getFieldName($this->_options['commentable']['name']);
    $modified = $event->getInvoker()->getModified();
    
    if ( ! isset($modified[$fieldName]))
    {
      $event->getInvoker()->$fieldName = (int) $this->_options['enabled'];
    }
  }
  
  /**
    * Remove all comments for the record table.
    *
    * @param Doctrine_Event $evet
    * @return void
    */
  public function postDelete(Doctrine_Event $event)
  {
    if ($event->getInvoker()->hasComments())
    {
      $event->getInvoker()->getComments()->delete();
    }
  }
}