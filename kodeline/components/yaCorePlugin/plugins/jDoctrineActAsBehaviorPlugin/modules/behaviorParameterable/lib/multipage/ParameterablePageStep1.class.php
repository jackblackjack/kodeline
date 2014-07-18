<?php
/**
 * Страница выбора типа конкурса.
 * 
 * @package     jDContest
 * @subpackage  multipage
 * @category    module
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
class ParameterablePageStep1 extends BaseParameterableStepsPage
{
  /**
   * {@inheritDoc}
   */
  public function fetch(sfWebRequest $request = null)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->modelName = $request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable'))
      {
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

    // Define form class name.
    $formClassName = 'ParameterableForm';
    $arConfig = sfConfig::get('app_jDoctrineActAsBehaviorPlugin_behaviorParameterable');

    // Define form class name from configuration.
    if (is_array($arConfig) && array_key_exists($this->modelName, $arConfig))
    {
      if (! empty($arConfig[$this->modelName]['forms']) && ! empty($arConfig[$this->modelName]['forms']['new']))
      {
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

    // Set default value for form parameter "belong".
    if ($request->hasParameter('belong'))
    {
      $this->form->setDefault('belong', $request->getParameter('belong'));
    }

    // Check requested method.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
    {
      // Bind request parameters for form.
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      // Validation form.
      if ($this->form->isValid())
      {
        $this->getHandler()->setValues($this->getName(), $this->form->getValues());
        return yaMultipageBase::PAGE_NEXT;
      }
    }

    return sfView::SUCCESS;
  }
}
