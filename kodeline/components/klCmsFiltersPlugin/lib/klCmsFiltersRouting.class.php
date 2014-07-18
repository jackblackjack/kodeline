<?php
/**
 * klCmsFiltersRouting routing.
 * 
 * @package     kodeline-cms
 * @subpackage  klCmsFiltersPlugin
 * @category    routing
 * @author      Kodeline
 * @version     $Id$
 */
class klCmsFiltersRouting
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
    $route->prependRoute('backend_filters_index', 
      new sfRoute('/:sf_culture/options/filters/', 
                  array('module' => 'klCmsFilters', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
