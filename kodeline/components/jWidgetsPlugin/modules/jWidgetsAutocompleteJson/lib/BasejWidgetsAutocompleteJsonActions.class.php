<?php
/**
 * Base actions for the sfDoctrineFBAutocompletePlugin sfDoctrineFBAutocompleteJson module.
 * 
 * @package     sfDoctrineFBAutocompletePlugin
 * @subpackage  sfDoctrineFBAutocompleteJson
 * @author      GSschurgast
 * @version     SVN: $Id: BaseActions.class.php 12534 2008-11-01 13:38:27Z Kris.Wallsmith $
 */
abstract class BasejWidgetsAutocompleteJsonActions extends yaBaseActions
{
  /**
   * {@inheritDoc}
   */
  public function preExecute()
  {
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);

    $this->getRequest()->setParameter('sf_format','json');
    $this->getResponse()->setContentType('application/json; charset=utf-8');
  }

  /**
   * Default action.
   * @param sfWebRequest $request
   */
  public function executeList(sfWebRequest $request)
  {
    // Check named method.
    if (strlen($this->getRequest()->getParameter('name', null)))
    {
      // Definition action name for forwading.
      $actionName = sfInflector::camelize(strtolower($request->getParameter('name')));

      // If named action is exists -
      if ($this->getController()->actionExists($this->getContext()->getModuleName(), $actionName))
      {
        // forward to action.
        return $this->forward($this->getContext()->getModuleName(), $actionName);
      }
    }
    
    try {
      if (! strlen($request->getParameter('model', null)))
      {
        throw new sfException($this->getContext()->getI18N()->__('Model is not defined', null, 'suggest-fb'));
      }

      $this->items = Doctrine_Core::getTable($request->getParameter('model'))->findAll();

      return sfView::SUCCESS;
    }
    // Catch unknown model.
    catch(Doctrine_Record_UnknownPropertyException $exception)
    {
      return $this->renderJsonError('Unknown model');
    }
    // Catch default exception.
    catch(sfException $exception)
    {
      return $this->renderJsonError($exception->getMessage());
    }
    
    return sfView::NONE;
  }
}
