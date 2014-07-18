<?php
/**
 * Базовая Форма добавления значения для
 * поля типа "флаг" (checkbox).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueCheckboxForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new sfWidgetFormInputCheckbox());
    $this->setValidator('value', new sfValidatorBoolean());
  }
}