<?php
/**
 * Базовая форма добавления значения 
 * расширенного (timestamp) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueTimestampForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();
    
    // Redefine title field.
    $this->setWidget('value', new sfWidgetFormDateTime());
    $this->setValidator('value', new sfValidatorDateTime(array('required' => false, 'with_time' => true)));
  }
}