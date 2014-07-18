<?php
class klCmsLayoutManagerPluginMenu
{
  public static function getBackendMenu(sfEvent $event)
  {
    // Define plugin menu.
    $arPluginMenu = array(
      array(
        'name'  => 'Шаблоны сайта',
        'url'   => '@backend_layout_index'
      ),
      array(
        'name'  => 'Новый шаблон',
        'url'   => '@backend_layout_new'
      )
    );

    $existsMenu = $event->getSubject();

    if (! isset($existsMenu['structure'])) $existsMenu['structure'] = array();
    $existsMenu['structure'][] = $arPluginMenu;
  }
}

