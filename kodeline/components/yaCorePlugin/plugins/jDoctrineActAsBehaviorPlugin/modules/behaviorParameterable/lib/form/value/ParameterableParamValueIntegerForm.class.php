<?php
/**
 * Форма добавления значения 
 * расширенного (integer) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueIntegerForm extends BaseParameterableParamValueIntegerForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent configure.
    parent::configure();

    // Set list of used fields.
    $this->useFields(array('id', 'component_id', 'object_id', 'parameter_id', 'value'));
    
    // Redefine value field widget.
    $this->setWidget('value', new jWidgetFormInputJQueryValidateNumber(array(
      'is_decimal' => ($this->getOption('type') == PluginjParameterableSchema::DECIMAL_TYPE_NAME)
    )));

    // Set label for widget.
    $parameter = $this->getOption('parameter', null);
    if ($parameter)
    {
      $this->getWidgetSchema()->setLabel('value', $parameter['Translation'][yaContext::getInstance()->getUser()->getCulture()]['title']);
    }
  }
}