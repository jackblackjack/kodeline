<?php
/**
 * Форма добавления расширенного параметра модели в таблицу.
 */
class ParameterableParamTimeForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine min field.
    $this->setWidget('min', new sfWidgetFormTime());
    $this->setValidator('min', new sfValidatorTime(array('required' => false)));

    // Redefine max field.
    $this->setWidget('max', new sfWidgetFormTime());
    $this->setValidator('max', new sfValidatorTime(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'min'   => 'Минимальное время',
      'max'   => 'Максимальное время',
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