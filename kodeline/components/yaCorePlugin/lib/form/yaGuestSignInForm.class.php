<?php

/**
 * Guest User form
 *
 * @package    yaCorePlugin
 * @subpackage lib.form
 * @author     pinhead
 * @version    SVN: $Id: yaGuestSignInForm.class.php 2382 2010-10-04 12:28:19Z pinhead $
 */
class yaGuestSignInForm extends BaseForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'email'       => new sfWidgetFormInputText(),
      'first_name'  => new sfWidgetFormInputText(),
      'last_name'   => new sfWidgetFormInputText(),
      'middle_name' => new sfWidgetFormInputText(),
      'username'    => new sfWidgetFormInputHidden(array(), array('value' => sfConfig::get('app_ya_core_plugin_guest_username', 'guest')))
    ));

    $this->setValidators(array(
      'email'       => new sfValidatorEmail(array('max_length' => 128, 'trim' => true, 'required' => true)),
      'first_name'  => new sfValidatorString(array('max_length' => 50, 'trim' => true, 'required' => true)),
      'last_name'   => new sfValidatorString(array('max_length' => 50, 'trim' => true, 'required' => true)),
      'middle_name' => new sfValidatorString(array('max_length' => 50, 'trim' => true, 'required' => false)),
      'username'    => new sfValidatorString(array('max_length' => 128, 'trim' => true, 'required' => true))
    ));

    $this->validatorSchema->setPostValidator(new yaValidatorGuestUser());

    $this->setName('guest');
  }

}