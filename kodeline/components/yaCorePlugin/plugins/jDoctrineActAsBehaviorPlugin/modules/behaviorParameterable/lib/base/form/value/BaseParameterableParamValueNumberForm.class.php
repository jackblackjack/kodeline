<?php
/**
 * Базовая форма добавления значения для поля типа "Число".
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueNumberForm extends BaseParameterableParamValueForm
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