<?php
/**
 * Provides convenient methods to start symfony application.
 *
 * @package    kodeline
 * @subpackage yaCorePlugin
 * @category   base
 * @author     kodeline
 * @version    $Id$
 $
 */

// Define version constant.
define('YA_CORE_VERSION', '1.0.0');

// Try to include symfony.
if (! defined('SYMFONY_VERSION'))
{
  // Load kodeline autoloader.
  require_once(__DIR__ . DIRECTORY_SEPARATOR . 'klCoreAutoload.class.php');
  klCoreAutoload::register();
}

class yaBase
{
  protected static $startTime;

  protected static $version;

  protected static $dir;

  public static function start($dir = null)
  {
    if (null !== self::$dir)
    {
      throw new Exception('Engine core has already been started');
    }

    self::resetStartTime();

    self::$version = YA_CORE_VERSION;

    self::$dir = null === $dir ? realpath(dirname(__FILE__).'/../..') : $dir;

    require_once(self::$dir.'/lib/config/yaProjectConfiguration.class.php');
  }

  public static function startApp()
  {
    require_once(self::$dir.'/lib/config/yaApplicationConfiguration.class.php');
  }

  public static function resetStartTime()
  {
    self::$startTime = microtime(true);
  }

  public static function getDir()
  {
    return self::$dir;
  }

  public static function getStartTime()
  {
    return self::$startTime;
  }

  /**
   * Loads the Swift mailer
   */
  public static function enableMailer()
  {
    if (! class_exists('Swift_Message'))
    {
      Swift::registerAutoload();
      sfMailer::initialize();
    }
  }

  public static function setVersion($version)
  {
    self::$version = $version;
  }

  public static function getVersion()
  {
    return self::$version;
  }

  public static function getVersionMajor()
  {
    $parts = explode('.', self::getVersion());
    return $parts[0];
  }

  public static function getVersionMinor()
  {
    $parts = explode('.', self::getVersion());
    return $parts[1];
  }

  public static function getVersionMaintenance()
  {
    $parts = explode('.', self::getVersion());
    return $parts[2];
  }

  public static function getVersionBranch()
  {
    $parts = explode('.', self::getVersion());
    return $parts[0].'.'.$parts[1];
  }

  /**
   * All context creations are made here.
   * You can replace here the yaContext class by your own.
   *
   * @return yaContext
   */
  public static function createContext(sfApplicationConfiguration $configuration, $name = null, $class = 'yaContext')
  {
    return yaContext::createInstance($configuration, $name, $class);
  }

  /**
   * Symfony common objects accessors
   */
  public static function getRouting()
  {
    return yaContext::getInstance()->getRouting();
  }

  public static function getRequest()
  {
    return yaContext::getInstance()->getRequest();
  }

  public static function getResponse()
  {
    return yaContext::getInstance()->getResponse();
  }

  public static function getController()
  {
    return yaContext::getInstance()->getController();
  }

  /**
   */
  public static function getConfiguration()
  {
    /*
$configuration = ProjectConfiguration::getApplicationConfiguration('frontend', 'prod', false);
sfContext::createInstance($configuration)->dispatch();
*/
    $sEnvironment = (strlen(sfConfig::get('sf_environment')) ? sfConfig::get('sf_environment') : 'dev');
    return yaContext::hasInstance() ? 
            yaContext::getInstance()->getConfiguration() : 
            ProjectConfiguration::getActive()->sfApplicationConfiguration((strlen(sfConfig::get('sf_app')) ? sfConfig::get('sf_app') : 'frontend'), $sEnvironment, ('dev' == $sEnvironment));
  }

  public static function getEventDispatcher()
  {
    return yaContext::hasInstance() ? yaContext::getInstance()->getEventDispatcher() : ProjectConfiguration::getActive()->getEventDispatcher();
  }

  public static function getUser()
  {
    return yaContext::getInstance()->getUser();
  }

  public static function getI18n()
  {
    return yaContext::getInstance()->getI18n();
  }

  public static function loadHelpers($helpers)
  {
    return yaContext::getInstance()->getConfiguration()->loadHelpers($helpers);
  }

}
