<?php
/**
 * Форма заказа работ по сайту.
 *
 * @package    frontend
 * @subpackage form
 * @author     Alexey Chugarev <chugarev@gmail.com>
 * @version    $Id$
 */
class leadForm extends yaForm
{
  /**
   * @see sfForm
   */
  public function configure()
  {
    // Определение поля 
    //для выбора целей заказа.
    $this->setWidget('status', new sfWidgetFormSelect(array('choices' => $this->getSupportedTypes())));
    $this->setValidator('status', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getSupportedTypes()))));

    // Set default widgets.
    $this->setWidgets(array(
      'changes'        => new sfWidgetFormInput(),
      'phone'       => new sfWidgetFormInput(),
      'email'       => new sfWidgetFormInput(),
      'text'        => new sfWidgetFormTextarea(array(), array('cols' => '20', 'rows' => '3'))
    ));

    // Set default validators.
    $this->setValidators(array(
      'name'    => new sfValidatorString(array('max_length' => 255, 'required' => false)),
      'phone'       => new sfValidatorString(array('max_length' => 255, 'required' => true), array('required' => 'Укажите телефон')),
      'email'       => new sfValidatorEmail(array('required' => true), array('required' => 'Укажите email', 'invalid' => 'Укажите верный email')),
      'text'        => new sfValidatorString(array('max_length' => 1024, 'required' => true), array(
        'required' => 'Текст обращения не заполнен',
        'max_length' => 'Текст сообщения слишком большой'
      ))
    ));

    $this->validatorSchema->setOption('allow_extra_fields', true);
    $this->validatorSchema->setOption('filter_extra_fields', false);
    $this->validatorSchema->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidator'))));

    // Setup field "recaptcha_response_field"
    $this->setWidget('recaptcha_response_field', new sfWidgetFormInput());
    $this->setDefault('recaptcha_response_field', 'manual_challenge');
    $this->setValidator('recaptcha_response_field', new sfValidatorString(array('required' => true, 'min_length' => 6)));

    // Переопределение сообщений валидатора для виджета password.
    $this->getValidator('recaptcha_response_field')->setMessages(array(
      'required'    => "Пожалуйста укажите символы с картинки",
      'invalid'     => "Пожалуйста укажите правильно символы",
      'min_length'  => 'Минимальное количество символов - %min_length%'
    ));

    // Set default labels.
    $this->widgetSchema->setLabels(array(
      'username'    => 'Ваше ФИО',
      'phone'       => 'Ваш контактный телефон',
      'email'       => 'E-mail',
      'text'        => 'Текст обращения',
      'recaptcha_response_field'  => 'Введите символы с картинки'
    ));
  }

  /**
   * Retrieve list of supported types for extended params.
   * @return array
   */
  protected function getStatusVariant()
  {
    return array(
      'image'     => 'Изображение',
      'document'  => 'Документ',
      'other'     => 'Файл'
    );
  }



  /**
   * {@inheritDoc}
   */
  public function postValidator($validator, $values)
  {
    $errors = array();

    if ($values['recaptcha_response_field'])
    {
      if (! empty($values['recaptcha_challenge_field']))
      {
        sfContext::getInstance()->getRequest()->setParameter('recaptcha_challenge_field', $values['recaptcha_challenge_field']);        
      }

      sfContext::getInstance()->getRequest()->setParameter('recaptcha_response_field', $values['recaptcha_response_field']);

      try {
        $recaptha = new reCaptcha();
        $response = $recaptha->checkAnswer(sfContext::getInstance()->getRequest());

        if (! $response->is_valid)
        {
          //throw new sfException($validator->getMessage('invalid'));
          throw new sfException('Пожалуйста укажите правильно символы');
        }
      }
      // Обработка исключений.
      catch(Exception $exception)
      {
        $errors['recaptcha_response_field'] = new sfValidatorError($validator, $exception->getMessage());
      }
    }

    if (! empty($errors))
    {
      throw new sfValidatorErrorSchema($validator, $errors);
    }

    return $values;
  }
}
