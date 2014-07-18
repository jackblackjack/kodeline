<?php
/**
 * PluginProduct form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class yaBaseFxShopItemForm extends PluginFxShopItemForm
{
  /**
   * {@inheritDoc}
   */
  public function setDefaults($defaults)
  {
    // Update defaults usually.
    parent::setDefaults($defaults);

    if (empty($defaults['id']) && !empty($defaults['parent_id']))
    {
      // Embed form by parent object's parameters.
      $this->embedForm('parameters', new FxShopParameterableValuesForm(
        array(),
        array('component' => $this->getModelName(), 'object_id' => $defaults['parent_id']))
      );
    }
    elseif(! empty($defaults['id']))
    {
      // Embed form by current object's parameters.
      $this->embedForm('parameters', new FxShopParameterableValuesForm(
        array(),
        array('component' => $this->getModelName(), 'object_id' => $defaults['id'], 'set_values' => true))
      );
    }

    return $this;
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
      if (! isset($values[$arColumnKeys[$i]]))
      {
        if ($bCallbackExists)
        {
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
        if ($bCallbackExists)
        {
          $values[$arColumnKeys[$i]] = call_user_func_array(
                                          array($this->getObject(), $callbackName),
                                          array($values[$arColumnKeys[$i]], $this)
                                       );
        }
        else {
          $values[$arColumnKeys[$i]] = $values[$arColumnKeys[$i]];
        }
      }

      // If form validation for field processing as boolean but type of column is an integer - convert value.
      if (isset($arFormFields[$arColumnKeys[$i]]) 
          && ($arFormFields[$arColumnKeys[$i]] instanceof sfValidatorBoolean) 
          && 'integer' == $arColumns[$arColumnKeys[$i]]['type'])
      {
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

    if ($this->getObject()->isNew())
    {
      if (0 < (int) $this->getObject()->get('parent_id'))
      {
        // Fetch object by parent id.
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
    }
    else {
      // Call parent method.
      parent::doSave($con);
    }

    // save embedded forms.
    $this->saveEmbeddedForms($con);
  }

  /**
   * @{inheritDoc}
   */
  public function saveEmbeddedForms($con = null, $forms = null)
  {   
    if (null === $con)
    {
      $con = $this->getConnection();
    }

    if (null === $forms)
    {
      $forms = $this->embeddedForms;
    }

    foreach ($forms as $form)
    {
      if ($form instanceof sfFormObject)
      {
        $form->getObject()->save($con);
        $form->saveEmbeddedForms($con);
      }
      else
      {
        // Define save callback method.
        if (method_exists($form, 'saveFormValues'))
        {
          call_user_func_array(array($form, 'saveFormValues'), array($con, $this));
        }
        else {
          $this->saveEmbeddedForms($con, $form->getEmbeddedForms());
        }
      }
    }
  }
}
