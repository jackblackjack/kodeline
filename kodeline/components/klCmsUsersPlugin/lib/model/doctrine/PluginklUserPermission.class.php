<?php
abstract class PluginklUserPermission extends BaseklUserPermission
{
  /**
   * @see Doctrine_Record
   */
  public function postSave($event)
  {
    parent::postSave($event);
    $this->getUser()->reloadGroupsAndPermissions();
  }
}