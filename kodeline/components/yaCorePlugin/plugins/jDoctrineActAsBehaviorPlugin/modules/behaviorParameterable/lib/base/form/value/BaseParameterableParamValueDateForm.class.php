<?php
/**
 * Base abstract class for 
 * form with field with "Date" parameter value.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueDateForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call the parent method.
    parent::configure();
    
    // Redefine field "title".
    $this->setWidget('value', new jWidgetFormInputJQueryDatepicker());
    $this->setValidator('value', new sfValidatorDate(array('required' => false)));
  }
}