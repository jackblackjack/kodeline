<?php
/**
 * Class for catch and processing user's profile events.
 * 
 * @package     jWidgetsPlugin
 * @subpackage  lib
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jWidgetsPluginListener
{
  /**
   * Process method for "suggest.search".
   * @param sfEvent $event
   */
  public static function listenToSearchEvent(sfEvent $event, sfGuardUser $user)
  {
  }
}