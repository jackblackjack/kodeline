<?php
abstract class PluginklPermissionForm extends BaseklPermissionForm
{
  /**
   * @see sfForm
   */
  public function setupInheritance()
  {
    parent::setupInheritance();

    unset(
      $this['created_by'],
      $this['updated_by'],
      $this['created_at'],
      $this['updated_at']
    );

    $this->widgetSchema['groups_list']->setLabel('Groups');
    $this->widgetSchema['users_list']->setLabel('Users');
  }
}
