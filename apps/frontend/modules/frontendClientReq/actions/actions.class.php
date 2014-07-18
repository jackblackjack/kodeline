<?php
/**
 * Контроллер работы с разделом клиентов.
 * 
 * @package     frontend
 * @subpackage  frontendClient
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class frontendClientActions extends yaBaseActions
{
  /**
   * Главная страница раздела "Услуги".
   * 
   * @param sfRequest $request A request object
   */
  public function executeFaq(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }

  /**
   * Главная страница раздела "Услуги".
   * 
   * @param sfRequest $request A request object
   */
  public function executeArticle(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }
}