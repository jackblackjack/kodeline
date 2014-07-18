<?php
abstract class BaseBackendKlCmsSiteActions extends yaBaseActions
{
  /**
   * Default action
   * 
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    // Define doctrine query.
    $query = Doctrine::getTable('klSite')->createQuery('klu');

    // Define configuration for pager.
    $arPagerConfig = sfConfig::get('app_klCmsUsersPlugin_klCmsUser_per_page', array('per_page' => 10));

    // Define pager.
    $this->items_pager = new klDoctrinePager('klSite', $arPagerConfig['per_page']);
    $this->items_pager->setQuery($query);
    $this->items_pager->setPage($request->getParameter('upage', 1));
    $this->items_pager->init();

    return sfView::SUCCESS;
  }

  /**
   * Action for delete user
   * @param sfWebRequest $request Web request.
   */
  public function executeDelete(sfWebRequest $request)
  {
    try {
      // Check id of the edited object.
      if (null == ($this->id = $request->getParameter('id_user', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID объекта не был указан!', null, 'flexible-tree'));
      }

      // Define redirect url.
      $redirectUrl = null;

      // Fetch object.
      $this->object = Doctrine::getTable('klUser')->createQuery()->where('id = ?', $this->id)->fetchOne();

      $this->forward404Unless($this->object);

      if (! $this->object)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID %d не найден!', null, 'flexible-tree'), $this->id));
      }

      // Define redirect url.
      if (method_exists($this->object, 'getUrlAfterDeleteSuccess')) {
        $redirectUrl = call_user_func(array($this->object, 'getUrlAfterDeleteSuccess'));
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
   * Action for add new user.
   * @param sfWebRequest $request Web request.
   */
  public function executeNew(sfWebRequest $request)
  {
    // Initiate form object.
    $this->form = new klDomainsForm();

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

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));

          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryEdit'))
          {
            $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryEdit'), $this->object));
          }

          // Redirect after saving.
          if (method_exists($this->object, 'getUrlAfterCategoryEdit'))
          {
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
   * Action for edit user.
   * @param sfWebRequest $request Web request.
   */
  public function executeEdit(sfWebRequest $request)
  {
    // Check exists user_id.
    $this->forward404Unless(
      ($this->id_user = $request->getParameter('id_user', null)), 
      $this->getContext()->getI18N()->__('ID пользователя не был указан!', null, 'klCmsUsersPlugin'));

    // Fetch user by id.
    $this->object = Doctrine::getTable('klUser')->createQuery()->where('id = ?', $this->id_user)->fetchOne();

    // Throw exception if object is not found.
    $this->forward404Unless($this->object, 
      sprintf($this->getContext()->getI18N()->__('Пользователь с id=%d не найден!', null, 'klCmsUsersPlugin'), $this->id_user));

    // Initiate form object.
    $this->form = new klUserAdminForm($this->object);

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

          // Send jFileAttachable event.
          $this->getContext()->getEventDispatcher()
                  ->notify(new sfEvent(null, 'attachable.autolinkage', array('object' => $this->object)));

          // Message after saving.
          if (method_exists($this->object, 'getMessageAfterCategoryEdit'))
          {
            $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterCategoryEdit'), $this->object));
          }

          // Redirect after saving.
          if (method_exists($this->object, 'getUrlAfterCategoryEdit'))
          {
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
}
