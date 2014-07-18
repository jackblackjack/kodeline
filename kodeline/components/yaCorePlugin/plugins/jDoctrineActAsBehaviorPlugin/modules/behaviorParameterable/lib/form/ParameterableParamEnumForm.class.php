<?php
/**
 * Base form for insert field with type "Enum" (Список).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
class ParameterableParamEnumForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine is_dynamic field.
    $this->setWidget('is_dynamic', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_dynamic', new sfValidatorBoolean());

    // Define extensions field.
    $this->setWidget('items', new jWidgetFormInputStrings());
    $this->setValidator('items', new jValidatorStrings(array('required' => false)));

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'is_dynamic'  => 'Пополняемый список',
      'items'       => 'Элементы списка',
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'is_dynamic'  => 'Разрешить авторизованным пользователям пополнять список',
    ));

    /* Definition items list form.
    $szItems = $this->getOption('qitems', 1);
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
  }
}