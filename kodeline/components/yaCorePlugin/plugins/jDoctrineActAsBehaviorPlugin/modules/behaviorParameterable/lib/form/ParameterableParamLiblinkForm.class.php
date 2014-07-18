<?php
/**
 * Форма добавления расширенного параметра модели в таблицу.
 */
class ParameterableParamLiblinkForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();

    // Define library field.
    $this->setWidget('library', new jWidgetFormJQuerySelect2(
        array(
          'placeholder'       => 'Значения списка',
          'allowClear'        => false,
          'choices_query'     => Doctrine_Core::getTable('FxShopList')->createQuery()->andWhere('is_category = ?', 1)
        ), 
        array()
      )
    );

    $this->setValidator('library', new sfValidatorNumber(array('min' => 1, 'required' => true)));

    // Define is_many field.
    $this->setWidget('is_many', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_many', new sfValidatorBoolean());
    $this->setDefault('is_many', false);

    // Define is_dynamic field.
    $this->setWidget('is_dynamic', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_dynamic', new sfValidatorBoolean());
    $this->setDefault('is_dynamic', false);

    // Define name field.
    $this->setWidget('name', new sfWidgetFormInputHidden());
    $this->setValidator('name', new sfValidatorString(array('required' => true)));
    $this->setDefault('name', klSluggableBuilder::translit($this->getOption('title', rand()), true));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'library'     => 'Словарь',
      'is_many'     => 'Множественный выбор',
      'is_dynamic'  => 'Пользовательский ввод',
      'name'        => 'Тех. название параметра',
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'library'     => 'Выберите словарь (перечень) для выбора элементов списка для значения параметра',
      'is_many'     => 'Допускать выбор нескольких элементов из списка',
      'is_dynamic'  => 'Разрешить пользователям сайта добавлять новые элементы',
      'name'        => 'Название параметра, используемое разработчиками',
    ));

    // Set other parameters for form.
    $this->getWidgetSchema()->getFormFormatter()->setTranslationCatalogue('behavior-parameterable');
  }
}