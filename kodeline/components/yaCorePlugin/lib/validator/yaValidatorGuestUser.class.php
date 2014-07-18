<?php

/**
 * Validates guest user.
 *
 * @package    yaCorePlugin
 * @subpackage lib.validator
 * @author     Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorGuestUser.class.php 2382 2010-10-04 12:28:19Z pinhead $
 */
class yaValidatorGuestUser extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('username_field', 'username');
    $this->addOption('throw_global_error', false);

    $this->setMessage('invalid', 'Invalid guest username.');
  }

  protected function doClean($values)
  {
    $username = isset($values[$this->getOption('username_field')]) ? $values[$this->getOption('username_field')] : '';

    $guestUsername = sfConfig::get('app_ya_core_plugin_guest_username', null);
    $guestGroup = sfConfig::get('app_ya_core_plugin_guest_group', null);
    $allowEmail = sfConfig::get('app_sf_guard_plugin_allow_login_with_email', true);
    $method = $allowEmail ? 'retrieveByUsernameOrEmailAddress' : 'retrieveByUsername';

    // don't allow to sign in with an empty username
    if ($username && $username == $guestUsername)
    {
      if ($callable = sfConfig::get('app_sf_guard_plugin_retrieve_by_username_callable'))
      {
        $user = call_user_func_array($callable, array($username));
      }
      else
      {
        $user = $this->getTable()->retrieveByUsername($username);
      }

      // user exists?
      if ($user && $user->getIsActive() && $user->hasGroup($guestGroup))
      {
        return array_merge($values, array('user' => $user));
      }
    }

    if ($this->getOption('throw_global_error'))
    {
      throw new sfValidatorError($this, 'invalid');
    }

    throw new sfValidatorErrorSchema($this, array($this->getOption('username_field') => new sfValidatorError($this, 'invalid')));
  }

  protected function getTable()
  {
    return Doctrine::getTable('sfGuardUser');
  }
}
