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
class ParameterablePageStep2 extends BaseParameterableStepsPage
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

    // Fetch previous values.
    $arPrevStepValues = $this->getHandler()->getValues(self::STEP1);

    // Define form class by type.
    $formClassName = 'ParameterableParam' . sfInflector::camelize($arPrevStepValues['type']) . 'Form';

    // Log classname for add field.
    if ('dev' == sfConfig::get('sf_environment'))
    {
      if (sfContext::hasInstance() && sfContext::getInstance())
      {
        sfContext::getInstance()->getEventDispatcher()->notify(new sfEvent($this, 'application.log', array(
          sprintf('Parameterable step "%s": classname for field "%s"', self::STEP2, $formClassName), 'priority' => sfLogger::INFO)));
      }
    }

    // Initiate form object.
    $this->form = new $formClassName(array(), array('title' => $this->getHandler()->getValue(self::STEP1, 'title')));

    // Check parent_id for new the node.
    $this->parent_id = $request->getParameter('parent_id', null);

    // If parent id is setted - set to form and fetch parent object.
    if (! empty($this->parent_id))
    {
      $this->form->setDefault('parent_id', $this->parent_id);
    }

    // Check requested method.
    if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
    {
      // Bind request parameters for form.
      $this->form->bind(
        $request->getParameter($this->form->getName()),
        $request->getFiles($this->form->getName())
      );

      // Validation form.
      if ($this->form->isValid())
      {
        // Save form fields values.
        $this->getHandler()->setValues($this->getName(), $this->form->getValues());

        // Move to next page.
        return yaMultipageBase::PAGE_NEXT;
      }
    }

    // Return success.
    return sfView::SUCCESS;
  }
}
