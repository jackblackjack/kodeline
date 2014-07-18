<?php

/**
 * yaHelperBroker class.
 *
 * @package     yatutu
 * @subpackage  lib.helper
 * @author      gen
 * @version     SVN: $Id: yaHelperBroker.class.php 693 2009-08-26 12:52:43Z pinhead $
 */
class jHelperBroker
{
  /**
   * yaHelperBroker instance
   *
   * @var yaHelperBroker
   */
  protected static $instance = null;
  
  /**
   * The sfParameterHolder object that stores the helpers.
   *
   * @var sfParameterHolder
   */
  protected $helperHolder = null;
  
  /**
   * Private for singleton pattern
   */
  private function __construct()
  {
  }
  
  /**
   * Private for singleton pattern
   */
  private function __clone()
  {
  }
  
  /**
   * Retrieve singleton instance
   *
   * @return yaHelperBroker
   */
  public static function getInstance()
  {
    if (null === self::$instance)
    {
      self::$instance = new self();
    }

    return self::$instance;
  }

  /**
   * Gets the sfParameterHolder object that stores the helpers.
   *
   * @return sfParameterHolder The helpers' holder.
   */
  public function getHelperHolder()
  {
    if (null === $this->helperHolder)
    {
      $this->helperHolder = new sfParameterHolder();
    }
    
    return $this->helperHolder;
  }

  /**
   * Get helper by name
   *
   * @param  string $name Helper alias name
   *
   * @return mixed Helper class instance
   *
   * @throws sfViewException
   */
  public function getHelper($name)
  {
    $name = sfConfig::get('app_helpers_'.$name, $name);
    $helpers = $this->getHelperHolder();

    if (!$helpers->has($name))
    {
      sfApplicationConfiguration::getActive()->loadHelpers($name, sfContext::getInstance()->getModuleName());

      $class = $name.'Helper';
      if (!class_exists($class, false))
      {
        $error = 'File "%sHelper.php" was loaded but class named "%s" was not found within it.';
        throw new sfViewException(sprintf($error, $name, $class));
      }

      $helper = new $class();
      if (method_exists($helper, 'setHelperBroker'))
      {
        $helper->setHelperBroker($this);
      }

      $helpers->set($name, $helper);
    }

    return $helpers->get($name);
  }
  
  /**
   * Magic getter to allow acces like $entry->helperName to call $entry->getHelper(helperName)
   *
   * @param string $name
   *
   * @return mixed
   */
  public function __get($name)
  {
    return $this->getHelper($name);
  }
  
  /**
   * Magic method to allow call a default direct() method in the helper.
   *
   * @param  string $method
   * @param  array $args
   *
   * @return mixed
   *
   * @throws sfViewException If helper does not have a direct() method
   */
  public function __call($method, $args)
  {
    $helper = $this->getHelper($method);
    if (method_exists($helper, 'direct'))
    {
      return call_user_func_array(array($helper, 'direct'), $args);
    }

    $error = 'Helper "%s" does not support overloading via direct().';
    throw new sfViewException(sprintf($error, $method));
  }
}