<?php
abstract class BaseBackendKlCmsUserFieldActions extends BaseBackendElementActions
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'klCmsUser';

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
   * Default action for fields
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    // Define doctrine query.
    $query = Doctrine::getTable('klCmsUser')->createQuery();

    // Define configuration for pager.
    $arPagerConfig = sfConfig::get('app_klCmsUsersPlugin_klCmsUsers_per_page', array('per_page' => 10));

    // Define pager.
    $this->items_pager = new sfDoctrinePager('klCmsUser', $arPagerConfig['per_page']);
    $this->items_pager->setQuery($query);
    $this->items_pager->setPage($request->getParameter('upage', 1));
    $this->items_pager->init();

    return sfView::SUCCESS;
  }

  /**
   * Добавление узла дерева.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeNew(sfWebRequest $request)
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
    $this->form = new $this->formClassNew();

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
}

