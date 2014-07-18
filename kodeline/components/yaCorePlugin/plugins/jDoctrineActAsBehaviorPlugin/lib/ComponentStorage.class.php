<?php
/**
 * Components cache storage class.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  lib
 * @category    storage
 * @author      Alexey G. Chugarev
 */
class ComponentStorage
{
  /**
   * Array of the types for models.
   * @var array
   */
  protected $cacher = null;

  /**
   * {@inheritDoc}
   */
  public function __construct($parentKey)
  {
    $this->cacher = new sfFileCache();
    
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    parent::__construct($this->_options);
  }
}