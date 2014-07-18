<?php
class BaseklUserAdminForm extends BaseklUserForm
{
  /**
   * {@inheritDoc}
   */
  public function setup()
  {
    parent::setup();

    unset(
      $this['last_login'],
//      $this['created_by'],
//      $this['updated_by'],
      $this['created_at'],
      $this['updated_at'],
      $this['salt'],
      $this['algorithm']
    );
  }
}