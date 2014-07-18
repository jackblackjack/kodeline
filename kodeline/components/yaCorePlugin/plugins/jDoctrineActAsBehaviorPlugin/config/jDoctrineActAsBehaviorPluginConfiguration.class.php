<?php
/**
 * Конфигурация плагина jDoctrineActAsBehaviorPlugin.
 * 
 * @package    jDoctrineActAsBehaviorPluginConfiguration
 * @subpackage configuration
 * @author     chugarev@gmail.com
 * @version    $Id$
 */
class jDoctrineActAsBehaviorPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Инициализация обработчика событий.
    //$classListener = sfConfig::get('app_jDoctrineProfilePlugin_listener', 'behaviorParameterableListener');
    $classListener = 'behaviorParameterableListener';

    // Регистрация нового пользователя (user.new).
    $this->dispatcher->connect('parameterable.update.baseclasses', array($classListener, 'listenParameterableUpdateBasesEvent'));
  }
}
