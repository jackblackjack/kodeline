<?php
/**
 * Базовая форма добавления значения 
 * расширенного (string) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueStringForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new sfWidgetFormTextarea());
    $this->setValidator('value', new sfValidatorString(array('required' => false)));
  }
}