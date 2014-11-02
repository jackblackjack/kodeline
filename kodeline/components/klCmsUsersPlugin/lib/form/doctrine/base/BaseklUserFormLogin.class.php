<?php
class BaseklUserFormLogin extends BaseForm
{
  /**
   * @see sfForm
   */
  public function setup()
  {
    // Sets widgets.
    $this->setWidgets(array(
      'username' => new sfWidgetFormInputText(),
      'password' => new sfWidgetFormInputPassword(array('type' => 'password')),
      'remember' => new sfWidgetFormInputCheckbox(),
    ));

    // Set widgets labels.
    $this->getWidgetSchema()->setLabels(array(
      'username'    => (sfConfig::get('app_klCmsUsersPlugin_username_is_email', true) ? 'E-mail' : 'Логин'),
      'password'    => 'Пароль',
      'remember'    => 'Запомнить меня на этом компьюторе'
    ));

    // Sets validators.
    $this->setValidators(array(
      'username' => (sfConfig::get('app_klCmsUsersPlugin_username_is_email', true) ? new sfValidatorEmail() : new sfValidatorString()),
      'password' => new sfValidatorString(),
      'remember' => new sfValidatorBoolean(),
    ));
    
    // Define validator classname.
    $validatorClass = sfConfig::get('app_klCmsUsersPlugin_validator_class', 'klValidatorUser');
    $this->validatorSchema->setPostValidator(new $validatorClass());
    
    // Set form names format by default.
    $this->widgetSchema->setNameFormat('login[%s]');
  }
}