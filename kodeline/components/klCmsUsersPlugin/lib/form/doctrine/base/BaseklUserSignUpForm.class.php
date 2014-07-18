<?php
class BaseklUserSignUpForm extends BaseklUserAdminForm
{
  /**
   * {@inheritDoc}
   */
  public function setup()
  {
    // Call parent method.
    parent::setup();

    unset(
      $this['is_active'],
      $this['is_super_admin'],
      $this['groups_list'],
      $this['permissions_list']
    );

    $this->validatorSchema['password']->setOption('required', true);
  }
}