<?php
class klCmsSitePluginRouting
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
    $route->prependRoute('backend_domain_index', 
      new sfRoute('/:sf_culture/sites/', 
                  array('module' => 'backendKlCmsSite', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for klCmsUserCommon module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForBackendKlCmsSite(sfEvent $event)
  {
    $route = $event->getSubject();

    // List users
    $route->prependRoute('backend_klsite_index', 
      new sfRoute('/:sf_culture/sites/', 
                  array('module' => 'backendKlCmsSite', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // New user
    $route->prependRoute('backend_klsite_new', 
      new sfRoute('/:sf_culture/site/add/', 
                  array('module' => 'backendKlCmsSite', 'action' => 'new'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit user
    $route->prependRoute('backend_klsite_edit', 
      new sfRoute('/:sf_culture/site/:id_site/edit/', 
                  array('module' => 'backendKlCmsSite', 'action' => 'edit'),
                  array('id_user' => '\d+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit user
    $route->prependRoute('backend_klsite_delete', 
      new sfRoute('/:sf_culture/site/:id_site/delete/', 
                  array('module' => 'backendKlCmsSite', 'action' => 'delete'),
                  array('id_user' => '\d+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
