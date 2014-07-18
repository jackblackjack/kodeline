<?php
/**
 * Форма добавления значения 
 * расширенного (date) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueDateForm extends BaseParameterableParamValueDateForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new jWidgetFormInputJQueryDatepicker());
    $this->setValidator('value', new sfValidatorDate(array('required' => false)));

    // Redefine title field.
    $this->setWidget('value1', new jWidgetFormInputJQueryDatepicker());
    $this->setValidator('value1', new sfValidatorDate(array('required' => false)));
  }
}