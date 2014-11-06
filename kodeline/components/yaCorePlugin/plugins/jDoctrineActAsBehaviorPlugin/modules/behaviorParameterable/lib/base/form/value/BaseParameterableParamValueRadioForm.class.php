<?php
/**
 * Базовая Форма добавления значения для
 * поля типа "переключатель" (radio).
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueRadioForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Call parent method.
    parent::configure();

    //var_dump($this->getDefaults());

    /*
    // Check exists all options for query.
    if (null === $this->getOption('component_id', null) || 
        null === $this->getOption('object_id', null) || 
        null === $this->getOption('parameter_id', null))
    {
      throw new sfException(sfContext::getInstance()->getI18N()
        ->__('Указаны не все параметры для корректной работы виджета', null, 'behavior-parameterable'));
    }
    */

    // Define value field.
    $this->setWidget('value', new sfWidgetFormSelectRadio(
        array(
          'choices'           => array('Yes', 'No')
          /*
          'choices_query'     => Doctrine_Core::getTable('jParameterableStringValue')->createQuery()
                                  ->andWhere('component_id = ?', $this->getOption('component_id'))
                                  ->andWhere('object_id = ?', $this->getOption('object_id'))
                                  ->andWhere('parameter_id = ?', $this->getOption('parameter_id'))
          */
        ), 
        array()
      )
    );

    $this->setValidator('value', new sfValidatorNumber(array('required' => false)));
  }
}