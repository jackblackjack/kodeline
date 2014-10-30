<?php
/**
 * klPluginConfiguration class.
 * 
 * @package     kodeline-cms-core
 * @subpackage  configuration
 * @author      Kodeline
 * @version     $Id$
 */
class klPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Call parent mathod.
    parent::initialize();

    // Enabled modules for plugin.
    $this->enablePluginModules();

    return true;
  }

  /**
   * Enables all plugin modules.
   */
  protected function enablePluginModules()
  {
    // Fetch plugin modules list.
    $arPluginModules = glob(sprintf('%s/modules/*', $this->getRootDir()), GLOB_NOSORT | GLOB_ONLYDIR | GLOB_ERR);

    // Normalize modules names.
    $szModules = count($arPluginModules);
    if (! $szModules) return true;

    // Define class name for rounting.
    $sProcessClassRouteName = $this->getName() . 'Routing';
    $bProcessClassRouteExists = class_exists($sProcessClassRouteName);

    for ($i = 0; $i < $szModules; $i++)
    {
      // Add list for modules plugins.
      $arPluginModules[$i] = basename($arPluginModules[$i]);  

      // Define method name for module route's.
      $bProcessModuleMethodName = 'addRoutesFor' . ucfirst($arPluginModules[$i]);
      $bProcessModuleMethodExists = method_exists($sProcessClassRouteName, $bProcessModuleMethodName);

      // Sets routing rules.
      if ($bProcessClassRouteExists && $bProcessModuleMethodExists)
      {
        $this->dispatcher->connect('routing.load_configuration', array($sProcessClassRouteName, $bProcessModuleMethodName));
      }
    }
   
    // Sets enabled modules.
    sfConfig::set('sf_enabled_modules', array_merge(sfConfig::get('sf_enabled_modules', array()), $arPluginModules));
  }
}