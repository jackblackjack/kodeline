<?php
/**
 * Контроллер работы с категориями элементов.
 * 
 * @package     backend
 * @subpackage  backendGoods
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBackendFxShopFilterActions extends BaseBackendElementActions
{
  /**
   * Class name for the object
   * 
   * @var string
   */
  protected $objectClassName = 'FxShopFilter';

  /**
   * Class name of form for create new object
   * 
   * @var string
   */
  protected $formClassNew = 'FxShopFilterNewNodeForm';

  /**
   * Class name of form for edit object
   * 
   * @var string
   */
  protected $formClassEdit = 'FxShopFilterEditNodeForm';

  /**
   * Add new filter for content types.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeNew(sfWebRequest $request)
  {
    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // Create new object instance.
    //$this->object = Doctrine::getTable($this->objectClassName)->getRecordInstance();
    $this->object = new $this->objectClassName();

    // If parent id is setted 
    //- set to form and fetch parent object.
    if (0 < (int) $this->parent_id) {
      
      // Fetch parent object.
      $this->parent = Doctrine::getTable($this->objectClassName)
                        ->createQuery()->where('id = ?', $this->parent_id)->fetchOne();

      // Check if parent node exists.
      if (! $this->parent) {
        
        throw new sfException(sprintf($this->getContext()->getI18N()
          ->__('Не найден родительский фильтр (%d).', null, 'flexible-tree'), $this->parent_id));
      }

      // Set default value of parent_id for form.
      $this->object['parent_id'] = $this->parent_id;
    }

    // Initiate form object.
    $this->form = new $this->formClassNew($this->object);

    // Check if requested method not the "POST".
    if (! $request->isMethod(sfRequest::POST) || ! $request->hasParameter($this->form->getName())) {
      return sfView::SUCCESS;
    }

    // Bind form parameters.
    $this->form->bind(
      $request->getParameter($this->form->getName()),
      $request->getFiles($this->form->getName())
    );

    // Check if form values is pass validation.
    if ($this->form->isValid()) {

      if ($this->form instanceof sfFormObject) {
        
        // Save form.
        $this->form->save();

        // Fetch saved object form.
        $this->object = $this->form->getObject();

        // Try to call getMessageAfterNew method in the object.
        if (method_exists($this->object, 'getMessageAfterNew')) {
          
          $this->getUser()->setFlash('success', 
            call_user_func(array($this->object, 'getMessageAfterNew'), $this->object));
        }

        // Try to call getUrlAfterNew method in the object.
        if (method_exists($this->object, 'getUrlAfterNew')) {
          $this->redirect(call_user_func(array($this->object, 'getUrlAfterNew')));
        }

        // Default redirect.
        $this->redirect('@homepage');
      }

      die('Standart save');

      //echo '<pre>'; var_dump($this->form->getValues()); die('</pre>');

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

    // If object has been saved - get rules for redirect.
    if (is_object($this->object) && $this->object->exists())
    {
      // Message after saving.
      if (method_exists($this->object, 'getMessageAfterNew')) {
        $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterNew'), $this->object));
      }

      // Redirect after saving.
      if (method_exists($this->object, 'getUrlAfterNew')) {
        $this->redirect(call_user_func(array($this->object, 'getUrlAfterNew')));
      }

      // Default redirect.
      $this->redirect('@homepage');
    }

    return sfView::SUCCESS;
  }

  /**
   * Редактирование узла дерева.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeEdit(sfWebRequest $request)
  {
    try {
      // Check exists id of the object.
      if (null == ($this->id = $request->getParameter('id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID категории не был указан!', null, 'flexible-tree'));
      }

      // Fetch object by id.
      $this->object = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->id)->fetchOne();

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
    $this->form = new $this->formClassEdit($this->object);

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

  /**
   * Удаление узла дерева.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeDelete(sfWebRequest $request)
  {
    try {
      // Check id of the edited object.
      if (null == ($this->id = $request->getParameter('id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID объекта не был указан!', null, 'flexible-tree'));
      }

      // Define redirect url.
      $redirectUrl = null;

      // Fetch object.
      $this->object = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->id)->fetchOne();

      if (! $this->object) {
        throw new sfException(sprintf($this->getContext()->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->id));
      }

      // Define redirect url.
      if (method_exists($this->object, 'getUrlAfterDelete')) {
        $redirectUrl = call_user_func(array($this->object, 'getUrlAfterDelete'));
      }

      // Delete object.
      $this->object->delete();
    }
    // Catch exceptions.
    catch(Exception $exception)
    {
      // Set error message.
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    if (! $this->object->exists())
    {
      // Redirect after set message.
      if (is_null($redirectUrl))
      {
        $sReferer = $this->getContext()->getUser()->getReferer($request->getReferer());
        $sServerName = $request->getHost();

        if (preg_match("~(ht|f)tp(s?)://$sServerName(/|$|\s)~i", $sReferer)) { 
          $redirectUrl = $sReferer;
        }
        else {
         $redirectUrl = '@homepage'; 
        }
      }

      // Redirect after delete.
      $this->redirect($redirectUrl);
    }

    return sfView::NONE;
  }

  /**
   * Returns all objects were has been fetching by filter result.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeDetail(sfWebRequest $request)
  {
    // Check id of the edited object.
    if (null === ($this->filter_id = $request->getParameter('id', null))) {
      
      throw new sfException($this->getContext()
        ->getI18N()->__('ID фильтра не был указан!', null, 'flexible-shop'));
    }

    // Create record of the component.
    $record = Doctrine::getTable($this->objectClassName)->getRecordInstance();

    // Fetch invoker component id.
    $iComponentId = $record->fetchComponentId($this->objectClassName);

    $iComponentId = 1;


    // Fetch filter data with rules.
    $this->filter = Doctrine::getTable($this->objectClassName)->createQuery('fxf')
                      ->innerJoin('fxf.Rules as rules')
                      ->innerJoin('rules.Type as type')
                      ->innerJoin('rules.Parameter as parameter WITH parameter.component_id = 1')
                      ->where('fxf.id = ?', $this->filter_id)
                      ->fetchOne();

//    echo '<pre>'; var_dump($this->filter->toArray()); die;
      
    // Throw exception if filter has not found.
    if (! $this->filter) {
        
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Фильтр с ID #%d не найден!', null, 'flexible-shop'), $this->filter_id));
    }

    // Load filter query builder helper by context.
    $this->getContext()->getConfiguration()->loadHelpers(array('FilterBuilderQuery'));

    // Process query for fetch.
    $fetchQuery = FxShopFilterBuilder::buildQuery(Doctrine_Query::create(), $this->filter['Rules']);
    $this->results = $fetchQuery->execute();  

    return sfView::SUCCESS;
  }
}