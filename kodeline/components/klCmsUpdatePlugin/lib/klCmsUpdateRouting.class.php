<?php
/**
 * klCmsUpdatePlugin routing.
 * 
 * @package     kodeline-cms
 * @subpackage  routing
 * @author      Kodeline
 * @version     $Id$
 */
class klCmsUpdateRouting
{
  /**
   * Listener to the routing.load_configuration event.
   * Sets the routes.
   * 
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutes(sfEvent $event)
  {
    $route = $event->getSubject();

    // Route for index of updating.
    $route->prependRoute('sys_update_index', 
      new sfRoute('/:sf_culture/update/', 
                  array('module' => 'klCmsUpdate', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
