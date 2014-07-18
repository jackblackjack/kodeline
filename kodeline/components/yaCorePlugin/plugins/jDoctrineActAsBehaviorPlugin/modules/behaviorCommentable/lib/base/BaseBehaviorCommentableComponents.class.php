<?php
/**
 * Компонента расширения комментирования объектов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  component
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBehaviorCommentableComponents extends yaBaseActions
{
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