<?php
/**
 * Форма для добавления комментария.
 */
class BaseCommentableForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    // Set widgets.
    $this->setWidgets(array(
      'model'         => new sfWidgetFormInputHidden(),
      'resource'      => new sfWidgetFormInputHidden(),
      'parent_id'     => new sfWidgetFormInputHidden(),
      'author_id'     => new sfWidgetFormInputHidden(),
      'author_name'   => new sfWidgetFormInput(),
      'author_email'  => new sfWidgetFormInput(),
      'title'         => new sfWidgetFormInput(),
      'body'          => new sfWidgetFormTextarea()
    ));

    // Set validators.
    $this->setValidators(array(
      'model'         => new bcValidatorCommentableModelName(array('required' => true)),
      'resource'      => new sfValidatorInteger(array('required' => true, 'min' => 1)),
      'parent_id'     => new bcValidatorCommentableExists(array('required' => false)),
      'author_id'     => new bcValidatorCommentableProfileExists(array('required' => true)),
      'author_name'   => new sfValidatorString(array('required' => true)),
      'author_email'  => new sfValidatorEmail(array('required' => true)),
      'title'         => new sfValidatorString(array('required' => false)),
      'body'          => new sfValidatorString(array('required' => true))
    ));

    // Setup pre validator.
    $this->getValidatorSchema()->setPreValidator(
       new sfValidatorCallback(array('callback' => array($this, 'preValidator')))
    );

    // Setup post validator.
    $this->getValidatorSchema()->setPostValidator(
       new sfValidatorCallback(array('callback' => array($this, 'postValidator')))
    );
  }

  /**
   * Превалидатор формы.
   * 
   * @param sfValidator $validator
   * @param array $values
   */
  public function preValidator($validator, $values)
  {
    // Если пользователь авторизован и параметр author_id установлен - 
    //проверка имени и электронного адреса пользователя не требуется.
    if (! sfContext::getInstance()->getUser()->isGuest() && !empty($values['author_id']))
    {
      $this->validatorSchema['author_name'] = new sfValidatorPass(array('required' => false));
      $this->validatorSchema['author_email'] = new sfValidatorPass(array('required' => false));
    }
    else {
     $this->validatorSchema['author_id'] = new sfValidatorPass(array('required' => false)); 
    }

    return $values;
  }

  /**
   * Поствалидатор формы.
   * 
   * @param sfValidator $validator
   * @param array $values
   */
  public function postValidator($validator, $values)
  {
    // Если пользователь авторизован и параметр author_id установлен - выборка профиля пользователя.
    if (! sfContext::getInstance()->getUser()->isGuest() && !empty($values['author_id']))
    {
      $profile = Doctrine_Core::getTable('jProfileExtension')->createQuery()->where('id = ?', $values['author_id'])->fetchOne();
      if ($profile) return array_merge($values, array('author' => $profile));
    }
    else {
     $this->validatorSchema['author_id'] = new sfValidatorPass(array('required' => false)); 
    }

    return $values;
  }
}