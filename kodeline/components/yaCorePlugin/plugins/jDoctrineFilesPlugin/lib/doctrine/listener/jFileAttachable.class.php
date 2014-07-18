<?php
/**
 * Doctrine_Template_Listener object for jFileAttachable behavior.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  jFileAttachable
 * @category    listener
 * @author      chugarev@gmail.com
 */
class Doctrine_Template_Listener_jFileAttachable extends Doctrine_Record_Listener
{
  /**
   * Post save event for object.
   * 
   * Add attachments for new record table.
   */
  public function postSave(Doctrine_Event $event)
  {
    // Define destination record object.
    $object = $event->getInvoker();
    
    if (! $object->getId() && isset($object['Attachments']))
    {
      foreach ($object['Attachments'] as $attachment) 
      {
        $object->addAttachment(new jFile($attachment));
      }
    }
  }
 
  /**
   * Remove all attachments from registry and file table.
   * 
   * @see Doctrine_Record::postDelete
   */
  public function postDelete(Doctrine_Event $event)
  {
    if ($event->getInvoker()->hasComments())
    {
      $event->getInvoker()->getAttachments()->delete();
    }
  }
}