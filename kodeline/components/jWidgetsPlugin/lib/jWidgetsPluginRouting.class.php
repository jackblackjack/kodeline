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
class jWidgetsPluginRouting
{
  /**
   * Listens to the routing.load_configuration event.
   * @param sfEvent An sfEvent instance
   */
  public static function addSearchRoute(sfEvent $event)
  {
    $route = $event->getSubject();
    $route->prependRoute('j_widgets_suggest_search', new sfRoute('/json/suggest/:action/', array('module' => 'jWidgetsAutocompleteJson')));
  }
}
