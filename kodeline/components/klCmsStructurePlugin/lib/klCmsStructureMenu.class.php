<?php
class klCmsStructureMenu
{
  public static function getBackendMenu(sfEvent $event)
  {
    // Define plugin menu.
    $arPluginMenu = array(
      array(
        'name'  => 'Страницы сайта',
        'url'   => '@homepage'
      )
    );

    $existsMenu = $event->getSubject();

    if (! isset($existsMenu['structure'])) $existsMenu['structure'] = array();
    $existsMenu['structure'][] = $arPluginMenu;
  }
}

