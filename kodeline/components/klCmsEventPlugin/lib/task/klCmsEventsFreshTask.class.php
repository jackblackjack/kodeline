<?php
/**
 * Kodeline cms events fresher.
 * 
 * @package     klCmsEventPlugin
 * @category    toolkit
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klCmsEventsFreshTask extends sfDoctrineBaseTask
{
  /**
   * Список типов рейтинга.
   * 
   * @static
   * @var array
   */
  protected static $arRateTypes = array();
      
  /**
   * @inheritDoc
   * @see sfTask
   */
  protected function configure()
  {
    // Register arguments
    $this->addArguments(array(
      new sfCommandArgument('culture', sfCommandArgument::OPTIONAL, 'Required culture', 'en'),
      new sfCommandArgument('application', sfCommandArgument::OPTIONAL, 'The application name', 'frontend')
    ));

    // Add options.
    $this->addOptions(array(
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev')
    ));

    $this->aliases = array('kl-eventsfresh');
    $this->namespace = 'kl-events';
    $this->name = 'fresh';
    $this->briefDescription = 'Checks and refresh all kodeline plugins events';

    $this->detailedDescription = <<<EOF
    The [kl-events:fresh|INFO] Checks and refresh all kodeline plugins events):
    [./symfony kl-events:fresh|INFO]
EOF;
  }
  
  /**
   * {@inheritDoc}
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {      
    // Database initialize.
    $databaseManager = new sfDatabaseManager($this->configuration);
    $con = Doctrine_Manager::getInstance()->getCurrentConnection();

    // Prepare paths plugins.
    foreach ($this->configuration->getAllPluginPaths() as $pluginName => $pluginPath)
    {
      // Define path to events list of plugin.
      $sEventsFilePath = $pluginPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'subscribers.yml';

      // Check file is exists.
      if (! is_file($sEventsFilePath)) continue;

      // Check file is readable.
      if (! is_readable($sEventsFilePath))
      {
        $this->logSection('error', sprintf('Events file is not readable: "%s"', $sEventsFilePath));
        continue;
      }

      // Parse events list file.
      $arEvents = sfYaml::load($sEventsFilePath);

      // Preparing events register.
      $this->pluginEventsRegister($arEvents, $pluginName);
    }

    return true;
  }

  /**
   * Register events for the plugin.
   * 
   * @param array $arEvents Events list
   * @param string $pluginName Name of preparing plugin.
   */
  private function pluginEventsRegister($arEvents, $pluginName)
  {
    foreach ($arEvents as $eventName => $eventConfiguration)
    {
      // Log about event name and plugin name.
      $this->logSection("info", sprintf("Prepare event %s for plugin %s", $eventName, $pluginName));

      klEventToolkit::eventRegister($eventName, $eventConfiguration);
    }
  }
}