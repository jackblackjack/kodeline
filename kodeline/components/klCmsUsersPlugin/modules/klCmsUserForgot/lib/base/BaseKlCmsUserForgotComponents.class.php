<?php
abstract class BaseKlCmsUserForgotComponents extends yaBaseComponents
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
