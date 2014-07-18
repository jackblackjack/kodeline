<?php
/**
 * Форма добавления значения 
 * расширенного (decimal) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueDecimalForm extends BaseParameterableParamValueDecimalForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Configure.
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new jWidgetFormInputJQueryValidateNumber(array(
      'is_decimal' => ($this->getOption('type') == PluginjParameterableSchema::DECIMAL_TYPE_NAME)
    )));

    // Redefine validator.
    $this->setValidator('value', new sfValidatorNumber(array('required' => (bool) $this->getOption('is_require', false))));
  }
}