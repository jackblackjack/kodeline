<?php
/**
 * klCmsUsersPluginRouting  class.
 * Listener to the routing.load_configuration event.
 * 
 * @package     kodeline-cms
 * @subpackage  klCmsUsersPlugin
 * @category    routing
 * @author      Kodeline
 * @version     $Id$
 */
class klCmsUsersPluginRouting
{
  /**
   * Sets the routes for klCmsUserCommon module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsUserCommon(sfEvent $event)
  {
    $route = $event->getSubject();

    // User signin.
    $route->prependRoute('user_login', 
      new sfRoute('/login/', 
                  array('module' => 'klCmsUserCommon', 'action' => 'login'),
                  array('sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // User signout.
    $route->prependRoute('user_logout', 
      new sfRoute('/logout/', 
                  array('module' => 'klCmsUserCommon', 'action' => 'logout'),
                  array('sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // User secure.
    $route->prependRoute('user_secure', 
      new sfRoute('/secure/', 
                  array('module' => 'klCmsUserCommon', 'action' => 'secure'),
                  array('sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for klCmsUserOpen module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsUserOpen(sfEvent $event)
  {
    $route = $event->getSubject();

    // List supported serices.
    $route->prependRoute('user_signin_by_index', 
      new sfRoute('/signin/by/', 
                  array('module' => 'klCmsUserOpen', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get'), 'sf_culture' => '(?:ru)')
                 )
    );

    // User signin by external service.
    $route->prependRoute('user_signin_by_service', 
      new sfRoute('/signin/by/:service/', 
                  array('module' => 'klCmsUserOpen', 'action' => 'process'),
                  array('service' => '\w+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for klCmsUserSignup module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsUserSignup(sfEvent $event)
  {
    $route = $event->getSubject();

    // Signup user.
    $route->prependRoute('user_signup', 
      new sfRoute('/:sf_culture/signup/', 
                  array('module' => 'klCmsUserSignup', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Signup successfull.
    $route->prependRoute('user_signup_success', 
      new sfRoute('/:sf_culture/signup/success/', 
                  array('module' => 'klCmsUserSignup', 'action' => 'success'),
                  array('sf_format' => 'html', 'sf_method' => array('get'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Signup activate.
    $route->prependRoute('user_signup_activate', 
      new sfRoute('/:sf_culture/activate/:username/:key/', 
                  array('module' => 'klCmsUserSignup', 'action' => 'activate'),
                  array('username' => '\w+', 'key' => '\w+', 'sf_format' => array('html', 'json'), 'sf_method' => array('get'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for klCmsUserForgot module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForKlCmsUserForgot(sfEvent $event)
  {
    $route = $event->getSubject();

    // Forgot password index
    $route->prependRoute('user_forgot_password', 
      new sfRoute('/:sf_culture/i/forgot/password/', 
                  array('module' => 'klCmsUserForgot', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Reset password confirm
    $route->prependRoute('user_forgot_password_reset_confirm', 
      new sfRoute('/:sf_culture/password/reset/:username/:key/', 
                  array('module' => 'klCmsUserForgot', 'action' => 'confirm'),
                  array('username' => '\w+', 'key' => '\w+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for backendKlCmsUser module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForBackendKlCmsUser(sfEvent $event)
  {
    $route = $event->getSubject();

    // List users
    $route->prependRoute('backend_user_index', 
      new sfRoute('/:sf_culture/users/', 
                  array('module' => 'backendKlCmsUser', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // New user
    $route->prependRoute('backend_user_new', 
      new sfRoute('/:sf_culture/user/add/', 
                  array('module' => 'backendKlCmsUser', 'action' => 'new'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit user
    $route->prependRoute('backend_user_edit', 
      new sfRoute('/:sf_culture/user/:id_user/edit/', 
                  array('module' => 'backendKlCmsUser', 'action' => 'edit'),
                  array('id_user' => '\d+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit user
    $route->prependRoute('backend_user_delete', 
      new sfRoute('/:sf_culture/user/:id_user/delete/', 
                  array('module' => 'backendKlCmsUser', 'action' => 'delete'),
                  array('id_user' => '\d+', 'sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for backendKlCmsUserField module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForBackendKlCmsUserField(sfEvent $event)
  {
    $route = $event->getSubject();

    // List user fields
    $route->prependRoute('backend_user_field_index', 
      new sfRoute('/:sf_culture/user/:id_user/fields/', 
                  array('module' => 'backendKlCmsUserField', 'action' => 'index'),
                  array('id_user' => '\d+', 'sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // New user field
    $route->prependRoute('backend_user_field_new', 
      new sfRoute('/:sf_culture/user/:id_user/field/new/', 
                  array('module' => 'backendKlCmsUserField', 'action' => 'new'),
                  array('id_user' => '\d+', 'sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit user field
    $route->prependRoute('backend_user_field_edit', 
      new sfRoute('/:sf_culture/user/:id_user/field/:id_field/edit/', 
                  array('module' => 'backendKlCmsUserField', 'action' => 'edit'),
                  array('id_user' => '\d+', 'id_field' => '\d+', 'sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Delete user field
    $route->prependRoute('backend_user_field_delete', 
      new sfRoute('/:sf_culture/user/:id_user/field/:id_field/delete/', 
                  array('module' => 'backendKlCmsUserField', 'action' => 'delete'),
                  array('id_user' => '\d+', 'id_field' => '\d+', 'sf_format' => array('html', 'json'), 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for backendKlCmsUserGroup module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForBackendKlCmsUserGroup(sfEvent $event)
  {
    $route = $event->getSubject();

    // List groups
    $route->prependRoute('backend_user_group_index', 
      new sfRoute('/usergroups/', 
                  array('module' => 'backendKlCmsUserGroup', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // New group
    $route->prependRoute('backend_user_group_new', 
      new sfRoute('/usergroups/new/', 
                  array('module' => 'backendKlCmsUserGroup', 'action' => 'new'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit group
    $route->prependRoute('backend_user_group_edit', 
      new sfRoute('/usergroups/:id_usergroup/edit/', 
                  array('module' => 'backendKlCmsUserGroup', 'action' => 'edit'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Delete group
    $route->prependRoute('backend_user_group_delete', 
      new sfRoute('/usergroups/:id_usergroup/delete/', 
                  array('module' => 'klCmsUserGroup', 'action' => 'delete'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }

  /**
   * Sets the routes for backendKlCmsPermission module
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutesForBackendKlCmsPermission(sfEvent $event)
  {
    $route = $event->getSubject();

    // List groups
    $route->prependRoute('backend_permission_index', 
      new sfRoute('/permissions/', 
                  array('module' => 'backendKlCmsPermission', 'action' => 'index'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // New group
    $route->prependRoute('backend_permission_new', 
      new sfRoute('/permissions/new/', 
                  array('module' => 'backendKlCmsPermission', 'action' => 'new'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Edit group
    $route->prependRoute('backend_permission_edit', 
      new sfRoute('/permissions/:id_permission/edit/', 
                  array('module' => 'backendKlCmsPermission', 'action' => 'edit'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );

    // Delete group
    $route->prependRoute('backend_permission_delete', 
      new sfRoute('/permissions/:id_permission/delete/', 
                  array('module' => 'backendKlCmsPermission', 'action' => 'delete'),
                  array('sf_format' => 'html', 'sf_method' => array('get', 'post'), 'sf_culture' => '(?:ru)')
                 )
    );
  }
}
