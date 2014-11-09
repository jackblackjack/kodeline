<?php
/**
 * Class yaBaseFxShopFilterForm implements sfFormObject class.
 *
 * @package    yaFlexibleShopPlugin
 * @subpackage form
 * @author     chugarev@gmail.com
 * @version    $Id$
 */
class yaBaseFxShopFilterForm extends BaseFxShopFilterForm
{
  /**
   * {@inheritDoc}
   */
  protected function doUpdateObject($values)
  {
    // Fetch fields list of this form.
    $arFormFields = $this->getValidatorSchema()->getFields();

    // Fetch columns list of the form object.
    $arColumns = $this->getObject()->getTable()->getColumns();
    $arColumnKeys = array_keys($arColumns);
    $szColumnKeys = count($arColumnKeys);

    // Fill fields values for the form object.
    for ($i = 0; $i < $szColumnKeys; $i++) {
      
      // If column is identifier - skip it.
      if ($this->getObject()->getTable()->isIdentifier($arColumnKeys[$i])) continue;

      // Define name of the callback object method.
      // Try to call "defaultColumnName".
      $sCallbackName = 'default' . sfInflector::camelize($arColumnKeys[$i]);
      $bCallbackExists = method_exists($this->getObject(), $sCallbackName);

      // Log checking method in form object.
      sfContext::getInstance()->getLogger()->info(
        sprintf("Method %s::%s %s", get_class($this->getObject()), $sCallbackName, ($bCallbackExists ? "is exists" : "is not exists"))
      );

      // If value for property is not set by the form and method for set default value is exists.
      if (! isset($values[$arColumnKeys[$i]]) && $bCallbackExists) {
        $values[$arColumnKeys[$i]] = call_user_func(array($this->getObject(), $callbackName), $form);
      }
      // If value for property set by form.
      elseif (isset($values[$arColumnKeys[$i]])) {
        
        // Define name callback method.
        $sCallbackName = 'processing' . sfInflector::camelize($arColumnKeys[$i]);
        $bCallbackExists = method_exists($this->getObject(), $sCallbackName);

        // Log checking method in form object.
        sfContext::getInstance()->getLogger()->info(
          sprintf("Method %s::%s %s", get_class($this->getObject()), $sCallbackName, ($bCallbackExists ? "is exists" : "is not exists"))
        );

        // Check this->object callback method for processing value.
        if ($bCallbackExists) {

          $values[$arColumnKeys[$i]] = call_user_func_array(
                                          array($this->getObject(), $sCallbackName), 
                                          array($values[$arColumnKeys[$i]], $this)
                                       );
        }
        else {
          $values[$arColumnKeys[$i]] = $values[$arColumnKeys[$i]];
        }
      }

      // If form validation for field processing 
      // as boolean but type of column is an integer - convert value.
      if (isset($arFormFields[$arColumnKeys[$i]]) && ($arFormFields[$arColumnKeys[$i]] instanceof sfValidatorBoolean)) {

        if ('integer' == $arColumns[$arColumnKeys[$i]]['type']) {
          $values[$arColumnKeys[$i]] = (int) $values[$arColumnKeys[$i]];
        }

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
    // Define connection to database.
    if (null === $con) { $con = $this->getConnection(); }

    // Update object by form.
    $this->updateObject();

    // If parent is specified - set value to parameter "parent_id" of object.
    if (0 < (int) $this->getObject()->get('parent_id')) {
      
      // Fetch parent object by parent_id.
      $parent = $this->getObject()->getTable()->createQuery()
                  ->where('id = ?', $this->getObject()->get('parent_id'))
                  ->fetchOne();

      // Check parent exists.
      if (! $parent) {

        throw new sfException(sprintf(sfContext::getInstance()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->getObject()->get('parent_id')));
      }

      // Set value to parent parameter for node, if table use template options hasManyRoots of the FlexibleTree behavior.
      if ($this->getObject()->getTable()->hasTemplate('FlexibleTree') && $this->getObject()->getTable()->getTree()->getAttribute('hasManyRoots'))
      {
        $rootColumnName = $this->object->getTable()->getTree()->getAttribute('rootColumnName');
        $this->getObject()->set($rootColumnName, $parent->get($rootColumnName));
      }

      // Add node as child of parent.
      $parent->getNode()->addChild($this->getObject());
    }
    else {
      // Create root node.
      $this->getObject()->getTable()->getTree()->createRoot($this->getObject());
    }

    // Save embedded forms.
    $this->saveEmbeddedForms($con);
  }

  /**
   * {@inheritDoc}
   */
  public function saveEmbeddedForms1($con = null, $forms = null)
  {
    // Log checking method in form object.
    sfContext::getInstance()->getLogger()->info(
      sprintf("Method %s::%s %s", get_class($this->getObject()), $sCallbackName, ($bCallbackExists ? "is exists" : "is not exists"))
    );

    // Fetch list forms of rules.
    $arRulesForms = $this->getEmbeddedForm('rules')->getEmbeddedForms();

    $arFormValues = $this->getValues();

    // Create collection for save new values.
    $rulesCollection = new Doctrine_Collection('FxShopFilterRule');

    $szRulesForms = count($arFormValues['rules']);
    for($i = 0; $i < $szRulesForms; $i++) {
      $rule = new FxShopFilterRule();
            $rule['filter_id'] = $this->getObject()->getId();
            $rule['component_id'] = $arFormValues['rules'][$i]['component_id'];
            $rule['parameter_id'] = $arFormValues['rules'][$i]['parameter_id'];
            $rule['condition_name'] = $conditionName;

            if (0 === $fcv)
            {
              $rule['is_and'] = (int) ('and' === $arFormValues['rules'][$i]['conditions'][$fc]['logic']);
            }
            else {
              $rule['is_and'] = 0;
            }


      $szConditions = count($arFormValues['rules'][$i]['conditions']);
      for($fc = 0; $fc < $szConditions; $fc++)
      {
        if ( !is_array($arFormValues['rules'][$i]['conditions'][$fc]['value']))
        {
          $rule = new FxShopFilterRule();
          $rule['filter_id'] = $this->getObject()->getId();
          $rule['component_id'] = $arFormValues['rules'][$i]['component_id'];
          $rule['parameter_id'] = $arFormValues['rules'][$i]['parameter_id'];
          $rule['is_and'] = (int) ('and' === $arFormValues['rules'][$i]['conditions'][$fc]['logic']);

          $fieldPostfix = $arFormValues['rules'][$i]['conditions'][$fc]['compare'];
          $rule['value_' . $fieldPostfix] = $arFormValues['rules'][$i]['conditions'][$fc]['value'];

          $rulesCollection->add($rule);
        }
        else {
          $szValues = count($arFormValues['rules'][$i]['conditions'][$fc]['value']);
          $conditionName = 'asdasd';

          for($fcv = 0; $fcv < $szValues; $fcv++)
          {
            $rule = new FxShopFilterRule();
            $rule['filter_id'] = $this->getObject()->getId();
            $rule['component_id'] = $arFormValues['rules'][$i]['component_id'];
            $rule['parameter_id'] = $arFormValues['rules'][$i]['parameter_id'];
            $rule['condition_name'] = $conditionName;

            if (0 === $fcv)
            {
              $rule['is_and'] = (int) ('and' === $arFormValues['rules'][$i]['conditions'][$fc]['logic']);
            }
            else {
              $rule['is_and'] = 0;
            }

            $fieldPostfix = $arFormValues['rules'][$i]['conditions'][$fc]['compare'];
            $rule['value_' . $fieldPostfix] = $arFormValues['rules'][$i]['conditions'][$fc]['value'][$fcv];

            $rulesCollection->add($rule);
          }
        }
      }
    }

    // Save all filter rules.
    $rulesCollection->save();

    // Call parent method.
    //parent::saveEmbeddedForms($con, $forms);
  }
}
