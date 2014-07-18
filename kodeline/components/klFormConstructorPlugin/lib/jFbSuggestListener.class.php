<?php
/**
 * Class for catch and processing user's profile events.
 * 
 * @package     jDoctrineProfilePlugin
 * @subpackage  lib
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jFbSuggestListener
{
  /**
   * Process method for "suggest.search".
   * @param sfEvent $event
   */
  public static function listenToSearchEvent(sfEvent $event, sfGuardUser $user)
  {
  }
}