<?php
/**
 * klCmsSitePlugin configuration.
 * 
 * @package     kodeline-cms
 * @subpackage  klCmsSitePlugin
 * @category    configuration
 * @author      Kodeline
 * @version     $Id$
 */
class klCmsSitePluginConfiguration extends klPluginConfiguration
{
  /**
   * Plugin filters key
   * @var string
   */
  const PLUGIN_FILTER_KEY = 'site_plugin_filter';

  /**
   * @see sfPluginConfiguration
   */
  public function setup()
  {
    // Plugin frontend filter setup.
    $this->setupFrontendFilter();
  }

  /**
   * Setup plugin filter.
   */
  private function setupFrontendFilter()
  {
    // Fetch system filters.
    $filters = sfFilterConfigHandler::getConfiguration(array(
      sfConfig::get('sf_symfony_lib_dir') . '/config/config/filters.yml',
      sfConfig::get('sf_plugins_dir') . '/yaCorePlugin/config/filters.yml',
      sfConfig::get('sf_root_dir') . '/apps/frontend/config/filters.yml')
    );

    if (! array_key_exists(self::PLUGIN_FILTER_KEY, $filters))
    {
      // If plugin's filters has not exists - set up.
      $filters = array(self::PLUGIN_FILTER_KEY => array('enabled' => true, 'class' => 'klCmsSiteFilter')) + $filters;

      // Save filter list.
      file_put_contents(sfConfig::get('sf_root_dir') . '/apps/frontend/config/filters.yml', sfYaml::dump($filters));
    }
  }
}