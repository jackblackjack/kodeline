<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
//class klFxShopFilterRuleForm extends BaseFxShopFilterRuleForm
class FxShopFilterNewRuleParamForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Definition fields list for uses in the form.
    /*
    $this->useFields(array('id', 'filter_id', 'type_id', 'parameter_id', 'logic_in', 'is_order_desc'));

    $this->setWidgets(array(
      'id'             => new sfWidgetFormInputHidden(),
      'filter_id'      => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('FxShopFilter'), 'add_empty' => false)),
      'component_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Component'), 'add_empty' => false)),
      'parameter_id'   => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('Parameter'), 'add_empty' => false)),
      'logic_in'       => new sfWidgetFormInputText(),
      'is_order_desc'  => new sfWidgetFormInputText(),
      'value_min'      => new sfWidgetFormTextarea(),
      'value_max'      => new sfWidgetFormTextarea(),
      'value_eq'       => new sfWidgetFormTextarea(),
      'value_ne'       => new sfWidgetFormTextarea(),
      'value_less'     => new sfWidgetFormTextarea(),
      'value_greater'  => new sfWidgetFormTextarea(),
      'value_contains' => new sfWidgetFormTextarea(),
      'root_id'        => new sfWidgetFormInputText(),
      'parent_id'      => new sfWidgetFormInputText(),
      'path'           => new sfWidgetFormTextarea(),
      'level'          => new sfWidgetFormInputText(),
      'sibling'        => new sfWidgetFormInputText(),
      'children'       => new sfWidgetFormInputText(),
      'created_at'     => new sfWidgetFormDateTime(),
      'updated_at'     => new sfWidgetFormDateTime(),
      'created_by'     => new sfWidgetFormInputText(),
      'updated_by'     => new sfWidgetFormInputText(),
      'position'       => new sfWidgetFormInputText(),
    ));
    */

    // Define field "filter_id".
    $this->setWidget('filter_id', new sfWidgetFormInputHidden());
    $this->setValidator('filter_id', new sfValidatorInteger(array('required' => false)));

    // Define field "type_id".
    $this->setWidget('type_id', new sfWidgetFormDoctrineChoice(
                                          array(
                                            'multiple'  => false,
                                            'model'     => 'FxShopItem'
                                          )));

    $this->setValidator('type_id', new sfValidatorDoctrineChoice(
                                          array(
                                            'multiple'  => false,
                                            'model'     => 'FxShopItem',
                                            'required'  => true
                                          )));
    //
    // @todo: 
    // Add main parameters, as:
    // - "is_active"
    // - "title"
    // - "annotation"
    // - "detail"
    //

    // Define field "parameter_id".
    $this->setWidget('parameter_id', new sfWidgetFormDoctrineChoice(
                                          array(
                                            'multiple' => false,
                                            'model' => 'jParameterableSchema'
                                          )));

    $this->setValidator('parameter_id', new sfValidatorDoctrineChoice(
                                              array(
                                                'multiple' => false, 
                                                'model' => 'jParameterableSchema', 
                                                'required' => true
                                              )));

    // Define field "in_logic".
    //
    // Field "in_logic" may be as hidden and as radio.
    //
    if ($this->getOption("in_logic_as_hidden", false)) {
      $this->setWidget("in_logic", new sfWidgetFormInputHidden());
      $this->setValidator("in_logic", new sfValidatorInteger(array('required' => true, 'min' => 1, 'max' => 1)));
      $this->setDefault("in_logic", 1);
    }
    else {
      $this->setWidget("in_logic", new sfWidgetFormSelectRadio(array('choices' => self::getLogicChoices())));
      $this->setValidator("in_logic", new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getLogicChoices()))));
    }

    // Define field "compare".
    $this->setWidget("compare", new sfWidgetFormSelect(array('choices' => self::getCompareChoices())));
    $this->setValidator("compare", new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getCompareChoices()))));

    // Define field "value".
    $this->setWidget("value", new sfWidgetFormInputText());
    $this->setValidator("value", new sfValidatorPass(array('required' => true)));

    // Set labels.
    $this->getWidgetSchema()->setLabels(array(
      'type_id'       => 'Тип содержания',
      'parameter_id'  => 'Параметр',
      'compare'       => 'Сравнение',
      'value'         => 'Значение'
    ));

    // Set widgets helps.
    $this->getWidgetSchema()->setHelps(array(
      'type_id'       => 'Тип содержания',
      'parameter_id'  => 'Расширенный параметр',
      'compare'       => 'Сравнение',
      'value'         => 'Значение'
    ));

    // Set name of widget.
    $this->getWidgetSchema()->setNameFormat('rule[%s]');
  }

  /**
   * Returns available choices for field "in_logic".
   * 
   * @return array
   */
  public static function getLogicChoices()
  {
    $i18n = sfContext::getInstance()->getI18N();
    return array(1 => $i18n->__('И', null, 'flexible-shop'), 0 => $i18n->__('ИЛИ', null, 'flexible-shop'));
  }

  /**
   * Returns available choices for field "compare".
   * 
   * @return array
   */
  public static function getCompareChoices()
  {
    // Define i18n.
    $i18n = sfContext::getInstance()->getI18N();

    return array(
      'min'       => $i18n->__('больше либо равно чем', null, 'flexible-shop'),
      'max'       => $i18n->__('меньше либо равно чем', null, 'flexible-shop'),
      'eq'        => $i18n->__('равно', null, 'flexible-shop'),
      'ne'        => $i18n->__('не равно', null, 'flexible-shop'),
      'le'        => $i18n->__('меньше чем', null, 'flexible-shop'),
      'ge'        => $i18n->__('больше чем', null, 'flexible-shop'),
      'contains'  => $i18n->__('содержит', null, 'flexible-shop')
    );
  }
}
