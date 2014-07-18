<?php
/**
 * Базовая форма добавления описания 
 * расширенного параметра для модели.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseParameterableForm extends BasejParameterableSchemaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  { 
    // Call parent form.
    parent::configure();
   
    // Definition list for uses fields of the form.
    $this->useFields(array('id', 'component_id', 'belong', 'name', 'type', 'length', 'default_value', 'is_hidden', 'parent_id'));

    // Redefine component_id field.
    $this->setWidget('component_id', new sfWidgetFormInputHidden());

    // Redefine title field.
    $this->setWidget('title', new sfWidgetFormInputText());
    $this->setValidator('title', new sfValidatorString(array('required' => false)));

    // Redefine type field.
    $this->setWidget('type', new sfWidgetFormSelect(array('choices' => $this->getSupportedTypes())));
    $this->setValidator('type', new sfValidatorChoice(array('required' => true, 'choices' => array_keys($this->getSupportedTypes()))));

    // Redefine is_public field.
    $this->setWidget('is_hidden', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_hidden', new sfValidatorBoolean());

    // Redefine is_group field.
    $this->setWidget('is_group', new sfWidgetFormInputCheckbox());
    $this->setValidator('is_group', new sfValidatorBoolean());

    // Redefine parent_id field.
    $this->widgetSchema['parent_id'] = new jWidgetFormJQuerySelect2(
      array(
        'placeholder'             => 'Группа',
        'select_default_choices'  => array(-1 => 'Без группы'),
        'choices_query'           => Doctrine_Core::getTable('jParameterableSchema')->createQuery('psc')->innerJoin('psc.Translation')->andWhere('psc.is_group = ?', 1)
      ), 
      array()
    );
  }

  /**
   * Retrieve list of supported types for extended params.
   * 
   * @return array
   */
  public static function getSupportedTypes()
  {
    return array(
      'number'    => 'Число',
      'string'    => 'Текст',
      'enum'      => 'Список',
      'date'      => 'Дата',
      'time'      => 'Время',
      'checkbox'  => 'Флаг',
      'radio'     => 'Переключатель',
      'timestamp' => 'Дата и время',
      'liblink'   => 'Связка со словарем',
      'image'     => 'Изображение',
      'document'  => 'Документ',
      'file'      => 'Файл',
//      'address'   => 'Адрес',
    );
  }

  /**
   * {@inheritDoc}
   */
  protected function doUpdateObject($values)
  {
    // Define column names of the table.
    $arFormFields = $this->getValidatorSchema()->getFields();

    $arColumns = $this->getObject()->getTable()->getColumns();
    $arColumnKeys = array_keys($arColumns);
    $szColumnKeys = count($arColumnKeys);

    // Fill values for fields of the this->object.
    for ($i = 0; $i < $szColumnKeys; $i++)
    {
      // Column is identifier - skip it.
      if ($this->getObject()->getTable()->isIdentifier($arColumnKeys[$i])) continue;

      // Define name callback method.
      $callbackName = 'default' . sfInflector::camelize($arColumnKeys[$i]);
      $bCallbackExists = method_exists($this->getObject(), $callbackName);

      // If value for property is not set by the form and method for set default value is exists.
      if (! isset($values[$arColumnKeys[$i]])) {
        if ($bCallbackExists) {
          $values[$arColumnKeys[$i]] = call_user_func(array($this->getObject(), $callbackName), $form);
        }
      }
      // If value for property set by form.
      elseif (isset($values[$arColumnKeys[$i]]))
      {
        // Define name callback method.
        $callbackName = 'processing' . sfInflector::camelize($arColumnKeys[$i]);
        $bCallbackExists = method_exists($this->getObject(), $callbackName);

        // Check this->object callback method for processing value.
        if ($bCallbackExists) {
          $values[$arColumnKeys[$i]] = call_user_func_array(array($this->getObject(), $callbackName), array($values[$arColumnKeys[$i]], $this));
        }
        else {
          $values[$arColumnKeys[$i]] = $values[$arColumnKeys[$i]];
        }
      }

      // If form validation for field processing as boolean but type of column is an integer - convert value.
      if (isset($arFormFields[$arColumnKeys[$i]]) 
          && ($arFormFields[$arColumnKeys[$i]] instanceof sfValidatorBoolean) 
          && 'integer' == $arColumns[$arColumnKeys[$i]]['type']) {
        $values[$arColumnKeys[$i]] = (int) $values[$arColumnKeys[$i]];
      }
    }

    // Call parent method.
    parent::doUpdateObject($values);
  }

  /**
   * {@inheritDoc}
   */
  protected function doSave($con = null)
  {
    if (null === $con) {
      $con = $this->getConnection();
    }

    $this->updateObject();

    // If object is new - set typeof value.
    if ($this->getObject()->isNew())
    {
      if (0 < (int) $this->getObject()->get('parent_id'))
      {
        $parent = $this->getObject()->getTable()->createQuery()->where('id = ?', $this->getObject()->get('parent_id'))->fetchOne();

        if (! $parent)
        {
          throw new sfException(sprintf(sfContext::getInstance()
            ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->getObject()->get('parent_id')));
        }

        // Fetch parent tree for node if table has tamplate option hasManyRoots of the FlexibleTree.
        if ($this->getObject()->getTable()->hasTemplate('FlexibleTree') && $this->getObject()->getTable()->getTree()->getAttribute('hasManyRoots'))
        {
          $rootColumnName = $this->object->getTable()->getTree()->getAttribute('rootColumnName');
          $this->getObject()->set($rootColumnName, $parent->get($rootColumnName));
        }

        $parent->getNode()->addChild($this->getObject());
      }
      else {
        $this->getObject()->getTable()->getTree()->createRoot($this->getObject());
      }

      // Fetch component name by id.
      $componentName = Doctrine::getTable('jBehaviorComponent')->createQuery()->select('name')
                              ->where('id = ?', $this->getObject()->getComponentId())
                              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      // If table extended by Parameterable behavior.
      if (Doctrine::getTable($componentName)->hasTemplate('Parameterable'))
      {
        // If options has specified column name for value.
        if (Doctrine::getTable($componentName)->getTemplate('Parameterable')->getOption('versionable_value')) {
          $this->getObject()->set('typeof', Doctrine::getTable($componentName)->getTemplate('Parameterable')->getOption('versionable_value'));
          $this->getObject()->save();
        }
      }
    }
    else {
      // Call parent method.
      parent::doSave($con);
    }

    // embedded forms
    $this->saveEmbeddedForms($con);
  }
}