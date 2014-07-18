<?php
/**
 * Контроллер работы с элементами товаров.
 * 
 * @package     backend
 * @subpackage  backendFxShopItem
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class backendFxShopItemActions extends BaseBackendFxShopItemActions
{
  /**
   * Class name for the object
   * 
   * @var string
   */
  protected $objectClassName = 'FxShopItem';

  /**
   * Class name of form for create new object
   * 
   * @var string
   */
  protected $formClassNew = 'FxShopItemNewNodeForm';

  /**
   * Class name of form for edit object
   * 
   * @var string
   */
  protected $formClassEdit = 'FxShopItemEditNodeForm';
}