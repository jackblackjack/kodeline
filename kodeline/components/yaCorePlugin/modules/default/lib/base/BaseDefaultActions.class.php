<?php
/**
 * Base actions for default module.
 * 
 * @package     yaCorePlugin
 * @subpackage  default
 * @category    actions
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseDefaultActions extends yaBaseActions
{
 /**
  * Executes index action
  * 
  * @param sfRequest $request A request object
  */
  public function executeIndex(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

  /**
   * Error page for page not found (404) error
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeError404(sfWebRequest $request)
  {
    $response = $this->getResponse();

    // HTTP headers
    $response->setHttpHeader('Content-Language', sfContext::getInstance()->getUser()->getCulture());
    $response->setStatusCode(404);
    $response->addCacheControlHttpHeader('no-cache');

    // Metas and page headers
    $response->addMeta('robots', 'NONE');
    
    return sfView::SUCCESS;
  }

  /**
   * Warning page for restricted area - requires login
   *
   * @param sfRequest $request A request object
   */
  public function executeSecure(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

  /**
   * Warning page for restricted area - requires credentials
   *
   * @param sfRequest $request A request object
   */
  public function executeLogin(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

  /**
   * Module disabled in settings.yml
   *
   * @param sfRequest $request A request object
   */
  public function executeDisabled(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

 /**
  * Client browser is not supported javascript cookie's.
  * 
  * @param sfRequest $request A request object
  */
  public function executeBadbrowser(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }
}