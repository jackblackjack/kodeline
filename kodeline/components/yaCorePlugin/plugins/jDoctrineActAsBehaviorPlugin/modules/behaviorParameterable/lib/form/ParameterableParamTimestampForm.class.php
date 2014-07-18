<?php
/**
 * Форма добавления расширенного параметра модели в таблицу.
 */
class ParameterableParamTimestampForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine min field.
    $this->setWidget('min', new sfWidgetFormDateTime());
    $this->setValidator('min', new sfValidatorDateTime(array('required' => false)));

    // Redefine max field.
    $this->setWidget('max', new sfWidgetFormDateTime());
    $this->setValidator('max', new sfValidatorDateTime(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'min'   => 'Минимальное значение',
      'max'   => 'Максимальное значение',
      'name'  => 'Тех. название параметра'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'name'          => 'Название параметра, используемое разработчиками',
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}