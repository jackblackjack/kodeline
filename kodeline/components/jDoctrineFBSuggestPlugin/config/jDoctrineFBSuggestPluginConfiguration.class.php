<?php
/**
 * jDoctrineFBSuggestPlugin configuration.
 * 
 * @package     jDoctrineFBSuggestPlugin
 * @subpackage  config
 * @author      GSschurgast
 * @version     $Id$
 */
class jDoctrineFBSuggestPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Инициализация обработчика событий.
    $classListener = sfConfig::get('app_Suggest_listener', 'jFbSuggestListener');

    // Регистрация нового пользователя (user.new).
    $this->dispatcher->connect('suggest.search', array($classListener, 'listenToSearchEvent'));
    
    // Инициализация роутинга.
    $this->dispatcher->connect('routing.load_configuration', array('jFbSuggestRouting', 'addSearchRoute'));
  }
}
