<?php
class klCmsStructureRouting
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
    $route->prependRoute('backend_structure_index', 
      new sfRoute('/:sf_culture/structure/', 
                  array('module' => 'klCmsStructure', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
