<?php
/**
 * signinForm форма авторизации.
 *
 * @package    frontend
 * @subpackage form
 * @author     Alexey Chugarev <chugarev@gmail.com>
 * @version    $Id$
 */
class signInForm extends sfGuardFormSignin
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    // Переопределение валидатора для виджета username.
    $this->setValidator('username', new sfValidatorAnd(array(
        new sfValidatorString(
          array('required' => true, 'trim' => true, 'min_length'  => 6),
          array(
            'required'    => "Пожалуйста укажите Ваш логин",
            'invalid'     => "Пожалуйста укажите правильно Ваш логин1",
            'min_length'  => 'Минимальное количество символов в логине - %min_length%')
        )
      ),
      array('halt_on_error' => true),
      array('required' => ' ', 'invalid'  => ' ')
      )
    );

    // Переопределение сообщений post-валидатора формы.
    $this->getValidatorSchema()->getPostValidator()->setMessages(array(
      'required'  => "Пожалуйста укажите Ваш логин",
      'invalid'   => 'Пожалуйста укажите правильно Ваш логин'
    ));

    // Переопределение сообщений валидатора для виджета password.
    $this->getValidator('password')->setMessages(array(
      'required'  => "Пожалуйста укажите Ваш пароль",
      'invalid'   => "Пожалуйста укажите правильно Ваш пароль",
    ));
  }
}
