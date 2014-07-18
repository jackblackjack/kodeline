<?php
/**
 * Компонент работы с элементами.
 * 
 * @package     jDoctrineElementPlugin
 * @subpackage  backendElement
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseBackendElementComponents extends yaBaseComponents
{
  /**
   * Class name for the object
   * @var string
   */
  protected $objectClassName = 'jElement';

  /**
   * Выборка списка корней деревьев.
   */
  public function executeListRoots()
  {
    try {
      // Define class name.
      $className = (empty($this->className) ? $this->objectClassName : $this->className);

      // Fetch roots of the tree.
      $this->listRoots = Doctrine::getTable($className)->getTree()->fetchRoots();
    }
    // Catch exceptions.
    catch(sfException $exception)
    {
      $this->getUser()->setFlash($exception->getMessage());
    }

    return sfView::SUCCESS;
  }

}