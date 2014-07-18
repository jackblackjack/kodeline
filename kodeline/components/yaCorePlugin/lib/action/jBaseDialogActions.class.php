<?php
/**
 * Контроллер диалоговых окон, 
 * запрашиваемых посредством ajax-вызовов.
 *
 * @package    frontend
 * @author     chugarev
 * @version    $Id$
 */
abstract class jBaseDialogActions extends yaBaseActions
{
  /**
   * {@inheritDoc}
   */
  public function preExecute()
  {
    if ($this->getContext()->getRequest()->isXmlHttpRequest())
    {
      $this->setLayout(false);
      sfConfig::set('sf_web_debug', false);
      $this->getResponse()->setContentType('application/xhtml+xml; charset=utf-8');
      //$this->getResponse()->setContentType('application/json; charset=utf-8');
    }
  }

  /**
   * {@inheritDoc}
   */
  public function postExecute()
  {
    return true;

    // Check for required action name.
    if (null === ($actionName = $this->getRequest()->getParameter('name', null)))
    {
      throw new sfError404Exception('Dialog action is not found!');
    }

    $this->setTemplateByAction($actionName);
  }

  /**
   * getTemplateName()
   * @param string $name Optional. Use custom name instead of current page name.
   * @return string
   */
  protected function setTemplateByAction($name = null)
  {
    if (!is_null($this->template)) {
      return $this->template;
    }

    $template     = sfInflector::camelize($this->getActionName() . ($name ? ucfirst($name) : null));
    $template[0]  = strtolower($template[0]);
    $this->setTemplate($template);
  }

  /**
   * Action for ajax request forwading of this controller.
   * @param sfWebRequest $request
   */
  public function executeDo(sfWebRequest $request)
  {
    try {
      // Check for required action dialog.
      if (null === ($dlgName = $request->getParameter('dlg', null))) {
        throw new sfStopException($this->getContext()->getI18N()->__('Dialog name must be set!', null, 'dialog'));
      }

      // Definition method name for forwading.
      $methodName = sprintf('%s%s', 
        (($this->getContext()->getRequest()->isXmlHttpRequest() ? 'Xhr' : null) . ucfirst($dlgName)),
        ucfirst($this->getRequest()->getParameter('name', 'default')));

      // Definition method for action do.
      $foreignAction = new sfCallable(array($this, 'execute' . $methodName));

      // Check foreign action callable.
      if (! is_callable($foreignAction->getCallable()))
      {
        throw new sfStopException(sprintf(
          $this->getContext()->getI18N()->__('Action "%s" for dialog "%s" is not found!', null, 'dialog'),
          $methodName, $dlgName));
      }
    }
    // Catch exceptions.
    catch (sfException $exception)
    {
      // Write error to log.
      $this->logMessage($exception->getMessage(), 'err');

      // Set error message for user.
      if ('dev' == sfConfig::get('sf_environment'))
      {
        $this->getUser()->setFlash('error', $exception->getMessage());
      }
      else {
        $this->getUser()->setFlash('error', $this->getContext()->getI18N()->__('Error while open dialog', null, 'dialog')); 
      }

      return sfView::ERROR;
    }

    // Forward to foreign action.
    $this->forward($this->getContext()->getModuleName(), $methodName);

    return sfView::NONE;
  }
}