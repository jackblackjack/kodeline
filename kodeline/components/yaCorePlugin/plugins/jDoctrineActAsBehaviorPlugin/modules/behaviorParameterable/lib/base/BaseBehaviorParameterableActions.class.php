<?php
/**
 * Котроллер расширения комментирования объектов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  controller
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBehaviorParameterableActions extends BaseyaMultipageActions
{
  /**
   * {@inheritDoc}
   */
  protected $configurationName = 'parameterable';

  /**
   * List extended parameters for model.
   * 
   * @param sfWebRequest $request
   */
  public function executeComponent(sfWebRequest $request)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->model = $request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Fetch parameter's names list.
      $this->parametersSchema = Doctrine::getTable($this->model)->getRecordInstance()->fetchExtendedParameterNames();
    }
    // Catch exceptions.
    catch(Exception $exception)
    {
      // Set error message.
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    return sfView::SUCCESS;   
  }

  /**
   * Вывод списка параметров (полей) объекта.
   * 
   * Производит выборку иерархии параметров для модели, 
   * а также выборку значений параметров объекта по его ID.
   * 
   * @param sfWebRequest $request
   */
  public function executeParameters(sfWebRequest $request)
  {
    try {
      // Check exists id of the object.
      if (null === ($this->modelName = $request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the object.
      if (null === ($this->object_id = $request->getParameter('object_id', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID объекта для редактирования не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable'))
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->modelName));
      }

      // Fetch object data by this id.
      $this->object = Doctrine::getTable($this->modelName)->createQuery('p')->andWhere('p.id = ?', $this->object_id)->fetchOne();

      // Check object is fetched.
      if (! $this->object)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID "%d" не найден!', null, 'behavior-parameterable'), $this->object_id));
      }

      // If model has been specified column' value for of Parameterable behavior.
      if (null !== ($pv = $this->object->getTable()->getTemplate('Parameterable')->getOption('param_value_col', null)))
      {
        $this->objectParamValue = $this->object[$pv];
      }

      // Fetch schema of the parameters.
      $this->parametersSchema = $this->object->fetchExtendedParametersSchema();
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
   * Экшен изменения значения параметра объекта модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeParameterValue(sfWebRequest $request)
  {
    // Flag of the process result.
    $bModified = false;

    try {
      // Check exists model of the object.
      if (null === ($this->modelName = $request->getParameter('model', null))) {
        throw new sfException($this->getContext()->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the object.
      if (null === ($this->object_id = $request->getParameter('object_id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID объекта для редактирования не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id parameter of the object.
      if (null === ($this->param_id = $request->getParameter('param_id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID объекта для редактирования не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable')) {
        throw new sfException(sprintf($this->getContext()->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->modelName));
      }

      // Initiate new object record instance.
      $record = new $this->modelName();
      $this->param = $record->fetchExtendedParameterById($this->param_id, $this->modelName);

      // Throw exception if parameter is not found.
      if (! $this->param)
      {
        throw new sfException(sprintf(
          $this->getContext()->getI18N()->__('Параметр #%d для модели "%s" не найден!', null, 'behavior-parameterable'), 
          $this->param_id, $this->modelName));
      }

      // Fetch list columns of the table.
      $arTableColumns = array_keys(Doctrine::getTable($this->modelName)->getColumns());

      // Fetch object data by this id.
      $this->object = Doctrine::getTable($this->modelName)->createQuery('omp')
                      ->select('omp.' . implode(', omp.', array_merge($arTableColumns, array($this->param['name']))))
                      ->andWhere('omp.id = ?', $this->object_id)
                      ->fetchOne();

      // Throw exception if object not found.
      if (! $this->object)
      {
        throw new sfException(sprintf(
          $this->getContext()->getI18N()->__('Объект #%d для модели "%s" не найден!', null, 'behavior-parameterable'), 
          $this->object_id, $this->modelName));
      }

      // Fetch parameter scheme.
      $parameterFormClassName = 'ParameterableParamValue' . sfInflector::camelize($this->param['type']) . 'Form';

      // Sets default values.
      $arDefaults = array(
        'value'       => $this->object[$this->param['name']],
        'model_name'  => $this->modelName,
        'object_id'   => $this->object['id'],
        'param_id'    => $this->param_id
      );

      $this->form = new $parameterFormClassName($arDefaults, array('length' => $this->param['length']));

      // Check requested method.
      if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
      {
        // Bind request parameters for form.
        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        // Validation form.
        if ($this->form->isValid())
        {
          // Define form values.
          $arFormValues = $this->form->getValues();
          $bModified = $this->object->setExtendedParameters(array($this->param['name'] => $arFormValues['value']));
        }
      }

    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    // If object has been created.
    if ($bModified)
    {
      // Message after saving.
      if (method_exists($this->object, 'getMessageAfterModify')) {
        $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterModify'), $this->object));
      }

      // Redirect after saving.
      if (method_exists($this->object, 'getUrlAfterModify')) {
        $this->redirect(call_user_func(array($this->object, 'getUrlAfterModify'), $this->object));
      }

      // Default redirect.
      $this->redirect('@parameterable_component_parameters?model=' . $this->modelName . '&object_id=' . $this->object['id']);
    }

    return sfView::SUCCESS;  
  }

  /**
   * Добавление значения для параметра модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeParameterAddValues(sfWebRequest $request)
  {
    // Flag of the process result.
    $bModified = false;

    try {
      // Check exists component name.
      if (null === ($this->componentName = $request->getParameter('component_name', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the belong node.
      if (null === ($this->belongBy = $request->getParameter('belong_by', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Родительский узел не был указан!', null, 'behavior-parameterable'));
      }

      // Check exists id parameter of the object.
      if (null === ($this->parameterId = $request->getParameter('parameter_id', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Редактируемый параметер был указан!', null, 'behavior-parameterable'));
      }

      // Check exists value for model's parameter.
      if (null === ($this->value = $request->getParameter('value', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Значение для параметра не указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->componentName)->hasTemplate('Parameterable'))
      {
        throw new sfException(sprintf($this->getContext()->getI18N()
                    ->__('Компонент "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->componentName));
      }

      // Fetch parameter record.
      $record = new $this->componentName();
      $bModified = $record->addExtendedParameterValues($this->parameterId, $this->value, $this->belongBy, $this->componentName);

      die(var_dump($bModified));
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    // If object has been created.
    if ($bModified)
    {
      // Message after saving.
      if (method_exists($this->object, 'getMessageAfterModify')) {
        $this->getUser()->setFlash('success', call_user_func(array($this->object, 'getMessageAfterModify'), $this->object));
      }

      // Redirect after saving.
      if (method_exists($this->object, 'getUrlAfterModify')) {
        $this->redirect(call_user_func(array($this->object, 'getUrlAfterModify'), $this->object));
      }

      // Default redirect.
      $this->redirect('@parameterable_component_parameters?model=' . $this->modelName . '&object_id=' . $this->object['id']);
    }

    return sfView::SUCCESS;
  }

  /**
   * Выборка значений списка.
   * 
   * @param sfWebRequest $request
   */
  public function executeParameterGetValues(sfWebRequest $request)
  {
    try {
      // Check exists component name.
      if (null === ($this->componentName = $request->getParameter('component_name', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the belong node.
      if (null === ($this->belongBy = $request->getParameter('belong_by', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Родительский узел не был указан!', null, 'behavior-parameterable'));
      }

      // Check exists id parameter of the object.
      if (null === ($this->parameterId = $request->getParameter('parameter_id', null)))
      {
        throw new sfException($this->getContext()->getI18N()
                    ->__('Редактируемый параметер был указан!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->componentName)->hasTemplate('Parameterable'))
      {
        throw new sfException(sprintf($this->getContext()->getI18N()
                    ->__('Компонент "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->componentName));
      }

      // Define limits for fetching.
      $iPage = $request->getParameter('p', 1);
      $iLimit = 100;
      $iOffset = (($iPage < 2) ? 0 : $iPage * $iLimit);


      // Fetch parameter record.
      $record = new $this->componentName();

      // Fetch values for parameter.
      $arValues = $record->fetchExtendedParameterValues(
                    $this->parameterId,
                    $request->getParameter('l', sfContext::getInstance()->getUser()->getCulture()),
                    $iLimit,
                    $iOffset,
                    $this->belongBy,
                    $this->componentName
                  );
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      // If request use isXmlHttpRequest - render results as json.
      if ($this->getContext()->getRequest()->isXmlHttpRequest())
      {
        $this->setLayout(false);
        sfConfig::set('sf_web_debug', false);

        $request->setRequestFormat('json');
        $this->getContext()->getResponse()->setContentType('application/json; charset=utf-8');

        return $this->renderText(json_encode(array('result' => array('values' => array(), 'error' => 1, 'message' => $exception->getMessage()))));
      }

      // Set flash message.
      $this->getUser()->setFlash('error', $exception->getMessage());

      // Return error.
      return sfView::ERROR;
    }

    // If request use isXmlHttpRequest - render results as json.
    if ($this->getContext()->getRequest()->isXmlHttpRequest())
    {
      $this->setLayout(false);
      sfConfig::set('sf_web_debug', false);

      $request->setRequestFormat('json');
      $this->getContext()->getResponse()->setContentType('application/json; charset=utf-8');

      // Prepare array values.
      $valueColName = 'value';
      array_walk($arValues['values'], function (&$v) use ($valueColName) { 
        $v = $v['Translation']['ru'][$valueColName]; 
      });

      return $this->renderText(json_encode(array('result' => $arValues)));
    }

    // Set values.
    $this->values = $arValues;

    // Return success.
    return sfView::SUCCESS;
  } 

  /**
   * Добавление параметра для модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeNew(sfWebRequest $request)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->modelName = $request->getParameter('model', null))) {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable')) {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->modelName));
      }
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());
      return sfView::ERROR;
    }

    // Flag of the process result.
    $bCreated = false;

    // Define form class name.
    $formClassName = 'ParameterableForm';
    $arConfig = sfConfig::get('app_jDoctrineActAsBehaviorPlugin_behaviorParameterable');

    if (is_array($arConfig) && array_key_exists($this->modelName, $arConfig)) {
      if (! empty($arConfig[$this->modelName]['forms']) && ! empty($arConfig[$this->modelName]['forms']['new'])) {
        $formClassName = $arConfig[$this->modelName]['forms']['new'];
      }
    }

    // Initiate form object.
    $this->form = new $formClassName();

    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // If parent id is setted - set to form and
    // fetch parent object.
    if (! empty($this->parent_id))
    {
      $this->form->setDefault('parent_id', $this->parent_id);
      $this->parent = Doctrine::getTable($this->objectClassName)->createQuery()->where('id = ?', $this->parent_id)->fetchOne();
    }

    // Initiate new record of the table.
    $record = Doctrine::getTable($this->modelName)->getRecordInstance();

    // Set default value for component_id form's field by model's component name.
    $this->form->setDefault('component_id', $record->fetchComponentId($this->modelName));

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
            $arFormValues = $this->form->getObject();
          }
          // Catch any exceptions.
          catch(Exception $exception)
          {
            $this->getUser()->setFlash('error', $exception->getMessage());
            return sfView::ERROR;
          }
        }
        else {
          // Define form values.
          $arFormValues = $this->form->getValues();
        }

        try {
          // Try create new parametr in the table.
          $bCreated = $record->createExtendedParameter(
            $arFormValues['name'],
            $arFormValues['type'],
            $arFormValues['length'],
            (0 < (int) $arFormValues['parent_id'] ? $arFormValues['parent_id'] : null),
            $arFormValues['default_value'],
            $arFormValues['title'],
            $this->getUser()->getCulture(),
            (bool) $arFormValues['is_public'],
            (strlen($arFormValues['param']) ? $arFormValues['param'] : null)
          );
        }
        // Catch any exceptions.
        catch(Exception $exception)
        {
          $this->getUser()->setFlash('error', $exception->getMessage());
          return sfView::ERROR;
        }
      }
    }

    if ($bCreated && is_object($record))
    {
      // Define redirect url after create.
      $redirectUrl = '@parameterable_component?model=' . $this->modelName;

      if (method_exists($record, 'getUrlAfterParamNew')) {
        $redirectUrl = call_user_func(array($record, 'getUrlAfterParamNew'));
      }

      // Define message after delete.
      if (method_exists($record, 'getMessageAfterParamNew')) {
        $this->getUser()->setFlash('success', call_user_func(array($record, 'getMessageAfterParamNew')));
      }

      // Redirect after delete.
      $this->redirect($redirectUrl);
    }

    return sfView::SUCCESS;
  }

  /**
   * Удаление параметра из модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeDelete(sfWebRequest $request)
  {
    // Flag of the process result.
    $bDeleted = false;

    try {
      // Check exists id of the component.
      if (null === ($this->component_id = $request->getParameter('component_id', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the component's parameter.
      if (null === ($this->param_id = $request->getParameter('param_id', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID параметра не было указано!', null, 'behavior-parameterable'));
      }

      // Fetch component name by id.
      $this->componentName = Doctrine::getTable('jBehaviorComponent')->createQuery()->select('name')
                              ->where('id = ?', $this->component_id)
                              ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);


      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->componentName)->hasTemplate('Parameterable'))
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->componentName));
      }

      // Initiate component object.
      $this->component = new $this->componentName();

      // Fetch field name in the component table.
      $param = $this->component->fetchExtendedParameterById($this->param_id);

      // Try delete parameter from table and Parameterable scheme.
      $bDeleted = $this->component->deleteExtendedParameter($param['name']);

      if (! $bDeleted)
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Возникла ошибка при удалении параметра!', null, 'behavior-parameterable'));
      }
      else
      {
        $param->delete();
      }
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    if ($bDeleted && is_object($this->component))
    {
      // Define redirect url after delete.
      $redirectUrl = sprintf('@parameterable_component_parameters?model=%s&object_id=%d', $this->componentName, $this->component_id);

      if (method_exists($this->component, 'getUrlAfterParamDelete')) {
        $redirectUrl = call_user_func(array($this->component, 'getUrlAfterParamDelete'));
      }

      // Define message after delete.
      if (method_exists($this->component, 'getMessageAfterParamDelete')) {
        $this->getUser()->setFlash('success', call_user_func(array($this->component, 'getMessageAfterParamDelete'), $param));
      }

      // Redirect after delete.
      $this->redirect($redirectUrl);
    }

    return sfView::NONE;
  }

  /**
   * Изменение свойств поля модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeEdit(sfWebRequest $request)
  {
    // Fetch parameter id.
    $this->id = $request->getParameter('param_id', null);

    // Throw exception if parameter id not found.
    $this->forward404Unless($this->id, $this->getContext()
      ->getI18N()->__('ID объекта для редактирования не было указано', null, 'behavior-parameterable'));

    // Fetch object by this id.
    $this->object = Doctrine::getTable('jParameterableSchema')->createQuery('psc')
                      ->innerJoin('psc.Component as pscmp')
                      ->innerJoin('psc.Translation as psctr WITH psctr.lang = ?', yaContext::getInstance()->getUser()->getCulture())
                      ->andWhere('psc.id = ?', $this->id)
                      ->fetchOne();

    // Throw exception if object has not found.
    $this->forward404Unless($this->object, sprintf($this->getContext()
      ->getI18N()->__('Объект с ID "%d" не найден', null, 'behavior-parameterable'), $this->id));

    try
    {
      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->object['Component']['name'])->hasTemplate('Parameterable'))
      {
        throw new sfException(
                    sprintf($this->getContext()
                        ->getI18N()->__('Компонент "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), 
                            $this->modelName));
      }

      // Define form class name.
      $formClassName = 'ParameterableEditForm';

      // Fetch classname form by configuration.
      $arConfig = sfConfig::get('app_jDoctrineActAsBehaviorPlugin_behaviorParameterable');
      if (is_array($arConfig) && array_key_exists($this->modelName, $arConfig))
      {
        if (! empty($arConfig[$this->modelName]['forms']) && ! empty($arConfig[$this->modelName]['forms']['edit']))
        {
          $formClassName = $arConfig[$this->modelName]['forms']['edit'];
        }
      }

      // Sets current object type.
      $this->type = $request->getParameter('type', $this->object->type);
      $this->object->type = $this->type;

      // Define current culture.
      $this->culture = $request->getParameter('l', yaContext::getInstance()->getUser()->getCulture());

      // Initiate form object.
      $this->form = new $formClassName($this->object);
      $this->form->embedI18n(array($this->culture));

      // Processing POST data.
      if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
      {
        // Fix posted type and reinitialize form for correct embedded it.
        $arPosted = $request->getParameter($this->form->getName());
        if (! empty($arPosted['type'])) $this->object->type = $this->type = $arPosted['type'];

        // Reinitiate form object.
        $this->form = new $formClassName($this->object);
        $this->form->embedI18n(array($this->culture));
        
        // Bind request parameters for form.
        $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

        // Validation form.
        if ($this->form->isValid())
        {
          // Define form values.
          $arFormValues = $this->form->getValues();
          var_dump($arFormValues); die('VALID');

          //$bCreated = $record->createExtendedParameter($arFormValues['name'], $arFormValues['type']);
        }

        die('POSTED');
      }

      // If request has been ajax - output as ajax.
      if ($this->getContext()->getRequest()->isXmlHttpRequest())
      {
        $this->getResponse()->xmlHttpResponse();
      }

      return sfView::SUCCESS;
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
   * Действие прикрепления файла к полю.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeUploadField(sfWebRequest $request)
  {
    // Инициализация переменной.
    $arResult = array('msg' => '', 'proc' => false, 'file' => null);
    $dbConn = Doctrine_Manager::connection();  

    try {
      if (sfRequest::POST == $request->getMethod())
      {
        // Определение загружаемого файла.
        $rawJpeg = file_get_contents("php://input");

        // Загрузка 
        $this->getContext()->getConfiguration()->loadHelpers(array('yaDiskStore'));

        // Определение директорий хранения файлов для фотографий из камеры.
        $storeConf = yaDiskStoreHelper::getStoreConfig('camera_gallery_store', true);

        // Определение имени временного файла.
        $sTempFileName = md5($rawJpeg) . '.jpg';
        $sTempFilePath = $storeConf['temporary']['path'] . DIRECTORY_SEPARATOR . $sTempFileName;

        $sFileName = md5($rawJpeg) . '.jpg';

        // Сохранение временного файла.
        @file_put_contents($sTempFilePath, $rawJpeg, FILE_BINARY);

        // Проверка загрузки файла.
        if (! file_exists($sTempFilePath))
        {
          throw new sfException(
            $this->getContext()->getI18N()->__('Файл не удалось загрузить!', null, 'contest')
          );
        }

        // Загрузка файла для обработки.
        $image = new sfImage($sTempFilePath, 'image/jpg');
       
        $dbConn->beginTransaction();

        // Создание копий файла для каждого из типов файлов.
        foreach($storeConf as $type => $conf)
        {
          // Создание подложки.
          //$backImage = new sfImage();
          //$backImage->setMIMEType('image/png');

          if ('temporary' == $type) continue;
          if ('default' == $type) continue;

          if (! empty($conf['width']) && ! empty($conf['height']))
          {
            // Генерация прозрачной подложки.
            //$backImage->getAdapter()->setHolder($backImage->getAdapter()->getTransparentImage($conf['width'], $conf['height']));

            $image->thumbnail($conf['width'], $conf['height']);
            //$backImage->overlayAlpha($image, 'center');
          }

          //$backImage->saveAs($conf['path'] . DIRECTORY_SEPARATOR . $sFileName, $backImage->getMIMEType());
          $image->saveAs($conf['path'] . DIRECTORY_SEPARATOR . $sFileName, 'image/jpg');

          // Создание объекта файла.
          $cameraFile = new jDoctrineCameraPhoto();
          //$cameraFile['content_type_id'] = $iFileTypeId;
          $cameraFile['path']            = $conf['rel_path'];
          $cameraFile['name']            = $sFileName;
          $cameraFile['original_name']   = $sFileName;
          $cameraFile['extension']       = 'jpg';
          $cameraFile['size']            = filesize($conf['path'] . DIRECTORY_SEPARATOR . $sFileName);
          $cameraFile['label']           = $type;
          $cameraFile->save();
        }

        /*
        // Определение существования типа контента файла.
        $iFileTypeId = Doctrine::getTable('jFileContentType')->createQuery('ct')->select('ct.id')
                ->where('ct.name = ?', $image->getMIMEType())
                ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

        if (! $iFileTypeId)
        {
          // Создание объекта типа контента файлов.
          $fileType = new jFileContentType();
          $fileType->name = $image->getMIMEType();
          $fileType->save();
          $iFileTypeId = $fileType->id;
          unset($fileType);
        }
        */

        $dbConn->commit();
        $arResult['proc'] = true;
        $arResult['file'] = sprintf('%s%s', $storeConf['small']['rel_path'], $sFileName);
      }
    }
    // Обработка исключений класса sfImageTransformException
    catch(sfImageTransformException $Exception)
    {
      $dbConn->rollback();

      $arResult['proc'] = false;
      $arResult['msg'] = $Exception->getMessage();
    }
    // Обработка исключений класса sfException
    catch(sfException $Exception)
    {
      $dbConn->rollback();

      $arResult['proc'] = false;
      $arResult['msg'] = $Exception->getMessage();
    }

    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);

    $this->getContext()->getResponse()->setContentType('application/json; charset=utf-8');
    return $this->renderText(json_encode(array('result' => $arResult)));
  }
}