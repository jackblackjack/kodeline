<?php

/**
 * Extends symfony sfComponents.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.action
 * @author      pinhead
 * @version     SVN: $Id: yaBaseComponents.class.php 2404 2010-10-11 21:09:55Z pinhead $
 */
class yaBaseComponents extends sfComponents
{

  /**
   * @return sfEventDispatcher
   */
  public function getDispatcher()
  {
    return $this->context->getEventDispatcher();
  }

  /**
   * @return sfRouting
   */
  public function getRouting()
  {
    return $this->context->getRouting();
  }

  /**
   * @return sfI18N
   */
  public function getI18n()
  {
    return $this->context->getI18n();
  }
}