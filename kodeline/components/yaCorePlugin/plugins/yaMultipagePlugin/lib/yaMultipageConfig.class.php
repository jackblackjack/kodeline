<?php
/**
 * Wrapper of configuration for yaMultipagePlugin.
 *
 * @package     yaMultipagePlugin
 * @subpackage  multipage
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class yaMultipageConfig
{
  /**
   * Prefix of configuration multipage.
   * @var string
   * @static
   */
  protected static $pluginPrefix = 'app_ya_multipage_plugin';

  protected static function hasConfiguration($configuration)
  {
    return sfConfig::has(sprintf('%s_%s', self::$pluginPrefix, $configuration));
  }

  /**
   * Prepare
   */
  protected static function getConfiguration($configuration, $default = null)
  {
    return sfConfig::get(sprintf('%s_%s', self::$pluginPrefix, $configuration), $default);
  }

  /**
   * Retrieves a config parameter.
   *
   * @param string $name    A config parameter name
   * @param mixed  $default A default config parameter value
   * @return mixed A config parameter value, if the config parameter exists, otherwise null
   */
  public static function get($name, $default = null)
  {
    $arPath = explode('/', $name);
    $cfgName = array_shift($arPath);

    return self::getValue($arPath, self::getConfiguration($cfgName, array()));
  }

  public static function getValue($path, $arrayToAccess)
  {
    $szPath = count($path);
    return (1 < $szPath ? self::getValue(array_slice($path, 1), $arrayToAccess[$path[0]]) : (0 == $szPath ? $arrayToAccess : $arrayToAccess[$path[0]]));
  }

  /**
   * Indicates whether or not a config parameter exists.
   *
   * @param string $name A config parameter name
   * @return bool true, if the config parameter exists, otherwise false
   */
  public static function has($name)
  {
    $arPath = explode('/', $name);
    $cfgName = array_shift($arPath);

    return self::hasValue($arPath, self::getConfiguration($cfgName, array()));
  }

  public static function hasValue($path, $arrayToAccess)
  {
    $szPath = count($path);
    return (1 < $szPath ? self::hasValue(array_slice($path, 1), $arrayToAccess[$path[0]]) : (0 == $szPath ? count($arrayToAccess) : array_key_exists($path[0], $arrayToAccess)));
  }
}