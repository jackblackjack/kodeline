<?php
/**
 * jSuggestFbRouting class.
 *
 * @package     
 * @subpackage  lib
 * @category    routing
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jFbSuggestRouting
{
  /**
   * Listens to the routing.load_configuration event.
   * @param sfEvent An sfEvent instance
   */
  public static function addSearchRoute(sfEvent $event)
  {
    $route = $event->getSubject();
    $route->prependRoute('j_fb_suggest_search', new sfRoute('/json/suggest/multi/:action/', array('module' => 'jDoctrineFBSuggestJson')));
  }
}
