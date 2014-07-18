<?php
class klValidatorUser extends sfValidatorBase
{
  /**
   * @see sfValidatorBase
   */
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('username_field', 'username');
    $this->addOption('password_field', 'password');
    $this->addOption('throw_global_error', false);

    $this->setMessage('invalid', 'The username and/or password is invalid.');
  }

  /**
   * @see doClean
   */
  protected function doClean($values)
  {
    // Define username value.
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';
    
    // Don't allow to sign in with an empty username
    if (0 == strlen(trim($username)))
    {
      throw new sfValidatorError($this, 'invalid');
    }

    // Define password value.
    $password = isset($values[$this->getOption('password_field')]) ? $values[$this->getOption('password_field')] : '';

    // Define other callable for fetch user.
    if ($callable = sfConfig::get('app_app_klCmsUsersPlugin_retrieve_user_callable'))
    {
      $user = call_user_func_array($callable, array($username));
    }
    else {
      $user = $this->getTable()->retrieveByUsername($username);
    }

    // user exists and password is ok?
    if ($user && $user->getIsActive() && $user->checkPassword($password))
    {
      return array_merge($values, array('user' => $user));
    }
    
    throw new sfValidatorErrorSchema($this, array($this->getOption('username_field') => new sfValidatorError($this, 'invalid')));
  }

  /**
   * {@inheritDoc}
   */
  protected function getTable()
  {
    return Doctrine::getTable('klUser');
  }
}
