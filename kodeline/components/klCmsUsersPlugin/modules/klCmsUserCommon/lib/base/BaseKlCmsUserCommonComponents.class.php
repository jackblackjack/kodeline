<?php
abstract class BaseKlCmsUsersComponents extends yaBaseComponents
{
  /**
   * Action for generate menu.
   */
  public function executeBackendMenu()
  {
    // Fetch menu.
    $this->menu = new klCmsBackendMenu();
  }
}
