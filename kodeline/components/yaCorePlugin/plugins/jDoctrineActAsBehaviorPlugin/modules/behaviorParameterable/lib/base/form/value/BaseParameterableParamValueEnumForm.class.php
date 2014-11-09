<?php
/**
 * Базовая форма добавления значения 
 * расширенного (enumerate) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueEnumForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();

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
    if (false && null === $this->getOption('object_id', null))
    {   
      $arSelectedValues = Doctrine_Core::getTable('jParameterableOption')->createQuery()
                              ->select('value')->indexBy('id')
                              ->where('parameter_id = ?', $this->getOption('parameter_id'))
                              ->andWhere('name = ?', 'item')
                              ->fetchArray();
    }
    */

    // Define selectbox widget.
    $component = $this->getOption('Component');
    $this->setWidget('value', new sfWidgetFormSelect(
        array(
          'multiple'          => (bool) $this->getOption('is_many', false),
          'choices'           => $this->getChoices($component['id'], $this->getOption('parameter_id'), $this->getOption('object_id', null))
        ), 
        array()
      )
    );

    // Define value field.
    /*
    $this->setWidget('value', new jWidgetFormJQuerySelect2(
        array(
          'placeholder'       => 'Значения списка',
          'title_record_col'  => 'value',
          'allowClear'        => true,
          'multiple'          => (bool) $this->getOption('is_many', false),
          'choices'           => $arPredefinedChoices
        ), 
        array()
      )
    );
    */

    $this->setValidator('value', new sfValidatorNumber(array('required' => false)));
  }

  /**
   * Retrieve choices for selectbox widget.
   * 
   * @param integer $iParameter
   * @param integer $iObject
   * @return array
   */
  protected function getChoices($iComponent, $iParameter, $iObject = null)
  {
    /*
    var_dump($sComponent); var_dump($iParameter); var_dump($iObject); die;
    echo Doctrine_Core::getTable('jParameterableStringValue')
                            ->createQuery('jpsv')->select('jpsv.id, jpsvtr.value')->indexBy('jpsv.id')
                            ->leftJoin('jpsv.Translation as jpsvtr')
                            ->where('jpsv.component_name = ?', $sComponent)
                            ->andWhere('jpsv.parameter_id = ?', $iParameter)->getSqlQuery();
    */

    // Fetch predefined values for enum list.
    $arPredefinedChoices = Doctrine_Core::getTable('jParameterableStringValue')
                            ->createQuery('jpsv')->select('jpsv.id, jpsvtr.value')->indexBy('jpsv.id')
                            ->leftJoin('jpsv.Translation as jpsvtr')
                            ->where('jpsv.component_id = ?', $iComponent)
                            ->andWhere('jpsv.parameter_id = ?', $iParameter)
                            ->orderBy('jpsv.position DESC')
                            ->fetchArray();


    // Prepare array values.
    $valueColName = 'value';
    array_walk($arPredefinedChoices, function (&$v) use ($valueColName) { $v = $v['Translation']['ru'][$valueColName]; });

    // Return prepared values.
    return $arPredefinedChoices;
  }
}
