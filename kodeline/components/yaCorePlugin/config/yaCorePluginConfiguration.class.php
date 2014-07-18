<?php
/**
 * yaCorePluginConfiguration class.
 * 
 * @package     kodeline.core
 * @subpackage  yaCorePlugin
 * @category    configurator
 * @author      Kodeline
 * @version     $Id$
 */
class yaCorePluginConfiguration extends sfPluginConfiguration
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    sfConfig::set('ya_core_dir', realpath(dirname(__FILE__).'/..'));
  }

  public function initialize()
  {
    $this->loadConfiguration();

    $this->dispatcher->connect('debug.web.load_panels', array('yaWebDebugPanelMemory', 'listenToLoadDebugWebPanelEvent'));
    sfSslRequirementActionMixin::register($this->dispatcher);
  }

  protected function loadConfiguration()
  {
    sfConfig::add(array(
      'sf_i18n'     => true,
      'sf_charset'  => 'utf-8',
      'sf_data_dir' => realpath(dirname(__FILE__) . '/../data')
    ));

    date_default_timezone_set(sfConfig::get('sf_default_timezone', 'GMT'));

    if (extension_loaded('mbstring')) {
      mb_internal_encoding('UTF-8');
      mb_regex_encoding('UTF-8');
    }
  }

  /**
   * Filters sfAutoload configuration values.
   *
   * @param sfEvent $event
   * @param array   $config
   *
   * @return array
   */
  public function filterAutoloadConfig(sfEvent $event, array $config)
  {
    $config = parent::filterAutoloadConfig($event, $config);

    /*
     * Do not load lib/vendor
     */
    $config['autoload'][$this->name.'_lib']['exclude'] = array('vendor');

    return $config;
  }
}