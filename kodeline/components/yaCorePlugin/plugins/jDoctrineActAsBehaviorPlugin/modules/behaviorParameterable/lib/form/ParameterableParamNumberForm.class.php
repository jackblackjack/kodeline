<?php
/**
 * Base form for insert field with type "Number" (Число).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
class ParameterableParamNumberForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define is_decimal field.
    $this->setWidget('is_decimal', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_decimal', new sfValidatorBoolean());
    $this->setDefault('is_decimal', false);

    // Redefine default_value field.
    $this->setWidget('default_value', new sfWidgetFormInputText());
    $this->setValidator('default_value', new sfValidatorNumber(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'is_decimal'    => 'С плавающей точкой',
      'default_value' => 'Значение по-умолчанию',
      'name'          => 'Имя поля'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'is_decimal'    => 'Используйте числа с плавающей точкой при необходимости указать цену, длинну или другой величины, не имеющей целочисленного исчисления',
      'default_value' => 'Значение, которое будет назначено при создании нового объекта',
      'name'          => 'Имя поля для разработчика',
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}