<?php
/**
 * Компонент для работы с категориями товаров.
 * 
 * @package     yaFlexibleShopPlugin
 * @subpackage  backendFShopCategory
 * @category    component
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBackendFxShopItemComponents extends BaseBackendElementComponents
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'FxShopItem';

  /**
   * Выводит список корней деревьев из каталога.
   */
  public function executeShowBackendMenu()
  {
    try {
      // Define component name.
      $this->className = (empty($this->className) ? $this->objectClassName : $this->className);

      // Fetch tree categories by 2 level.
      $this->roots = Doctrine::getTable($this->className)->getTree()->fetchRoots();
    }
    // Catch exceptions.
    catch(sfException $exception)
    {
      $this->getUser()->setFlash($exception->getMessage());
    }

    return sfView::SUCCESS;
  }

  /**
   * Выводит список дочерних элементов объекта.
   */
  public function executeShowBackendMenuChildren()
  {
    try {
      // Define component name.
      $this->className = (empty($this->className) ? $this->objectClassName : $this->className);

      // Define parent node id.
      $this->pid = (empty($this->pid) ? null : $this->pid);

      // Fetch children of node.
      if (null !== $this->pid)
      {
        $query = Doctrine::getTable($this->className)->createQuery()
                            ->where('id = ?', $this->pid)
                            ->andWhere('is_category = ?', 1)
                            ->fetchOne()->getNode()->getChildren(true)
                            ->andWhere('is_category = ?', 1);

        $this->children = $query->fetchArray();
      }
    }
    // Catch exceptions.
    catch(sfException $exception)
    {
      $this->getUser()->setFlash($exception->getMessage());
    }

    return sfView::SUCCESS;
  } 
}