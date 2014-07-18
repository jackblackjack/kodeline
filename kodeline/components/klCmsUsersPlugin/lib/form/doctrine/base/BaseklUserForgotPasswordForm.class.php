<?php
class BaseklUserForgotPasswordForm extends BaseForm
{
  /**
   * {@inheritDoc}
   */
  public function setup()
  {
    $this->widgetSchema['username'] = new sfWidgetFormInput();
    $this->validatorSchema['username'] = new sfValidatorString();

    $this->widgetSchema->setNameFormat('forgot_password[%s]');
  }

  /**
   * {@inheritDoc}
   */
  public function isValid()
  {
    $valid = parent::isValid();

    if (! $valid)
    {
      return false;
    }

    $values = $this->getValues();

    // Fetch user.
    $this->user = Doctrine_Core::getTable('klUser')
                    ->createQuery('u')->where('u.username = ?', $values['username'])
                    ->fetchOne();

    return (false != $this->user);
  }
}