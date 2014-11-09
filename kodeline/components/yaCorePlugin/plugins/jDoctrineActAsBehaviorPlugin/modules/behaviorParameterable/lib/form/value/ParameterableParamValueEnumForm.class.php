<?php
/**
 * Форма добавления значения 
 * расширенного (enumerate) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamValueEnumForm extends BaseParameterableParamValueEnumForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    $idParameter = $this->getOption('id');
    $idObject = $this->getOption('belong');

    parent::configure();

    //echo '<pre>'; var_dump($this->getOptions()); echo '</pre>'; die;
    /*
    // Check exists all options for query.
    if (null === $this->getOption('component_id', null) || 
        null === $this->getOption('object_id', null) || 
        null === $this->getOption('parameter_id', null)) {
      throw new sfException(sfContext::getInstance()->getI18N()
        ->__('Указаны не все параметры для корректной работы виджета', null, 'behavior-parameterable'));
    }
    */

    // Fetch selected values for current list.
    /*
    if (null === $this->getOption('object_id', null))
    {   
      $arSelectedValues = Doctrine_Core::getTable('jParameterableOption')->createQuery()
                              ->select('value')->indexBy('id')
                              ->where('parameter_id = ?', $this->getOption('parameter_id'))
                              ->andWhere('name = ?', 'item')
                              ->fetchArray();
    }
    */

    // Define url for get element.
    $component = $this->getOption('Component');
    $getRequestUrl = sfContext::getInstance()->getRouting()->generate('parameterable_component_parameter_value_get',
                      array(
                        'component_name'  => $component['name'],
                        'parameter_id'    => $this->getOption('id'),
                        'belong_by'       => $this->getOption('belong')
                      ), true);


    // Define url for add new element.
    $postRequestUrl = sfContext::getInstance()->getRouting()->generate('parameterable_component_parameter_value_add',
                      array(
                        'component_name'  => $component['name'],
                        'parameter_id'    => $this->getOption('id'),
                        'belong_by'       => $this->getOption('belong')
                      ), true);

    // Define value field.
    $this->setWidget('value', new jWidgetFormJQuerySelect2(
        array(
          'placeholder'         => sfContext::getInstance()->getI18N()->__('Выберите из списка', null, 'behavior-parameterable'),
          'allowClear'          => (! (bool) $this->getOption('is_require', false)),
          'multiple'            => (bool) $this->getOption('is_many', false),
          'add_new_item_allow'  => (bool) $this->getOption('is_dynamic', false),
//          'choices'             => $this->getChoices($this->getOption('Component')['name'], $idParameter, $idObject),
          'add_new_item_url'    => $postRequestUrl,
          'ajax'                => array('url' => $getRequestUrl)
        )
      )
    );

    // Set value validator.
    $this->setValidator('value', new sfValidatorNumber(array('required' => (bool) $this->getOption('is_require', false))));
  }
}