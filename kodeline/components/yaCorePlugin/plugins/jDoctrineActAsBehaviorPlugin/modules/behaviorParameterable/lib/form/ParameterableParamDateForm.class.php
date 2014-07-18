<?php
/**
 * Форма добавления расширенного параметра модели в таблицу.
 */
class ParameterableParamDateForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine min field.
    $this->setWidget('min', new jWidgetFormInputJQueryDatepicker());
    $this->setValidator('min', new sfValidatorDate(array('required' => false)));

    // Redefine max field.
    $this->setWidget('max', new jWidgetFormInputJQueryDatepicker());
    $this->setValidator('max', new sfValidatorDate(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'min'   => 'Минимальная дата',
      'max'   => 'Максимальная дата'
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}