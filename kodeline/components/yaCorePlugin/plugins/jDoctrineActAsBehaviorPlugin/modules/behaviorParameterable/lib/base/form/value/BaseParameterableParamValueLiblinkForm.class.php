<?php
/**
 * Базовая форма добавления значения 
 * расширенного (связь с словарем) параметра 
 * для конкретного объекта.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableParamValueLiblinkForm extends BaseParameterableParamValueForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    parent::configure();

    // Check exists all options for query.
    //if (null === $this->getOption('component_id', null) || null === $this->getOption('object_id', null) || null === $this->getOption('parameter_id', null))
    if (null === $this->getOption('component_id', null) || null === $this->getOption('parameter_id', null))
    {
      throw new sfException(sfContext::getInstance()->getI18N()
        ->__('Указаны не все параметры для корректной работы виджета', null, 'behavior-parameterable'));
    }

    // Fetch library ID for link.
    $iLibrary = Doctrine_Core::getTable('jParameterableOption')->createQuery()->select('value')
                                ->andWhere('parameter_id = ?', $this->getOption('parameter_id'))
                                ->andWhere('name = ?', 'library')
                                ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    
    // Define value field.
    $this->setWidget('value', new jWidgetFormJQuerySelect2(
        array(
          'placeholder'       => 'Выберите из списка',
          'allowClear'        => true,
          'multiple'          => (bool) $this->getOption('is_many', false),
          'choices_query'     => Doctrine_Core::getTable('FxShopList')->createQuery()
                                  ->andWhere('parent_id = ?', $iLibrary)
                                  ->andWhere('is_category = ?', 0)
                                  //->andWhere('is_active = ?', 1)
        ), 
        array()
      )
    );

    $this->setValidator('value', new sfValidatorPass(array('required' => false)));
  }
}