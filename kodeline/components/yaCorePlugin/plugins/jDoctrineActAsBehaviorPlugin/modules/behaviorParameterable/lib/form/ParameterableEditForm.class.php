<?php
/**
 * Форма редактирования описания параметра.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableEditForm extends BaseParameterableForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent form.
    parent::configure();

    // Redefine component_id field.
    $this->setWidget('component_id', new sfWidgetFormInputHidden());

    // Redefine title field.
    //$this->setWidget('title', new sfWidgetFormInputText());
    //$this->setValidator('title', new sfValidatorString(array('required' => true)));

    // Redefine hint field.
    //$this->setWidget('hint', new sfWidgetFormTextarea());
    //$this->setValidator('hint', new sfValidatorString(array('required' => false)));

    // Redefine type field.
    $this->setWidget('type', new sfWidgetFormSelect(array('choices' => $this->getSupportedTypes())));
    $this->setValidator('type', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getSupportedTypes()))));

    // Redefine is_hidden field.
    $this->setWidget('is_require', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_require', new sfValidatorBoolean());

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());

    // Redefine belong field.
    $this->setWidget('belong', new sfWidgetFormInputHidden());
    $this->setValidator('belong', new sfValidatorInteger(array('required' => true)));

    // Set widgets labels.
    $this->getWidgetSchema()->setLabels(array(
      'type'            => 'Тип поля',
      'length'          => 'Ограничение по длинне',
      'is_require'      => 'Обязательное',
      'default_value'   => 'Значение по-умолчанию'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'type'        => 'Выберите тип поля',
      'is_require'  => 'Поле будет обязательно для заполнения'
    ));

    // If form has existed object.
    if (! $this->isNew())
    {
      // Define classname for field definition.
      $parameterFormClassName = 'ParameterableParam' . sfInflector::camelize($this->getObject()->getType()) . 'Form';

      // Embed current form.
      $this->embedForm('options', new $parameterFormClassName(array('parameter_id' => $this->getObject()->getId())));
    }

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}