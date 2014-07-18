<?php
class klUserSignUpForm extends BaseklUserSignUpForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    parent::configure();
    
    // Redefined post validator.
    $this->getValidatorSchema()->setPostValidator(new sfValidatorAnd(array(
      new sfValidatorDoctrineUnique(array('model' => 'klUser', 'column' => array('username')), array('invalid' => 'Username already in use.'))
    )));
  }
}