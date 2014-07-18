<?php
/**
 * Controller for processing parameterable behaviour.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class behaviorParameterableActions extends BaseBehaviorParameterableActions
{

  /**
   * List extended parameters for model.
   * 
   * @param sfWebRequest $request
   */
  public function executeComponent(sfWebRequest $request)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->modelName = $request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Fetch parameter's names list.
      $this->parametersSchema = Doctrine::getTable($this->modelName)->getRecordInstance()->fetchExtendedParameterNames();
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
   * Вывод списка расширенных параметров модели.
   * 
   * @param sfWebRequest $request
   */
  public function executeComponent11(sfWebRequest $request)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->modelName = $request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Имя компонента не указано!', null, 'behavior-parameterable'));
      }

      // Check exists name of the component.
      if (null === ($this->belong = $request->getParameter('belong', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Идентификатор принадлежности поля не найден!', null, 'behavior-parameterable'));
      }

      // Fetch object data by this id.
      $this->object = Doctrine::getTable($this->modelName)
              ->createQuery()->andWhere('id = ?', $this->belong)->fetchOne();

      // Throw exception if object is not found.
      if (! $this->object)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Компонент #%d не найден!', null, 'flexible-tree'), $this->id));
      }

      // Fetch parameter's names list.
      $this->parametersSchema = $this->object->fetchExtendedParameterNames();
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
}