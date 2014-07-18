<?php
/**
 * ProjectConfiguration class.
 * 
 * @package     kodeline.core
 * @category    configuration
 * @author      Kodeline
 * @version     $Id$
 */
require_once dirname(__DIR__) . '/kodeline/components/yaCorePlugin/lib/core/ya.class.php'; 
ya::start();

class ProjectConfiguration extends yaProjectConfiguration
{
  /**
   * {@inheritDoc}
   */
  public function setup()
  {
    parent::setup();
    
    // Enable all plugins exclude propel.
    $this->enableAllPluginsExcept(array('sfPropelPlugin'));
  }
}
