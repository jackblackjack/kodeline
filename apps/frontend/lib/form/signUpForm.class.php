<?php
/**
 * signinForm форма регистрации.
 *
 * @package    frontend
 * @subpackage form
 * @author     Alexey Chugarev <chugarev@gmail.com>
 * @version    $Id$
 */
class signUpForm extends sfGuardRegisterForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    // Переопределение валидатора для виджета username.
    $this->setValidator('username', new sfValidatorAnd(array(
        new sfValidatorString(
          array('required' => true, 'trim' => true, 'min_length'  => 1),
          array(
            'required'    => "Пожалуйста укажите Ваш email",
            'invalid'     => "Пожалуйста укажите правильно Ваш email",
            'min_length'  => 'Минимальное количество символов в логине - %min_length%')
        )
      ),
      array('halt_on_error' => false),
      array(
        'required' => 'Пожалуйста укажите Ваш email',
        'invalid'  => 'Пожалуйста укажите правильно Ваш email'
        )
      )
    );

    // Переопределение сообщений post-валидатора формы.
    $this->getValidatorSchema()->getPostValidator()->setMessages(array(
      'required'  => "Пожалуйста укажите Ваш email",
      'invalid'   => 'Пожалуйста укажите правильно Ваш email'
    ));

    // Переопределение сообщений валидатора для виджета password.
    $this->getValidator('password')->setMessages(array(
      'required'  => "Пожалуйста укажите Ваш пароль",
      'invalid'   => "Пожалуйста укажите правильно Ваш пароль",
    ));
  }
}
