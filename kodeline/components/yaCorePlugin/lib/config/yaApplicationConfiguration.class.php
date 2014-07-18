<?php

require_once dirname(__FILE__) . '/yaConfigCache.class.php';

/**
 * Extends symfony application configuration.
 *
 * @package    yaCorePlugin
 * @subpackage lib.config
 * @author     pinhead
 * @version    SVN: $Id: yaApplicationConfiguration.class.php 2756 2010-12-15 22:50:26Z pinhead $
 */
abstract class yaApplicationConfiguration extends sfApplicationConfiguration
{
  /**
   * @see sfApplicationConfiguration
   */
  public function initConfiguration()
  {
    /*
     * Call parent
     */
    parent::initConfiguration();

    /*
     * Symfony 1.3 registers sfAutoloadAgain on dev env. This causes huge performance issues.
     */
    if ($this->isDebug())
    {
      sfAutoloadAgain::getInstance()->unregister();
    }

    /*
     * Connect to event "template.filter_parameters".
     */
    $this->getEventDispatcher()->connect('template.filter_parameters', array($this, 'onTemplateFilterParameters'));

    /*
     * Now that we have the project config, we can configure the doctrine cache
     */
    $this->configureDoctrineCache(Doctrine_Manager::getInstance());

    /*
     * Fire configuration event.
     */
    $this->getEventDispatcher()->notify(new sfEvent($this, 'application.configuration.finish'));
  }

  /**
   * Returns a configuration cache object for the current configuration.
   *
   * @return yaConfigCache  A yaConfigCache instance
   */
  public function getConfigCache()
  {
    if (null === $this->configCache)
    {
      $this->configCache = new yaConfigCache($this);
    }

    return $this->configCache;
  }

  /**
   * Listens to the template.filter_parameters event.
   * Adds OOP-aware helper adapter.
   *
   * @param  sfEvent $event       An sfEvent instance
   * @param  array   $parameters  An array of template parameters to filter
   *
   * @return array   The filtered parameters array
   */
  public function onTemplateFilterParameters(sfEvent $event, $parameters)
  {
    $parameters['hlpBroker'] = yaHelperBroker::getInstance();
    return $parameters;
  }
}