<?php
/**
 * Форма добавления поля объекта.
 * Тип объекта: переключатель (radio).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamRadioForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Define extensions field.
    $this->setWidget('items', new jWidgetFormInputStrings());
    $this->setValidator('items', new jValidatorStrings(array('required' => false)));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'items' => 'Варианты выбора'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'items'  => 'Укажите возможнные варианты для последующего пользовательского выбора'
    ));

    /*
    // Definition items list form.
    $szItems = $this->getOption('items', 1);
    $arItems = $this->getDefaults();
    
    for($i = 0; $i < $szItems; $i++)
    {
      if (isset($arItems[$i]) && is_array($arItems[$i])) {
        $this->embedForm($i, new ParameterableParamEnumItemForm($arItems[$i]));
      }
      else {
        $this->embedForm($i, new ParameterableParamEnumItemForm());
      }
    }
    */

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}