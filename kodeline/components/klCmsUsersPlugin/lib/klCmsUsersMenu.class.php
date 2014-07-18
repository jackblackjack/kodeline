<?php
class klCmsUsersMenu
{
  public static function getBackendMenu(sfEvent $event)
  {
    // Define plugin menu.
    $arPluginMenu = array(
      array(
        'name'  => 'Список пользователей',
        'url'   => '@backend_user_index'
      ),
      array(
        'name'  => 'Список групп',
        'url'   => '@backend_user_group_index'
      ),
      array(
        'name'  => 'Список прав',
        'url'   => '@backend_permission_index'
      )
    );

    $existsMenu = $event->getSubject();

    if (! isset($existsMenu['users'])) $existsMenu['users'] = array();
    $existsMenu['users'][] = $arPluginMenu;
  }
}

