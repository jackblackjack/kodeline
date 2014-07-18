<?php
abstract class BaseKlCmsUserOpenComponents extends yaBaseComponents
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
