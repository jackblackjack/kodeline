<?php
/**
 * jWidgetsPluginPlugin configuration.
 * 
 * @package     jWidgetsPluginPlugin
 * @subpackage  config
 * @author      GSschurgast
 * @version     $Id$
 */
class jWidgetsPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Инициализация обработчика событий.
    $classListener = sfConfig::get('app_WidgetsPlugin_listener', 'jWidgetsPluginListener');

    // Регистрация нового пользователя (user.new).
    $this->dispatcher->connect('suggest.search', array($classListener, 'listenToSearchEvent'));
    
    // Инициализация роутинга.
    $this->dispatcher->connect('routing.load_configuration', array('jWidgetsPluginRouting', 'addSearchRoute'));
  }
}