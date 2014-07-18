<?php
abstract class PluginklUserUserGroup extends BaseklUserUserGroup
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