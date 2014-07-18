<?php
/**
 * Form for definition parameter for attaching objects.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamDocumentForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define extensions field.
    $this->setWidget('extensions', new jWidgetFormInputStrings());
    $this->setValidator('extensions', new jValidatorStrings(array('required' => false)));
    $this->setDefault('extensions', array('*.doc', '*.docx', '*.xls', '*.xlsx', '*.pdf'));

    // Define is_many field.
    $this->setWidget('is_many', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_many', new sfValidatorBoolean());
    $this->setDefault('is_many', false);

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'attach_type' => 'Тип прикрепления',
      'is_many'     => 'Множественное добавление',
      'name'        => 'Тех. название параметра'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'name'          => 'Название параметра, используемое разработчиками',
      'is_many'       => 'Параметр, отвечающий за добавление списка файлов',
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}