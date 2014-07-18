<?php
/**
 * Форма добавления значения 
 * расширенного (time) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueTimeForm extends BaseParameterableParamValueTimeForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new jWidgetFormInputJQueryTimepicker(array(
      'show_date' => ($this->getOption('type') == PluginjParameterableSchema::TIMESTAMP_TYPE_NAME)
    )));

    // Set validator.
    $this->setValidator('value', new sfValidatorTime(array('required' => false, 'time_output' => 'H:i')));
  }
}
