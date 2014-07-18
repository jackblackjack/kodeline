<?php
/**
 * Extends symfony project configuration.
 *
 * @package    yaCorePlugin
 * @subpackage lib.config
 * @author     pinhead
 * @version    SVN: $Id: yaProjectConfiguration.class.php 2592 2010-11-29 06:37:47Z pinhead $
 */
class yaProjectConfiguration extends sfProjectConfiguration
{
  /**
   * Роутер.
   * 
   * @var sfRouting
   * @static
   */
  static protected $routing = null;

  /**
   * Роутер.
   * 
   * @var sfRouting
   * @static
   */
  static protected $mailer = null;

  /**
   * {@inheritDoc}
   */
  public function setup()
  {
    // Call parent function.
    parent::setup();

    // Set timezone by server.
    if (function_exists('date_default_timezone_set')) {
      date_default_timezone_set(sfConfig::get('default_timezone', @date_default_timezone_get()));
    }

    // Set off magic_quotes_gpc.
    ini_set('magic_quotes_gpc', 'off');
    ini_set('short_open_tag', 'off');
    ini_set('register_globals', 'off');
    ini_set('session.auto_start', 'off');

    // Define bundler plugins directory path.
    $sBundledPluginsPath = sfConfig::get('sf_plugins_dir') . 
                            DIRECTORY_SEPARATOR . basename(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'plugins';

    // Define include path
    ini_set('include_path', 
      dirname($sBundledPluginsPath) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'config' . 
      PATH_SEPARATOR . ini_get('include_path')
    );

    require_once(sfConfig::get('sf_plugins_dir') . '/yaCorePlugin/lib/config/klPluginConfiguration.class.php');
    require_once(sfConfig::get('sf_plugins_dir') . '/klCmsEventPlugin/lib/klEventToolkit.class.php');
    //die($sBundledPluginsPath);
    //echo ini_get('include_path'); die;

    // Get all files in specified directory
    //$arBundledPlugins = array_map('basename', glob(sprintf('%s/*', $sBundledPluginsPath), GLOB_NOSORT | GLOB_ONLYDIR));
    $arBundledPlugins = glob(sprintf('%s/*', $sBundledPluginsPath), GLOB_NOSORT | GLOB_ONLYDIR); 

    $szPlugins = count($arBundledPlugins);
    for ($i = 0; $i < $szPlugins; $i++) { $arBundledPlugins[] = basename($arBundledPlugins[$i]); }
    
    // Set paths for bundled plugins.
    $this->setBundledPluginPaths($arBundledPlugins);

    // Enable all plugins.
    $this->enablePlugins(array_merge(array('sfDoctrinePlugin', 'yaCorePlugin'), $arBundledPlugins));
  }

  /**
   * {@inheritDoc}
   */
  public function setupPlugins()
  {
    $arPlugins = $this->getPlugins();

    $arPluginsKeys = array_keys($arPlugins);
    $szPlugins = count($arPluginsKeys);
    for ($i = 0; $i < $szPlugins; $i++)
    {
      $pluginCall = new sfCallable(array($arPlugins[$arPluginsKeys[$i]], 'connectTests'));

      if (is_callable($pluginCall->getCallable())) {
        $pluginCall->call();
      }
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function setBundledPluginPaths($arPlugins)
  {
    // Define base path for bundled plugins.
    $sBundledPluginsPath = sfConfig::get('sf_plugins_dir') . DIRECTORY_SEPARATOR . basename(dirname(dirname(__DIR__))) . DIRECTORY_SEPARATOR . 'plugins' . DIRECTORY_SEPARATOR;
    //array_map(function($pluginPath) use ($sBundledPluginsPath) { $this->setPluginPath($pluginPath, $sBundledPluginsPath . $pluginPath); }, $bundledPlugins);
    
    $szPlugins = count($arPlugins);
    for ($i = 0; $i < $szPlugins; $i++)
    { 
      $this->setPluginPath(basename($arPlugins[$i]), $sBundledPluginsPath . $arPlugins[$i]);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function setRootDir($rootDir)
  {
    $this->rootDir = $rootDir;

    sfConfig::add(array(
      'sf_root_dir' => $rootDir,

      // global directory structure
      'sf_apps_dir'    => $rootDir.DIRECTORY_SEPARATOR.'apps',
      'sf_lib_dir'     => $rootDir.DIRECTORY_SEPARATOR.'kodeline',
      'sf_log_dir'     => $rootDir.DIRECTORY_SEPARATOR.'log',
      'sf_data_dir'    => $rootDir.DIRECTORY_SEPARATOR.'data',
      'sf_config_dir'  => $rootDir.DIRECTORY_SEPARATOR.'config',
      'sf_test_dir'    => $rootDir.DIRECTORY_SEPARATOR.'test',
      'sf_plugins_dir' => $rootDir.DIRECTORY_SEPARATOR.'kodeline'.DIRECTORY_SEPARATOR.'components',
    ));

    $this->setWebDir($rootDir.DIRECTORY_SEPARATOR.'web');
    $this->setCacheDir($rootDir.DIRECTORY_SEPARATOR.'cache');
  }

  /**
   * {@inheritDoc}
   */
  public function initializeDoctrine()
  {
    chdir(sfConfig::get('sf_root_dir'));
 
    $task = new sfDoctrineBuildTask($this->dispatcher, new sfFormatter());
    $task->setConfiguration($this);
    $task->run(array(), array(
      'no-confirmation' => true,
      'db'              => true,
      'model'           => true,
      'forms'           => true,
      'filters'         => true
    ));
  }

  /**
   * {@inheritDoc}
   */
  public function configureDoctrineConnection(Doctrine_Connection $connection)
  {
  }

  /**
   * {@inheritDoc}
   */
  public function configureDoctrineConnectionDoctrine2(Doctrine_Connection $connection)
  {
    $connection->setAttribute(Doctrine_Core::ATTR_VALIDATE, false);
  }

  /**
   * {@inheritDoc}
   */
  public function configureDoctrine(Doctrine_Manager $manager)
  {
    // Setup debug mode for ORM.
    Doctrine_Core::debug(sfConfig::get('app_ya_core_plugin_orm_debug', null));

    // Set up doctrine extensions dir.
    Doctrine_Core::setExtensionsPath(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'extension');

    // Configure inheritance
    $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CLASS, 'myDoctrineQuery');
    $manager->setAttribute(Doctrine_Core::ATTR_COLLECTION_CLASS, 'myDoctrineCollection');
    $manager->setAttribute(Doctrine_Core::ATTR_TABLE_CLASS, 'myDoctrineTable');

    // Configure hydrators.
    $manager->registerHydrator('yaFlat', 'Doctrine_Hydrator_yaFlatDriver');

    // This will allow us to use "mutators"
    $manager->setAttribute(Doctrine::ATTR_AUTO_ACCESSOR_OVERRIDE, true);

    // This will allow sql callbacks for behavours.
    $manager->setAttribute(Doctrine_Core::ATTR_USE_DQL_CALLBACKS, true);

    // Configure method of the model's loading.
    $manager->setAttribute(Doctrine_Core::ATTR_MODEL_LOADING, Doctrine_Core::MODEL_LOADING_CONSERVATIVE);

    // Configure memmory strategy.
    $manager->setAttribute(Doctrine_Core::ATTR_AUTOLOAD_TABLE_CLASSES, true);
    $manager->setAttribute(Doctrine_Core::ATTR_AUTO_FREE_QUERY_OBJECTS, true);

    // Support extended classes for preparing events of the fields and records.
    require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'EventListener.class.php');
    require_once(dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'record' . DIRECTORY_SEPARATOR . 'yaDoctrineRecordListener.class.php');

    // Set default listeners.
    $manager->setAttribute(Doctrine_Core::ATTR_LISTENER, new yaDoctrine_EventListener());
    $manager->setAttribute(Doctrine_Core::ATTR_RECORD_LISTENER, new yaDoctrine_Record_Listener());

    // This sets all table columns to notnull and unsigned (for ints) by default.
    $manager->setAttribute(Doctrine::ATTR_DEFAULT_COLUMN_OPTIONS, array('notnull' => true, 'unsigned' => true));

    // Set the default primary key to be named 'id', integer, 20 bytes as default MySQL bigint.
    $manager->setAttribute(Doctrine::ATTR_DEFAULT_IDENTIFIER_OPTIONS, array('name' => 'id', 'type' => 'integer', 'length' => 20));

    // Configure model builder.
    sfConfig::set('doctrine_model_builder_options', array('generateTableClasses' => true, 'baseClassName' => 'myDoctrineRecord', 'suffix' => '.class.php'));
  }

  /**
   * {@inheritDoc}
   */
  protected function configureDoctrineCache(Doctrine_Manager $manager)
  {
    // Fetch configuration.
    $config = sfConfig::get('app_ya_core_plugin_orm_cache', false);

    if (! $config || ! isset($config['enabled']) || ! $config['enabled']) {
      return false;
    }

    // Define driver.
    $driver = null;

    // If set specific cache class.
    if (isset($config['class']))
    {
      $arCacheParams = ((! isset($config['param'])) ? array() : $config['param']);
      $cacheClassname = $config['class'];
      $driver = new $cacheClassname($arCacheParams);
    }
    // If apc plugin loaded cache queries by apc.
    else if (extension_loaded('apc'))
    {
      $driver = new Doctrine_Cache_Apc(array('prefix' => yaProject::getNormalizedRootDir() . '/doctrine/'));
    }
    // If memcached plugin loaded cache queries by apc.
    else if (extension_loaded('memcache') && sfConfig::get('app_database_memcache_enabled', false))
    {
      $driver = new Doctrine_Cache_Memcache(array(
        'prefix'      => yaProject::getNormalizedRootDir() . '/doctrine/',
        'compression' => sfConfig::get('app_database_memcache_compression', false),
        'servers'     => sfConfig::get('app_database_memcache_servers', array(
                          'host' => 'localhost',
                          'port' => 11211,
                          'persistent' => false
                         ))
      ));
    }

    if (! is_null($driver))
    {
      // Set cache driver.
      $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE, $driver);
      $manager->setAttribute(Doctrine_Core::ATTR_QUERY_CACHE_LIFESPAN, (isset($config['query_lifespan']) ? $config['query_lifespan'] : 86400));

      // Set result cache options.
      if (isset($config['cache_result_enabled']) && $config['cache_result_enabled'])
      {
        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE, $driver);
        $manager->setAttribute(Doctrine_Core::ATTR_RESULT_CACHE_LIFESPAN, (isset($config['result_lifespan']) ? $config['result_lifespan'] : 86400));
      }
    }
  }

  /**
   * Возвращает класс роутинга для активной конфигурации.
   */
  static public function getRouting()
  {
    if (null !== self::$routing)
    {
      return self::$routing;
    }
 
    // If sfContext has an instance, returns the loaded routing resource
    if (sfContext::hasInstance() && sfContext::getInstance()->getRouting())
    {
      self::$routing = sfContext::getInstance()->getRouting();
    }
    else
    {
      // Initialization
      if (!self::hasActive())
      {
        throw new sfException('No sfApplicationConfiguration loaded');
      }

      $appConfig = self::getActive();
      $config = sfFactoryConfigHandler::getConfiguration($appConfig->getConfigPaths('config/factories.yml'));
      $params = array_merge(
        $config['routing']['param'],
        array(
          'load_configuration' => false,
          'logging'            => false,
          'context'            => array(
            'host'      => sfConfig::get('app_host',   'localhost'),
            'prefix'    => sfConfig::get('app_prefix', sfConfig::get('sf_no_script_name') ? '' : '/' . $appConfig->getApplication() . '_' . $appConfig->getEnvironment() . '.php'),
            'is_secure' => sfConfig::get('app_host',   false)
          ),
        )
      );

      $handler = new sfRoutingConfigHandler();
      $routes = $handler->evaluate($appConfig->getConfigPaths('config/routing.yml'));
      $routeClass = $config['routing']['class'];
      self::$routing = new $routeClass($appConfig->getEventDispatcher(), null, $params);
      self::$routing->setRoutes($routes);
    }
 
    return self::$routing;
  }

  /**
   * Returns the project mailer
   */
  static public function getMailer()
  {
    if (null !== self::$mailer)
    {
      return self::$mailer;
    }
 
    // If sfContext has instance, returns the classic mailer resource
    if (sfContext::hasInstance() && sfContext::getInstance()->getMailer())
    {
      self::$mailer = sfContext::getInstance()->getMailer();
    }
    else
    {
      // Else, initialization
      if (!self::hasActive())
      {
        throw new sfException('No sfApplicationConfiguration loaded');
      }
      require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/classes/Swift.php';
      Swift::registerAutoload();
      sfMailer::initialize();
      $applicationConfiguration = self::getActive();
 
      $config = sfFactoryConfigHandler::getConfiguration($applicationConfiguration->getConfigPaths('config/factories.yml'));
 
      self::$mailer = new $config['mailer']['class']($applicationConfiguration->getEventDispatcher(), $config['mailer']['param']);
    }
 
    return self::$mailer;
  }
}