<?php
/**
 * Компонента расширения комментирования объектов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  component
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBehaviorParameterableComponents extends yaBaseActions
{
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
      if (empty($this->modelName)) {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check exists id of the object.
      if (empty($this->object_id)) {
        throw new sfException($this->getContext()
          ->getI18N()->__('ID объекта для редактирования не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable')) {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->modelName));
      }

      // Fetch object data by this id.
      $this->object = Doctrine::getTable($this->modelName)
                        ->createQuery('p')->andWhere('p.id = ?', $this->object_id)->fetchOne();

      // Check object is fetched.
      if (! $this->object) {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Объект с ID "%d" не найден!', null, 'behavior-parameterable'), $this->object_id));
      }

      // If model has been specified column' value for of Parameterable behavior.
      if (null !== ($pv = $this->object->getTable()->getTemplate('Parameterable')->getOption('param_value_col', null))) {
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
   * Выборка списка комментариев.
   * @param sfWebRequest $request
   */
  public function executeCommentableList()
  {
    // Проверка указания имени модели.
    if (empty($this->object)) return sfView::NONE;

    // Имя модели, к которой указывается комментарий.
    $this->objectClassName = get_class($this->object);

    // Номер ресурса, к которому указывается комментарий.
    $this->objectId = $this->object['id'];

    // Номер комментария, к которому идет комментарий.
    $this->parentId = (empty($this->parent) ? null : $this->parent);

    // Определение формы добавления комментария.
    $this->form = new commentableForm();
    $arDefaults = array('model' => $this->objectClassName, 'resource'  => $this->objectId);

    // Если указан родительский комментарий - указываем его в форме.
    if (null !== $this->parentId) $arDefaults['parent_id'] = $this->parentId;

    // Если пользователь авторизован - указываем его id.
    if ($this->getUser()->isAuthenticated()) $arDefaults['author_id'] = $this->getUser()->getProfile()->getId();

    $this->form->setDefaults($arDefaults);

    return sfView::SUCCESS;
  }
}