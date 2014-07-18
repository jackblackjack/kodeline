<?php
/**
 * End step for congrats.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    multipage
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterablePageLast extends BaseParameterableStepsPage
{
  /**
   * {@inheritDoc}
   */
  public function fetch(sfWebRequest $request = null)
  {
    try {
      // Check exists name of the component.
      if (null === ($this->modelName = $request->getParameter('model', null))) {
        throw new sfException($this->getContext()->getI18N()->__('Наименование компонента не было указано!', null, 'behavior-parameterable'));
      }

      // Check if model has support behavior Parameterable.
      if (! Doctrine::getTable($this->modelName)->hasTemplate('Parameterable')) {
        throw new sfException(sprintf($this->getContext()->getI18N()->__('Модель "%s" не поддерживает расширение Parameterable', null, 'behavior-parameterable'), $this->modelName));
      }
    }
    // Catch any exceptions.
    catch(Exception $exception)
    {
      $this->getUser()->setFlash('error', $exception->getMessage());
      return sfView::ERROR;
    }

    // Fetch previous values kdkdkdkdk
    $arPrevStepValues = $this->getHandler()->getValues(self::STEP1);

    // Define form class by type.
    $formClassName = 'ParameterableParam' . sfInflector::camelize($arPrevStepValues['type']) . 'Form';

    // Initiate form object.
    $this->form = new $formClassName(array(), array('title' => $this->getHandler()->getValue(self::STEP1, 'title')));

    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // If parent id is setted - set to form and
    // fetch parent object.
    if (! empty($this->parent_id))
    {
      $this->form->setDefault('parent_id', $this->parent_id);
      $this->parent = Doctrine::getTable($this->modelName)->createQuery()->where('id = ?', $this->parent_id)->fetchOne();
    }

    // Check requested method.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
    {
      // Bind request parameters for form.
      $this->form->bind($request->getParameter($this->form->getName()), $request->getFiles($this->form->getName()));

      // Validation form.
      if ($this->form->isValid())
      {
        // Save form fields values.
        $this->getHandler()->setValues($this->getName(), $this->form->getValues());

        // Define form class for create parameter.
        $callMethodName = 'create' . sfInflector::camelize($arPrevStepValues['type']) . 'Parameter';

        try {
          // Check create parameter method exists.
          if (! method_exists('ParameterableToolkit', $callMethodName)) {
            throw new sfException(sprintf(sfContext::getInstance()
              ->getI18N()->__('Метод "%s" не найден!', null, 'behavior-parameterable'), $callMethodName));
          }

          // Call method for create parameter.
          call_user_func_array(array('ParameterableToolkit', $callMethodName), array($this->modelName, $this->getHandler()->getMergedValues()));

          // Return successfully result.
          return yaMultipageBase::PAGE_LAST;
        }
        // Catch any exceptions.
        catch(Exception $exception)
        {
          $this->getUser()->setFlash('error', $exception->getMessage());
          return sfView::ERROR;
        }
      }
    }

    /*
    if (false && $bCreated && is_object($record))
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
    */

    return sfView::SUCCESS;
  }
}
