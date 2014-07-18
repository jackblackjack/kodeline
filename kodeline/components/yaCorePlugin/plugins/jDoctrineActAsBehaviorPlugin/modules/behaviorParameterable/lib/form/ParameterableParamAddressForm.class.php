<?php
/**
 * Форма добавления поля объекта.
 * Тип объекта: адрес (с использованием интерактивной карты).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamAddressForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Define is_manual field: user can use manual position for label.
    $this->setWidget('is_manual', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_manual', new sfValidatorBoolean());
    $this->setDefault('is_manual', false);

    // Redefine name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'is_manual'   => 'Ручное позиционирование',
      'name'        => 'Метка поля'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'is_manual' => 'Разрешить пользователю добавлять адрес путем ручной установки указателя на карте',
      'name'      => 'Метка, используемая при разработке'
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}