<?php
/**
 * Базовая форма добавления значения 
 * расширенного параметра для элемента типа.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call the parent method.
    parent::configure();

    // Define field "component".
    $this->setWidget('component', new sfWidgetFormInputHidden());
    $this->setValidator('component', new sfValidatorString(array('required' => true)));

    // Define field "parameter_id".
    $this->setWidget('parameter_id', new sfWidgetFormInputHidden());
    $this->setValidator('parameter_id', new sfValidatorInteger(array('required' => true)));

    $object = $this->getOption('object', null);
    if ($object)
    {
      $this->setWidget('object_id', new sfWidgetFormInputHidden());
      $this->setValidator('object_id', new sfValidatorInteger(array('required' => isset($object['id']))));

      if (isset($object['id'])) $this->setDefault('object_id', $object->getId());
    }
  }

  /**
   * {@inheritDoc}
   */
  public function addCSRFProtection($secret = null)
  {
    return $this;
  }
}
