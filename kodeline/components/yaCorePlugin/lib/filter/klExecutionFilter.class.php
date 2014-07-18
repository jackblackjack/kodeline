<?php
/**
 * This filter processes autocall methods by execution.
 *
 * @package     yaCorePlugin
 * @subpackage  filter
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klExecutionFilter extends sfExecutionFilter
{
  /**
   * {@inhetritDoc}
   */
  protected function executeAction($actionInstance)
  {
    // execute the action
    $actionInstance->preExecute();
    $viewName = $actionInstance->execute($this->context->getRequest());
    $actionInstance->postExecute();

    // Define current view name.
    $currentView = (null === $viewName ? sfView::SUCCESS : $viewName);

    if (sfView::NONE === $currentView)
    {
      // Define method name for redirect url.
      $urlMethodName = 'getUrlAfter' . sfInflector::camelize($actionInstance->getActionName()) . sfInflector::camelize($currentView);

      if (method_exists($actionInstance, $urlMethodName))
      {
        $redirectUrl = call_user_func(array($actionInstance, $urlMethodName));

        if (null !== $redirectUrl) {
          $actionInstance->redirect($redirectUrl);
        }
      }
    }

    // Define method name for flashes.
    $flashMethodName = 'setFlashAfter' . sfInflector::camelize($actionInstance->getActionName()) . sfInflector::camelize($currentView);

    if (method_exists($actionInstance, $flashMethodName))
    {
      call_user_func(array($actionInstance, $flashMethodName));
    }

    return $currentView;
  }
}
