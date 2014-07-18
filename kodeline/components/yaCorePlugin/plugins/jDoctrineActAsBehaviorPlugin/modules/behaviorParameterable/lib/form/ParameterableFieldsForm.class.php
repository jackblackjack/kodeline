<?php
/**
 * Form for fields parameters.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableFieldsForm extends sfFormObject
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent form.
    parent::configure();

    if (null !== $this->getOption('component', null))
    {
      // Fetch component's record.
      $object = $this->getOption('object', Doctrine_Core::getTable($this->getOption('component'))->getRecordInstance());

      // Fetch schema of the extended parameters.
      $arExtendedParametersSchema = $object->fetchExtendedParameters();

      // Build embeded forms for each parameter.
      foreach($arExtendedParametersSchema as $parameter)
      {
        // Define form class name for parameter.
        $parameterFormClassName = 'ParameterableParamValue' . sfInflector::camelize($parameter['type']) . 'Form';

        // Create form instance.
        $parameterFormClassName = new $parameterFormClassName(
          null,
          array(
            'object'        => $object,
            'parameter'     => $parameter,
            'component_id'  => BehaviorTemplateToolkit::getComponentIdByName($this->getOption('component')),
            //'value'         => $parameter->getCurrentValue()
          )
        );

        // Embed current form by parameterable form value.
        $this->embedForm($parameter['name'], $parameterFormClassName);
        //$this->mergeForm($parameterFormClassName);

        //$parameterFormClassName->getWidgetSchema()->addFormFormatter('embeddedForm', new sfWidgetFormSchemaFormatterBehaviorParameterableEmbedForm($parameterFormClassName->getWidgetSchema()));
        //$parameterFormClassName->getWidgetSchema()->setFormFormatterName('embeddedForm');
      }
    }

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }

  /**
   * {@inheritDoc}
   */
  public function getObject()
  {
    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function save($con = null)
  {
    return null;
  }

  /**
   * {@inheritDoc}
   */
  public function getModelName()
  {
    return null;
  }

  /**
   * {@inheritDoc}
   */
  public function getTable()
  {
    return $this;
  }

  /**
   * @see Doctrine_Table
   */
  public function hasTemplate($templateName)
  {
    return false;
  }

  /**
   * {@inheritDoc}
   */
  public function getConnection()
  {
    return null;
  }

  /**
   * {@inheritDoc}
   */
  protected function doUpdateObject($values)
  {
    // Define parameter's names and length.
    $arParameters = array_keys($values);
    $szParameters = count($arParameters);

    // Values processing.
    for($i = 0; $i < $szParameters; $i++)
    {
      $this->embeddedForms[$arParameters[$i]]->setDefaults($values[$arParameters[$i]]);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function processValues($values)
  {
    // Define parameter's names and length.
    $arParameters = array_keys($values);
    $szParameters = count($arParameters);

    // Define results values.
    $arResultValues = array();

    // Values processing.
    for($i = 0; $i < $szParameters; $i++)
    {
      if (! isset($this->embeddedForms[$arParameters[$i]]))
      {
        continue;
      }

      $arResultValues[$arParameters[$i]] = $values[$arParameters[$i]];
    }

    return $arResultValues;
  }

  /**
   * {@inheritDoc}
   */
  public function updateAccessory(sfDoctrineRecord $record)
  {
    foreach($this->embeddedForms as $form)
    {
      $form->getObject()->set('object_id', $record->getId());
    }
  }
}
