<?php
/**
 * File attachable listener for managment atatched files.
 * 
 * @package     jDoctrineFilesPlugin
 * @subpackage  lib
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jFileAttachableListener
{
  /**
   * Processing link attached files for object.
   * 
   * @param sfEvent $event
   */
  public static function listenToLinkageObjectEvent(sfEvent $event)
  {
    // Initialize.
    $dispatcher = ya::getEventDispatcher();
    $app = ya::getConfiguration()->getApplication();

    // If empty attached object.
    if (empty($event['object']))
    {
      $dispatcher->notify(new sfEvent($app, 'application.log', 
        array('Object for attachable is not present', 'priority' => sfLogger::WARNING)));
    }

    // If object is not instanceof sfDoctrineRecord
    if (! ($event['object'] instanceof sfDoctrineRecord))
    {
      $dispatcher->notify(new sfEvent($app, 'application.log', 
        array('Object for attachable is sfDoctrineRecord instance', 'priority' => sfLogger::WARNING)));
    }

    // Fetch user's 'attachable.posted' attribute.
    $arPostedFiles = ya::getUser()->getAttribute('attachable.posted');

    // If object is not instanceof sfDoctrineRecord
    if (! empty($arPostedFiles))
    {
      $dispatcher->notify(new sfEvent($app, 'application.log', 
        array('Posted files list is empty', 'priority' => sfLogger::WARNING)));
    }

    // Fetch invoker component id.
    $iComponentId = BehaviorTemplateToolkit::getComponentIdByName($event['object']->getTable()->getComponentName());

    // Create collection for save values.
    $componentCollection = new Doctrine_Collection('jFileAttachment');

    foreach($arPostedFiles as $arFile)
    {
      $attachment = new jFileAttachment();

      $attachment['component_id'] = $iComponentId;
      $attachment['record_id'] = $event['object']->getId();
      $attachment['file_id'] = $arFile['id'];

      $componentCollection->add($attachment);
    }

    $componentCollection->save();
  }
}