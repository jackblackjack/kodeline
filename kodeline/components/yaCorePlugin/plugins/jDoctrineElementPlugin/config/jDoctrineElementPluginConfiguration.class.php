<?php
/**
 * Конфигурация плагина.
 * 
 * @package    jDoctrineProfilePlugin
 * @subpackage config
 * @author     chugarev
 * @version    $Id$
 */
class jDoctrineElementPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    /*
    // Инициализация обработчика событий.
    $classListener = sfConfig::get('app_jDoctrineProfilePlugin_listener', 'jProfileListener');

    // Регистрация нового пользователя (user.new).
    $this->dispatcher->connect('user.new', array($classListener, 'listenToNewUserEvent'));
    
    // Обновление информации о пользователе (user.update).
    $this->dispatcher->connect('user.update', array($classListener, 'listenToUpdateUserEvent'));

    // Инициализация роутинга.
    if (sfConfig::get('app_jDoctrineProfilePlugin_routes_register', true)) 
    {
      foreach (array('jProfileSignUp', 'jProfile', 'jProfileForgotPassword') as $module)
      {
        if (in_array($module, sfConfig::get('sf_enabled_modules', array())))
        {
          $routeCall = new sfCallable(array('jProfileRouting', 'addRouteForProfile' . str_replace('jProfile', '', $module)));
          if (is_callable($routeCall->getCallable()))
          {
            $this->dispatcher->connect('routing.load_configuration', $routeCall->getCallable());
          }
        }
      }
    }
    */
  }
}
