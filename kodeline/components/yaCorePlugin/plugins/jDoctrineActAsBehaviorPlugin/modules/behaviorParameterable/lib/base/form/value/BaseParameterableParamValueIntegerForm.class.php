<?php
/**
 * Базовая форма добавления значения 
 * расширенного (integer) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueIntegerForm extends PluginjParameterableIntegerValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();

    // Fetch options for form.
    $object = $this->getOption('object', null);
    $parameter = $this->getOption('parameter', null);
    $iComponent = $this->getOption('component_id', null);

    // Define object exists flag.
    $bObjectExists = (is_object($object) && !$object->isNew());

    // Set default values.
    if (null !== $parameter) $this->setDefault('parameter_id', $parameter->getId());
    if (null !== $iComponent) $this->setDefault('component_id', $iComponent);
    if ($bObjectExists) $this->setDefault('object_id', $object->getId());

    // Redefine object_id field and validator
    $this->setWidget('object_id', new sfWidgetFormInputHidden());
    $this->setValidator('object_id', new sfValidatorNumber(array('required' => $bObjectExists)));

    // Redefine component_id field.
    $this->setWidget('component_id', new sfWidgetFormInputHidden()); 

    // Redefine parameter_id field.
    $this->setWidget('parameter_id', new sfWidgetFormInputHidden());

    // Fetch object of parameter.
    if ($bObjectExists)
    { 
      // Fetch parameter data.
      $formObject = $this->getObject()->getTable()->createQuery()
                            ->where('component_id = ?', $iComponent)
                            ->andWhere('parameter_id = ?', $parameter['id'])
                            ->andWhere('object_id = ?', $object['id'])
                            ->fetchOne();

      // Set object.
      if ($formObject)
      {
        $this->object = $formObject;
        $this->isNew = !$this->object->exists();

        // Redefine id validator.
        $this->setValidator('id', new sfValidatorChoice(array('choices' => array($this->getObject()->get('id')), 'empty_value' => $this->getObject()->get('id'), 'required' => false)));
      }
    }
  }
}