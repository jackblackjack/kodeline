<?php
class klCmsUsersPluginEvent implements klCmsUsersPluginEventInterface 
{
  /**
   * Process method for "user.update".
   * @param sfEvent $event
   */
  public static function listenToUpdateUserEvent(sfEvent $event)
  {
    // Define component for user profile.
    $componentName = sfConfig::get('app_jDoctrineProfilePlugin_component_name', 'jProfileExtension');

    // Define classname for base processing.
    $processClass = sfConfig::get('app_jDoctrineProfilePlugin_process_class', 'jProfileProcess');

    // Определение метода для вызова.
    $profileCall = new sfCallable(array($processClass, 'updateFieldsFromUser'));

    // Create profile from user
    $profile = $profileCall->call($event['user'], $componentName);

    // Set value for return.
    $event->setReturnValue($profile);

    // Return profile extension component.
    return $profile;
  }

  /**
   * Process method for "user.new".
   * @param sfEvent $event
   */
  public static function listenToNewUserEvent(sfEvent $event)
  {
    $dispatcher = ya::getEventDispatcher();
    $app = ya::getConfiguration()->getApplication();

    if (sfConfig::get('sf_logging_enabled')) {
      $dispatcher->notify(new sfEvent($app, 'application.log', array(
        sprintf('Fire "%s"', __METHOD__), 'priority' => sfLogger::INFO)));
    }

    if (empty($event['user_id'])) {
      $dispatcher->notify(new sfEvent($app, 'application.log', array(
        sprintf('Cannot find param "%s" in the "%s" method', 'user_id', __METHOD__), 
        'priority' => sfLogger::CRIT)));

      $event->setReturnValue(false);
      return false;
    }

    // Define component for user profile.
    $componentName = sfConfig::get('app_jDoctrineProfilePlugin_component_name', 'jProfileExtension');

    // Create new profile of the user.
    if (! method_exists($componentName, 'updateFromUserById')) {
      $event->setReturnValue(false);
      throw new sfException($dispatcher->getContext()->getI18N()->__('Возникла ошибка при создании профиля пользователя!', null, 'profile'));
    }

    // Fetch profile of the user.
    $profile = call_user_func(array($componentName, 'updateFromUserById'), $event['user_id']);
    $event->setReturnValue($profile);
    return $profile;
  }
}