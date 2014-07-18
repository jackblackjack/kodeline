<?php

/**
 * Adds OOP-aware helpers.
 *
 * @package     yaCorePLugin
 * @subpackage  lib.helper
 * @author      pinhead
 * @version     SVN: $Id: yaHelperBroker.class.php 2756 2010-12-15 22:50:26Z pinhead $
 */
class yaHelperBroker
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
   * Retrieve helpers holder
   *
   * @return sfParameterHolder
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
   * Retrieves helper by name
   *
   * @param string $name Helper alias name
   *
   * @return mixed Helper class instance
   *
   * @throws sfViewException
   */
  public function getHelper($name)
  {
    $name = sfConfig::get('app_helpers_'.$name, $name);
    $helpers = $this->getHelperHolder();

    if (! $helpers->has($name))
    {
      //yaApplicationConfiguration::getActive()->loadHelpers($name, yaContext::getInstance()->getModuleName());

      $class = $name . 'Helper';
      if (!class_exists($class, true))
      {
        throw new sfViewException(sprintf('Could not load helper %s. Class %s not found.', $name, $class));
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
   * Magic getter to allow acces like $brokerInstance->helperName
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
   * Magic method to allow call a default direct() method in the helper
   *
   * @param string  $method
   * @param array   $args
   *
   * @return mixed
   *
   * @throws sfViewException
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