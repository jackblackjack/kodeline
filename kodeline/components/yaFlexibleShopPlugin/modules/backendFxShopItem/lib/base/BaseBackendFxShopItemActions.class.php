<?php
/**
 * Контроллер работы с категориями элементов.
 * 
 * @package     yaFlexibleShopPlugin
 * @subpackage  backendFShopCategory
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBackendFxShopItemActions extends BaseBackendElementActions
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'FxShopItem';

  /**
   * Class name of form for create new object
   * @var string
   */
  protected $formClassNew = 'FxShopItemNewNodeForm';

  /**
   * Class name of form for edit object
   * @var string
   */
  protected $formClassEdit = 'FxShopItemEditNodeForm';

  /**
   * Добавление узла дерева как категории.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeCategoryNew(sfWebRequest $request)
  {
    // Initiate form object.
    $this->form = new FxShopItemNewCategoryForm();

    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // If parent id is setted - set to form and fetch parent object.
    if (0 < (int) $this->parent_id)
    {
      // Set default value of parent_id for form.
      $this->form->setDefault('parent_id', $this->parent_id);

      // Fetch parent object.
      $this->parent = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->parent_id)->fetchOne();

      if (! $this->parent)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->parent_id));
      }
    }

    // Check requested method.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
    {
      // Bind request parameters for form.
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      // Validation form.
      if ($this->form->isValid())
      {
        if ($this->form instanceof sfFormObject)
        {
          try {
            $this->form->save();
          }
          // Catch any exceptions.
          catch(Exception $exception)
          {
            $this->getUser()->setFlash('error', $exception->getMessage());
            return sfView::ERROR;
          }

          // Fetch saved object form.
          $this->object = $this->form->getObject();

          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryNew')) {
            $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryNew'), $this->object));
          }

          // Redirect after saving.
          if (method_exists($this->object, 'getUrlAfterCategoryNew')) {
            $this->redirect(call_user_func(array($this->object, 'getUrlAfterCategoryNew')));
          }

          // Default redirect.
          $this->redirect('@homepage');

          return sfView::SUCCESS;
        }

        // Define form values.
        $arFormValues = $this->form->getValues();

        // Initiate new object instance.
        $this->object = new $this->objectClassName();

        // Fetch parent tree for node if table has tamplate option hasManyRoots of the FlexibleTree.
        if ($this->object->getTable()->hasTemplate('FlexibleTree') && $this->object->getTable()->getTree()->getAttribute('hasManyRoots'))
        {
          $rootColumnName = $this->object->getTable()->getTree()->getAttribute('rootColumnName');

          if ($this->parent)
          {
            $this->object[$rootColumnName] = $this->parent[$rootColumnName];
          }
        }

        // Define column names of the table.
        $arFormFields = $this->form->getValidatorSchema()->getFields();
        $arColumns = $this->object->getTable()->getColumns();
        $arColumnKeys = array_keys($arColumns);
        $szColumnKeys = count($arColumnKeys);

        try {
          // Fill values for fields of the this->object.
          for ($i = 0; $i < $szColumnKeys; $i++)
          {
            // Column is identifier - skip it.
            if ($this->object->getTable()->isIdentifier($arColumnKeys[$i])) continue;

            // Define callback method for field of this->object.
            $fillCallbackName = 'processing' . sfInflector::camelize($arColumnKeys[$i]);
            $defaultCallbackName = 'default' . sfInflector::camelize($arColumnKeys[$i]);
            $bFillCallbackExists = method_exists($this->object, $fillCallbackName);
            $bDefaultCallbackExists = method_exists($this->object, $fillCallbackName);

            // Processing common values.
            if (isset($arFormValues[$arColumnKeys[$i]]))
            {
              // Check this->object callback method for processing value.
              if ($bFillCallbackExists)
              {
                $this->object[$arColumnKeys[$i]] = call_user_func_array(array($this->object, $fillCallbackName), array($arFormValues[$arColumnKeys[$i]], $this->form));
              }
              else {
                if ($arFormFields[$arColumnKeys[$i]] instanceof sfValidatorBoolean && 'integer' == $arColumns[$arColumnKeys[$i]]['type']) {
                  $this->object[$arColumnKeys[$i]] = (int) $arFormValues[$arColumnKeys[$i]];  
                }
                else {
                  $this->object[$arColumnKeys[$i]] = $arFormValues[$arColumnKeys[$i]];
                }
              }
            }
            // If form value is not exists but method exists.
            elseif (! isset($arFormValues[$arColumnKeys[$i]]) && $bDefaultCallbackExists) {
              $this->object[$arColumnKeys[$i]] = call_user_func(array($this->object, $defaultCallbackName), $this->form);
            }
          }

          // Set absolute values for node,
          $this->object['is_category'] = 1;

          // Create child node.
          if ($this->parent)
          {
            $this->parent->getNode()->addChild($this->object);
          }
          // Create root node.
          else {
            $this->object->getTable()->getTree()->createRoot($this->object);
          }
        }
        // Catch any exceptions.
        catch(Exception $exception)
        {
          $this->getUser()->setFlash('error', $exception->getMessage());
          return sfView::ERROR;
        }
      }
    }

    // If object has been saved - get rules for redirect.
    if (is_object($this->object) && $this->object->exists())
    {
      // Message after saving.
      if (method_exists($this->object, 'getMessageAfterCategoryNew')) {
        $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryNew'), $this->object));
      }

      // Redirect after saving.
      if (method_exists($this->object, 'getUrlAfterCategoryNew')) {
        $this->redirect(call_user_func(array($this->object, 'getUrlAfterCategoryNew')));
      }

      // Default redirect.
      $this->redirect('@homepage');
    }

    return sfView::SUCCESS;
  }

  /**
   * Редактирование узла дерева как категории.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeCategoryEdit(sfWebRequest $request)
  {
    try {
      // Check exists id of the object.
      if (null == ($this->id = $request->getParameter('id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID категории не был указан!', null, 'flexible-tree'));
      }

      // Fetch object by id.
      $this->object = Doctrine::getTable($this->objectClassName)->createQuery()
                        ->where('id = ?', $this->id)
                        ->andWhere('is_category = ?', 1)
                        ->fetchOne();

      // Throw exception if object is not found.
      if (! $this->object)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->id));
      }
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());
      return sfView::ERROR;
    }

    // Initiate form object.
    $this->form = new FxShopItemEditCategoryForm($this->object);

    // Check requested method.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
    {
      // Bind request parameters for form.
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      // Validation form.
      if ($this->form->isValid())
      {
        // If form is instanceof sfFormObject - save it.
        if ($this->form instanceof sfFormObject)
        {
          try {
            $this->form->save();
          }
          // Catch any exceptions.
          catch(Exception $exception)
          {
            $this->getUser()->setFlash('error', $exception->getMessage());
            return sfView::ERROR;
          }

          // Fetch saved object form.
          $this->object = $this->form->getObject();

          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryEdit')) {
            $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryEdit'), $this->object));
          }

          // Redirect after saving.
          if (method_exists($this->object, 'getUrlAfterCategoryEdit')) {
            $this->redirect(call_user_func(array($this->object, 'getUrlAfterCategoryEdit')));
          }

          // Default redirect.
          $this->redirect('@homepage');

          return sfView::SUCCESS;
        }

        // Define form values.
        $arFormValues = $this->form->getValues();

        // Define column names of the table.
        $arFormFields = $this->form->getValidatorSchema()->getFields();
        $arColumns = $this->object->getTable()->getColumns();
        $arColumnKeys = array_keys($arColumns);
        $szColumnKeys = count($arColumnKeys);

        try {
          // Fill values for fields of the this->object.
          for ($i = 0; $i < $szColumnKeys; $i++)
          {
            // Column is identifier - skip it.
            if ($this->object->getTable()->isIdentifier($arColumnKeys[$i])) continue;

            // Define callback method for field of this->object.
            $fillCallbackName = 'processing' . sfInflector::camelize($arColumnKeys[$i]);
            $defaultCallbackName = 'default' . sfInflector::camelize($arColumnKeys[$i]);
            $bFillCallbackExists = method_exists($this->object, $fillCallbackName);
            $bDefaultCallbackExists = method_exists($this->object, $fillCallbackName);

            // Processing common values.
            if (isset($arFormValues[$arColumnKeys[$i]]))
            {
              // Check this->object callback method for processing value.
              if ($bFillCallbackExists)
              {
                $this->object[$arColumnKeys[$i]] = call_user_func_array(array($this->object, $fillCallbackName), array($arFormValues[$arColumnKeys[$i]], $this->form));
              }
              else {
                if ($arFormFields[$arColumnKeys[$i]] instanceof sfValidatorBoolean && 'integer' == $arColumns[$arColumnKeys[$i]]['type']) {
                  $this->object[$arColumnKeys[$i]] = (int) $arFormValues[$arColumnKeys[$i]];  
                }
                else {
                  $this->object[$arColumnKeys[$i]] = $arFormValues[$arColumnKeys[$i]];
                }
              }
            }
            // If form value is not exists but method exists.
            elseif (! isset($arFormValues[$arColumnKeys[$i]]) && $bDefaultCallbackExists) {
              $this->object[$arColumnKeys[$i]] = call_user_func(array($this->object, $defaultCallbackName), $this->form);
            }
          }

          // Set absolute values for node.
          $this->object['parent_id'] = ((0 >= (int) $arFormValues['parent_id']) ? null : $arFormValues['parent_id']);
          $this->object['is_category'] = 1;
          $this->object->save();
        }
        // Catch any exceptions.
        catch(Exception $exception)
        {
          $this->getUser()->setFlash('error', $exception->getMessage());
          return sfView::ERROR;
        }

        if ($this->object->getLastModified())
        {
          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryEdit')) {
            $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryEdit'), $this->object));
          }

          // Redirect after saving.
          if (method_exists($this->object, 'getUrlAfterCategoryEdit')) {
            $this->redirect(call_user_func(array($this->object, 'getUrlAfterCategoryEdit')));
          }

          // Default redirect.
          $this->redirect('@homepage');
        }
      }
    }

    return sfView::SUCCESS;
  }
}