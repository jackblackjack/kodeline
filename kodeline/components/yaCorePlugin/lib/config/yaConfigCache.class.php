<?php

require_once dirname(__FILE__) . '/yaFactoryConfigHandler.class.php';

/**
 * Extends symfony sfConfigCache.
 *
 * @package    yaCorePlugin
 * @subpackage lib.config
 * @author     pinhead
 * @version    SVN: $Id: yaConfigCache.class.php 2756 2010-12-15 22:50:26Z pinhead $
 */
class yaConfigCache extends sfConfigCache
{
  /**
   * Loads all configuration application and module level handlers.
   *
   * @throws <b>sfConfigurationException</b> If a configuration related error occurs.
   */
  protected function loadConfigHandlers()
  {
    // manually create our config_handlers.yml handler
    $this->handlers['config_handlers.yml'] = new sfRootConfigHandler();

    // application configuration handlers
    require $this->checkConfig('config/config_handlers.yml');

    // override factory configuration handler
    $this->handlers['config/factories.yml'] = new yaFactoryConfigHandler();

    // module level configuration handlers

    // checks modules directory exists
    if (!is_readable($sf_app_modules_dir = sfConfig::get('sf_app_modules_dir')))
    {
      return;
    }

    // ignore names
    $ignore = array('.', '..', 'CVS', '.svn');

    // create a file pointer to the module dir
    $fp = opendir($sf_app_modules_dir);

    // loop through the directory and grab the modules
    while (($directory = readdir($fp)) !== false)
    {
      if (in_array($directory, $ignore))
      {
        continue;
      }

      $configPath = $sf_app_modules_dir.'/'.$directory.'/config/config_handlers.yml';

      if (is_readable($configPath))
      {
        // initialize the root configuration handler with this module name
        $params = array('module_level' => true, 'module_name' => $directory);

        $this->handlers['config_handlers.yml']->initialize($params);

        // replace module dir path with a special keyword that
        // checkConfig knows how to use
        $configPath = 'modules/'.$directory.'/config/config_handlers.yml';

        require $this->checkConfig($configPath);
      }
    }

    // close file pointer
    closedir($fp);
  }
}
