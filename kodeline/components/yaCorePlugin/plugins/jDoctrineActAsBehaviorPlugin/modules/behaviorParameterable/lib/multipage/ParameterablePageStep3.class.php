<?php
/**
 * Step to sets options for extended parameter.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    multipage
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterablePageStep3 extends BaseParameterableStepsPage
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

    // Define form class by type.
    $formClassName = 'ParameterableParamOptions' . sfInflector::camelize($this->getHandler()->getValue(self::STEP1, 'type')) . 'Form';

    // If form to set additional 
    // parameters has not exists - create parameter and going to last page.
    if (! class_exists($formClassName))
    {
      // Call create extended parameter method.
      ParameterableToolkit::createExtendedParameter($this->modelName, $this->getHandler()->getMergedValues());

      return yaMultipageBase::PAGE_NEXT;
    }

    // Initiate form object.
    $this->form = new $formClassName(array(), array('title' => $this->getHandler()->getValue(self::STEP2, 'title')));

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

        // Call create extended parameter method.
        ParameterableToolkit::createExtendedParameter($this->modelName, $this->getHandler()->getMergedValues());

        return yaMultipageBase::PAGE_NEXT;
      }
    }

    return sfView::SUCCESS;
  }
}
