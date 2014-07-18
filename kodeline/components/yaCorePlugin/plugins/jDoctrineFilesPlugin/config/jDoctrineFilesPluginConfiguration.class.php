<?php
/**
 * jDoctrineFBSuggestPlugin configuration.
 * 
 * @package     jDoctrineFBSuggestPlugin
 * @subpackage  config
 * @author      GSschurgast
 * @version     $Id$
 */
class jDoctrineFilesPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {   
    // Initialize routes.
    $this->dispatcher->connect('routing.load_configuration', array('jFileAttachableRouting', 'addRoutes'));

    // Initialize autoenable modules.
    $this->dispatcher->connect('context.load_factories', array($this, 'listenToContextLoadFactories'));
  }

  /**
   * Automatic plugin modules and helper loading.
   *
   * @param sfEvent $event
   */
  public function listenToContextLoadFactories(sfEvent $event)
  {
    // Enable module of attachments automatically.
    sfConfig::set('sf_enabled_modules', array_merge(sfConfig::get('sf_enabled_modules', array()), array('jFileAttachable')));
   
    // Load helper as well.
    $event->getSubject()->getConfiguration()->loadHelpers(array('jFileAttachable'));
  }
}
