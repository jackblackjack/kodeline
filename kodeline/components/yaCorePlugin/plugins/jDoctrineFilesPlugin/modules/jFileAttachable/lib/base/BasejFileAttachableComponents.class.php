<?php
/**
 * Base component for behavior attachment files actions.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  jDoctrineFile
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BasejFileAttachableComponents extends yaBaseComponents
{
  /**
   * Returns a random generated key.
   *
   * @param int $len The key length
   * @return string
   */
  protected function generateRandomKey($len = 20)
  {
    return base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
  }

  /**
   * Add new attachment.
   * @param sfWebRequest $request Web request.
   */
  public function executeAttachments(sfWebRequest $request)
  {
    // Generate unique key for attachments.
    $this->uniqueKey = $this->generateRandomKey();

    return sfView::SUCCESS;
  }
}