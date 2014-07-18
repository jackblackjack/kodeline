<?php
class klCmsLayoutManagerPluginRouting
{
  /**
   * Listener to the routing.load_configuration event.
   * Sets the routes for klCmsLayoutManager module.
   * 
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsLayoutManager(sfEvent $event)
  {
    $route = $event->getSubject();

    // Route for index.
    $route->prependRoute('backend_layout_index', 
      new sfRoute('/:sf_culture/layout/', 
                  array('module' => 'klCmsLayoutManager', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Listener to the routing.load_configuration event.
   * Sets the routes for klCmsLayoutCreator module.
   * 
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsLayoutCreator(sfEvent $event)
  {
    $route = $event->getSubject();

    // Route for index of updating.
    $route->prependRoute('backend_layout_new', 
      new sfRoute('/:sf_culture/layout/new/', 
                  array('module' => 'klCmsLayoutCreator', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
