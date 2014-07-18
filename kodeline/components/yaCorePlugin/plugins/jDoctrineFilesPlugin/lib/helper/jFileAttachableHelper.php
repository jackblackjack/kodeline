<?php
/**
 * jFileAttachableHelper helper.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  lib
 * @category    helper
 * @author      chugarev
 * @version     $Id$
 */
class jFileAttachableHelper
{
  /**
   * Retrieve unique form key for each form.
   *
   * @param sfForm $form Form which require unique form key.
   * @param string $identifierName Name of the identifier field of the form, which should be base for generation.
   * @return string
   */
  static public function getAttachmentsFormKey(sfForm $form, $identifierName = 'id')
  {
    // Define form class.
    $formClass = get_class($form);

    // If form has default identifier field value (on updates objects).
    if ($form->hasDefault($identifierName) && null !== $form->getDefault($identifierName))
    {
      return self::generateUniqueKey($form->getDefault($identifierName), $formClass);
    }

    // if form CSRF protected - use CSRF token as key.
    if ($form->isCSRFProtected())
    {
      return self::generateUniqueKey($form->getCSRFToken(), $formClass);
    }

    // TODO: Algo for generate form unique key by default.
    throw new sfException(sprintf('Cannot generate unique key for form %s', $form->getName()));
    //return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
  }

  /**
   * Retrieves key for form.
   *
   * @param string $formKey The key of the form.
   * @param string $formClass The form classname.
   * @return string
   */
  static private function generateUniqueKey($formKey, $formClass)
  {
    return md5($formKey . session_id() . $formClass);
  }

  /**
   * Set attachments for record by attachments key.
   * 
   * @param Doctrine_Record_Abstract $record Destination record.
   * @param string $attachmentsKey Key of the attachments.
   */
  static public function setAttachmentsByKey(Doctrine_Record_Abstract $record, $attachmentsKey)
  {
    // If destination record is not have a jFileAttachable template throw stop exception.
    if (! $record->getTable()->hasTemplate('jFileAttachable'))
    {
      throw new sfStopException(sprintf('Record of the table "%s" has not template jFileAttachable', $record->getTable()->getTableName()));
    }

    // Define record attachments list.
    $record->setAttachments(Doctrine::getTable('jFile')->createNamedQuery('get.by.key')->execute(array($attachmentsKey), Doctrine::HYDRATE_RECORD));
  }
}
