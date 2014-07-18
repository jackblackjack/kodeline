<?php
/**
 * Форма добавления расширенного параметра модели в таблицу.
 */
class ParameterableParamStringForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine default_value field.
    $this->setWidget('default_value', new sfWidgetFormTextarea());
    $this->setValidator('default_value', new sfValidatorString(array('required' => false)));

    // Redefine default_value field.
    $this->setWidget('length', new sfWidgetFormInputText());
    $this->setValidator('length', new sfValidatorInteger(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'default_value' => 'Значение по-умолчанию',
      'length'        => 'Ограничение по длинне',
      'name'          => 'Тех. название параметра'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'default_value' => 'Значение, присваеваемое параметру по-умолчанию',
      'length'        => 'Не указывайте значение параметра, если ограничения по длинне текста нет',
      'name'          => 'Название параметра, используемое разработчиками'
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}