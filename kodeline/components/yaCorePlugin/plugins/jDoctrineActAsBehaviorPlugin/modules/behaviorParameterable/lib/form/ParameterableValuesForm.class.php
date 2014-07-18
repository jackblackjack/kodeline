<?php
/**
 * Форма добавления описания 
 * расширенного параметра для модели.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableValuesForm extends BaseParameterableForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent form.
    parent::configure();

    // Definition list for uses fields of the form.
    $this->useFields(array('id', 'component_id', 'belong', 'title', 'type'));

    // Redefine component_id field.
    $this->setWidget('component_id', new sfWidgetFormInputHidden());

    // Redefine title field.
    $this->setWidget('title', new sfWidgetFormInputText());
    $this->setValidator('title', new sfValidatorString(array('required' => true)));

    // Redefine hint field.
    $this->setWidget('hint', new sfWidgetFormTextarea());
    $this->setValidator('hint', new sfValidatorString(array('required' => false)));

    // Redefine type field.
    $this->setWidget('type', new sfWidgetFormSelect(array('choices' => $this->getSupportedTypes())));
    $this->setValidator('type', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getSupportedTypes()))));

    // Redefine is_hidden field.
    $this->setWidget('is_require', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_require', new sfValidatorBoolean());
    $this->setDefault('is_require', false);

    // Redefine belong field.
    $this->setWidget('belong', new sfWidgetFormInputHidden());
    $this->setValidator('belong', new sfValidatorInteger(array('required' => false)));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'title'       => 'Название поля',
      'hint'        => 'Подсказка при заполнении',
      'type'        => 'Тип поля',
      'is_require'  => 'Обязательное'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'title'     => 'Название параметра для вывода',
      'hint'      => 'Подсказка к параметру, которая выдается при заполнении формы',
      'type'      => 'Выберите один из представленных типов параметров',
      'is_require' => 'Обязательное для заполнения'
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}