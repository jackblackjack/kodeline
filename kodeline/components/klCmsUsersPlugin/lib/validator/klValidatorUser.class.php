<?php
class klValidatorUser extends sfValidatorBase
{
  /**
   * @see sfValidatorBase
   */
  public function configure($options = array(), $messages = array())
  {
    // Add options for form.
    $this->addOption('username_field', 'username');
    $this->addOption('password_field', 'password');
    $this->addOption('throw_global_error', false);

    // Add error messages text.
    $this->addMessage('invalid_username', 'The username and/or password is invalid.');
    $this->addMessage('invalid_password', 'The username and/or password is invalid.');
    $this->addMessage('user_not_found', 'The username and/or password is invalid.');
  }

  /**
   * @see doClean
   */
  protected function doClean($values)
  {
    // Define username value.
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';
   
    // Don't allow to sign in with an empty username
    if (0 == strlen(trim($username))) {
      throw new sfValidatorError($this, 'invalid_username', array('value' => $username));
    }

    // Define password value.
    $password = isset($values[$this->getOption('password_field')]) ? $values[$this->getOption('password_field')] : '';

    // Define other callable for fetch user.
    if ($callable = sfConfig::get('app_app_klCmsUsersPlugin_retrieve_user_callable')) {
      $user = call_user_func_array($callable, array($username));
    }
    else {
      $user = $this->getTable()->retrieveByUsername($username);
    }

    // Throw error message if user not found.
    if (! $user || ! $user->getIsActive()) {
      throw new sfValidatorError($this, 'user_not_found', array('value' => $username)); 
    }

    // User exists - check the password.
    if ($user && $user->getIsActive() && ! $user->checkPassword($password)) {
      throw new sfValidatorError($this, 'invalid_password', array('value' => $password));
    }

    // Return by default.
    return array_merge($values, array('user' => $user));
  }

  /**
   * {@inheritDoc}
   */
  protected function getTable()
  {
    return Doctrine::getTable('klUser');
  }
}
