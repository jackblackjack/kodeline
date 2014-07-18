<?php
/**
 * Base components for default module.
 * 
 * @package     yaCorePlugin
 * @subpackage  default
 * @category    components
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseDefaultComponents extends yaBaseComponents
{
  /**
   * Component for check javascript enabled.
   */
  public function executeJavascriptCheck()
  {
    // Define redirect page if javascript is not enabled.
    if (! $this->redirect_page) {
      $this->redirect_page = sfConfig::get('app_noscript_redirect', '/default/badbrowser/');
    }

    return sfView::SUCCESS;
  }
}