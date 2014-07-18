<?php
/**
 * Extends symfony sfConfig.
 *
 * @package    yaCorePlugin
 * @subpackage lib
 * @category   config
 * @author     chugarev@gmail.com
 * @version    $Id$
 */
class yaConfig extends sfConfig
{
  /**
   * Retrieves a config parameter.
   *
   * @param string $name    A config parameter name
   * @param mixed  $default A default config parameter value
   *
   * @return mixed A config parameter value, if the config parameter exists, otherwise null
   */
  public static function get($name, $default = null)
  {
    $segments = is_array($name) ? $name : explode('/', $name);

    $config =& self::$config;
    foreach ($segments as $segment)
    {
      if (! isset($config[$segment])) return $default;

      if (isset($config[$segment]))
      {
        $config =& $config[$segment];
      }
    }

    return $config;
  }

  /**
   * Indicates whether or not a config parameter exists.
   *
   * @param string $name A config parameter name
   *
   * @return bool true, if the config parameter exists, otherwise false
   */
  public static function has($name)
  {
    $segments = is_array($name) ? $name : explode('/', $name);

    $config =& self::$config;
    foreach ($segments as $segment)
    {
      if (! isset($config[$segment])) return false;

      if (isset($config[$segment]))
      {
        $config =& $config[$segment];
      }
    }

    return true;
  }

  /**
   * Sets a config parameter.
   *
   * If a config parameter with the name already exists the value will be overridden.
   *
   * @param string $name  A config parameter name
   * @param mixed  $value A config parameter value
   */
  public static function set($name, $value)
  {
    $segments = is_array($name) ? $name : explode('/', $name);

    $config =& self::$config;
    foreach ($segments as $segment)
    {
      if (isset($config[$segment]))
      {
        $config =& $config[$segment];
      }
    }

    $config = $value;
  }
}
