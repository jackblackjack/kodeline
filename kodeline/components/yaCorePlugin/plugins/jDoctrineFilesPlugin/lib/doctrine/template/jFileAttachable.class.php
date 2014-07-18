<?php
/**
 * Doctrine_Template extends class for jFileAttachable behavior.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  jFileAttachable
 * @category    template
 * @author      chugarev@gmail.com
 */
class Doctrine_Template_jFileAttachable extends Behavior_Template
{
  /**
   * {@inheritDoc}
   */
  public function setTableDefinition()
  {
    $this->addListener(new Doctrine_Template_Listener_jFileAttachable());
  }

  /**
   * Return query for select attachments for object.
   * 
   * @return Doctrine_Query 
   */
  public function getAttachmentsQuery()
  {
    // Define ID of the system component.
    $iComponent = $this->getComponentId($this->getInvoker()->getTable()->getComponentName());

    return Doctrine::getTable('jFileAttachment')->createQuery('jfatt')->select('jfatt.*')
              ->addWhere('jfatt.component_id = ?', $iComponent)
              ->addWhere('jfatt.record_id = ?', $object->getId());
  }

  /**
   * Return Doctrine_Collection of the records for attachments.
   * 
   * @return Doctrine_Collection
   */
  public function getAttachments()
  {
    return $this->getInvoker()->getAttachmentsQuery()->innerJoin('jfatt.File')->execute(array(), Doctrine::HYDRATE_RECORD);
  }

  /**
   * Set attachments for object.
   */
  public function setAttachments(Doctrine_Collection $attachments)
  {
    foreach ($attachments as $attachment) 
    {
      $this->addAttachment($attachment);
    }
  }

  /**
   * Return true if object has attachment records.
   * 
   * @return integer
   */
  public function hasAttachments()
  {
    return ($this->getAttachmentsQuery()->count() > 0);
  }

  /**
   * Add new attachment file.
   * 
   * @param Doctrine_Record $attachment object with record definition.
   */
  public function addAttachment(Doctrine_Record $attachment)
  {
    // Define object.
    $object = $this->getInvoker();

    // Define id of the system's object component.
    $iComponent = $this->getComponentId($this->getInvoker()->getTable()->getComponentName());

    // If object not in database - try save it.
    if (! $object->exists()) { $object->save(); }

    // Create registry record of the attachment.
    $registry = new jFileAttachment();
    $registry['component_id'] = $iComponent;
    $registry['record_id'] = $object['id'];
    $registry['file_id'] = $attachment['id'];
    $registry->save();

    $attachment['is_active'] = true;
    $attachment['fkey'] = null;
    $attachment->save();
  }
}
