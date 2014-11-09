<?php
/**
 * 
 * @package    
 * @subpackage 
 * @author     
 * @author     
 * @version    
 */
class klValidatorDoctrineIfExists extends sfValidatorSchema
{
  /**
   * {@inheritDoc}
   */
  public function __construct($options = array(), $messages = array()) {
    parent::__construct(null, $options, $messages);
  }

  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * model:              The model class (required)
   *  * column:             The unique column name in Doctrine field name format (required)
   *                        If the uniquess is for several columns, you can pass an array of field names
   *  * primary_key:        The primary key column name in Doctrine field name format (optional, will be introspected if not provided)
   *                        You can also pass an array if the table has several primary keys
   *  * connection:         The Doctrine connection to use (null by default)
   *  * throw_global_error: Whether to throw a global error (false by default) or an error tied to the first field related to the column option array
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('model');
    $this->addRequiredOption('column');
    $this->addOption('primary_key', null);
    $this->addOption('connection', null);
    $this->addOption('throw_global_error', false);

    $this->setMessage('invalid', 'An object with the same "%column%" already exist.');
  }

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


