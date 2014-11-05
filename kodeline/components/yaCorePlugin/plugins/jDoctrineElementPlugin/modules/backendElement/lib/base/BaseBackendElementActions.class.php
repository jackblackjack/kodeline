<?php
/**
 * Контроллер работы с элементами.
 * 
 * @package     jDoctrineElementPlugin
 * @subpackage  backendElement
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseBackendElementActions extends yaBaseActions
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'jElement';

  /**
   * Class name of form for create new object
   * @var string
   */
  protected $formClassNew = 'jElementForm';

  /**
   * Class name of form for edit object
   * @var string
   */
  protected $formClassEdit = 'jElementForm';

  /**
   * Добавление узла дерева.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeNew(sfWebRequest $request)
  {
    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // Create model class.
    //$this->object = Doctrine::getTable($this->objectClassName)->getRecordInstance();
    $this->object = new $this->objectClassName();

    // If parent id is setted - set to form and fetch parent object.
    if (0 < (int) $this->parent_id)
    {
      // Fetch parent object.
      $this->parent = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->parent_id)->fetchOne();

      if (! $this->parent)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->parent_id));
      }

      // Set default value of parent_id for form.
      $this->object['parent_id'] = $this->parent_id;
    }

    // Initiate form object.
    $this->form = new $this->formClassNew($this->object);

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

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));

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

          // Create child node.
          if ($this->parent)
          {
            $this->parent->getNode()->addChild($this->object);
          }
          // Create root node.
          else {
            $this->object->getTable()->getTree()->createRoot($this->object);
          }

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));
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
      if (method_exists($this->object, 'getMessageAfterNew'))
      {
        $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterNew'), $this->object));
      }

      // Redirect after saving.
      if (method_exists($this->object, 'getUrlAfterNew'))
      {
        /*
        if ($this->getContext()->getRequest()->isXmlHttpRequest())
        {
          $this->setLayout(false);
          sfConfig::set('sf_web_debug', false);

          $this->getRequest()->setParameter('sf_format','json');
          $this->getResponse()->setContentType('application/json; charset=utf-8');

          // Render text for ajax request.
          $this->renderText(json_encode(array('id' => $this->object['id'], )));
        }
        */

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
    // Check exists id of the object.
    if (null === ($this->id = $request->getParameter('id', null))) {
      throw new sfException($this->getContext()
        ->getI18N()->__('ID категории не был указан!', null, 'flexible-tree'));
    }

    // Fetch object by id.
    $this->object = Doctrine::getTable($this->objectClassName)
                      ->createQuery()->where('id = ?', $this->id)->fetchOne();

    // Throw exception if object is not found.
    if (! $this->object) {
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->id));
    }

    // Initiate form object instance.
    // FxShopItemEditNodeForm
    $this->form = new $this->formClassEdit($this->object);

    // Check if request method is POST.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName())) {
      
      // Bind parameters for form.
      $this->form->bind(
        $request->getParameter($this->form->getName()), 
        $request->getFiles($this->form->getName())
      );

      // Validate form.
      if ($this->form->isValid()) {
       
        // If form is instanceof sfFormObject - save it.
        if ($this->form instanceof sfFormObject) {
          // Save the form.
          $this->form->save();

          // Fetch saved object form.
          $this->object = $this->form->getObject();

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));

          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryEdit')) {

            $this->getUser()->setFlash('success', call_user_func(
              array($this->object, 'getMessageAfterCategoryEdit'), $this->object));
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

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));
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
      if (null == ($this->id = $request->getParameter('id', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID объекта не был указан!', null, 'flexible-tree'));
      }

      // Define redirect url.
      $redirectUrl = null;

      // Fetch object.
      $this->object = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->id)->fetchOne();

      if (! $this->object)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->id));
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
   * Вывод родительских элементов дерева.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeRootList(sfWebRequest $request)
  {
    try {
      // Define model name for works.
      $this->modelName = $this->objectClassName;

      // Check exists id of the node for view.
      if (null !== ($this->id = $request->getParameter('id', null)))
      {
        // Fetch data of the node.
        $this->node = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->id)->fetchOne();

        // Fetch first level nodes of the tree.
        $this->list = Doctrine::getTable($this->objectClassName)->getTree()->fetchBranch($this->id, array('depth' => 1));
      }
      else {
        $this->list = Doctrine::getTable($this->objectClassName)->getTree()->fetchRoots();
      }
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());
      return sfView::ERROR;
    }

    return sfView::SUCCESS;
  }

  /**
   * Вывод дочерних элементов первого уровня от текущего.
   * 
   * @param integer id Уникальный ключ для выбора элемента дерева.
   * @param sfWebRequest $request Web request.
   */
  public function executeNodeDetail(sfWebRequest $request)
  {
    try {
      // Save class name.
      $this->modelName = $this->objectClassName;

      // Check exists id of the node for view.
      if (null === ($this->id = $request->getParameter('id', null))) {
        throw new sfException(sprintf("Cannot find node unique id."));
      }

      // Fetch data of the root tree node.
      $this->rootNode = Doctrine::getTable($this->modelName)->getTree()->fetchRoot($this->id);

      // Fetch data of the node.
      $this->node = Doctrine::getTable($this->modelName)
                      ->createQuery()->where('id = ?', $this->id)->fetchOne();

      //var_dump($this->node->getNode()->getRecord()->toArray());

      // Set the flag by result of compare nodes.
      $this->bSameNodes = ($this->rootNode === $this->node);

      // Fetch first level children of the current node.
      $this->list = Doctrine::getTable($this->modelName)
                      ->getTree()->fetchBranch($this->id, array('depth' => 1));
    }
    // Catch any exceptions.
    catch(Exception $exception) {
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    return sfView::SUCCESS;
  }

  /**
   * Изменение позиции (position) узла дерева,
   * если дерево поддерживает Sortable расширение.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNodePosition(sfWebRequest $request)
  {
    try {
      // Check id of the edited object.
      if (null == ($this->id = $request->getParameter('id', null)))
      {
        throw new sfException($this->getContext()->getI18N()->__('Номер объекта не был указан!', null, 'flexible-tree'));
      }

      // Check setting motion parameter.
      if (null == $request->getParameter('motion', null)) {
        throw new sfException($this->getContext()->getI18N()->__('Направление движения объекта не указано!', null, 'flexible-tree'));
      }

      // Check parameter motion available value.
      if (! in_array(strtolower($request->getParameter('motion')), array('up', 'down'))) {
        throw new sfException($this->getContext()->getI18N()->__('Направление движения не поддерживается!', null, 'flexible-tree'));
      }

      // Fetch object.
      $this->object = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->id)->fetchOne();

      // Throw exception if node is not found.
      if (! $this->object) {
        throw new sfException(sptintf($this->getContext()->getI18N()->__('Объект с номером %d не найден!', null, 'flexible-tree'), $this->id));
      }

      // Check supports behavior Sortable.
      if (! $this->object->isSortable()) {
        throw new sfException($this->getContext()->getI18N()->__('Объект не поддерживает расширение Sortable!', null, 'flexible-tree'));
      }

      switch (strtolower($request->getParameter('motion')))
      {
        case 'up':
          $this->object->demote();
        break;

        case 'down':
          $this->object->promote();
        break;
      }

      // Redirect after set message.
      $redirectUrl = '@homepage';
      $sReferer = $this->getUser()->getReferer($request->getReferer());
      $sServerName = $request->getHost();

      if (preg_match("~(ht|f)tp(s?)://$sServerName(/|$|\s)~i", $sReferer)) { $redirectUrl = $sReferer; }
      return $this->redirect($redirectUrl);
    }
    // Catch exceptions.
    catch(Exception $exception)
    {
      die($exception->getMessage());

      // Set error message.
      $this->getUser()->setFlash('error', $exception->getMessage());

      // Redirect after set message.
      $redirectUrl = '@homepage';
      $sReferer = $this->getUser()->getReferer($request->getReferer());
      $sServerName = $request->getHost();

      if (preg_match("~(ht|f)tp(s?)://$sServerName(/|$|\s)~i", $sReferer)) { $redirectUrl = $sReferer; }
      return $this->redirect($redirectUrl);  
    }

    return sfView::NONE;
  }
}