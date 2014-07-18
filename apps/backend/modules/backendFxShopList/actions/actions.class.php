<?php
/**
 * Контроллер для работы с элементами словарей (перечней).
 * 
 * @package     backend
 * @subpackage  backendFxShopList
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class backendFxShopListActions extends BaseBackendFxShopListActions
{
  /**
   * Class name for the object
   * 
   * @var string
   */
  protected $objectClassName = 'FxShopList';

  /**
   * Class name of form for create new object
   * 
   * @var string
   */
  protected $formClassNew = 'FxShopListNewNodeForm';

  /**
   * Class name of form for edit object
   * 
   * @var string
   */
  protected $formClassEdit = 'FxShopListEditNodeForm';
}