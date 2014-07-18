<?php
/**
 * Base form for insert field with type "Checkbox" (Флаг)
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
class ParameterableParamCheckboxForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define is_enabled option: if is set - checkbox enabled by default.
    $this->setWidget('is_enabled', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_enabled', new sfValidatorBoolean());
    $this->setDefault('is_enabled', false);

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'is_enabled'  => 'По-умолчанию:',
      'name'        => 'Метка поля'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'is_enabled'  => 'Активировать флаг по-умолчанию',
      'name'        => 'Метка, используемая при разработке',
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}