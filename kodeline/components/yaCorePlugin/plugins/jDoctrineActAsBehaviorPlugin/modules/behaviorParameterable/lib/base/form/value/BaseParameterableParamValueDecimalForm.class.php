<?php
/**
 * Базовая форма добавления значения 
 * расширенного (decimal) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueDecimalForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new sfWidgetFormInputText());
    $this->setValidator('value', new sfValidatorNumber(array('required' => false)));
  }
}