<?php
/**
 * Контроллер работы с разделом услуг компании.
 * 
 * @package     frontend
 * @subpackage  frontendService
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class frontendServiceActions extends yaBaseActions
{
  /**
   * Главная страница раздела "Услуги".
   * 
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    return sfView::SUCCESS;
  }
}