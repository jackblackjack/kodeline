<?php
class klCmsUsersPluginProcess
{
  /**
   * List of the excluded fields.
   * @var array
   */
  protected static $arExcludedFields = array('id');

  /**
   * Notify by system logger.
   * 
   * @param $event sfEvent Event of the
   * @return null
   */
  public static function notify($event)
  {
    if (! sfContext::hasInstance())
    {
    }

    sfContext::getInstance()->getEventDispatcher()->notify($event);
  }

  /**
   * @see jProfileProcessInterface
   */
  public static function addExcludeField($fname)
  {
    if (false == array_search($fname, self::$arExcludedFields))
    {
      self::$arExcludedFields[] = $fname;
    }
  }

  /**
   * @see jProfileProcessInterface
   */
  public static function removeExcludeField($fname)
  {
    if (false != ($fIndex = array_search($fname, self::$arExcludedFields)))
    {
      unset(self::$arExcludedFields[$fIndex]);
    }
  }

  /**
   * @see jProfileProcessInterface
   */
  public static function updateFieldsFromUser(sfGuardUser $user, $componentName)
  {
    // Check user instance of sfBasicSecurityUser.
    if (! ($user instanceof sfBasicSecurityUser))
    {
      self::notify(new sfEvent(null, 'application.log', array(
        sprintf('User object is not instance of the sfBasicSecurityUser'), 'priority' => sfLogger::ERR)));
    }

    // Fetch component record of the user id.
    $profile = Doctrine::getTable($componentName)->createQuery()->where('user_id = ?', $user['id'])->fetchOne();

    if (! $profile)
    {
      // If profile has not exists - create new.
      $profile = new $componentName();
      $profile['user_id'] = $user['id'];
    }   

    // Определение соответствия полей расширенного профиля с базовым профилем пользователя.
    $arFieldsMap = sfConfig::get('app_jDoctrineProfilePlugin_fields_map', array());

    // Fetch component table columns.
    $profileColumns = $profile->getTable()->getColumns();
   
    // Fetch base user table columns.
    $userColumns = $user->getTable()->getColumns();
    $_ =& $profileColumns; // is_ref = 1;

    // Set columns values for profile.
    foreach($profileColumns as $colName => $definition)
    {
      // Skip excludes fields.
      if (false !== array_search($colName, self::$arExcludedFields)) continue;

      // Проверка сопоставлений в карте полей.
      if (! empty($arFieldsMap[$colName]))
      {
        // Если есть простое сопоставление поле - поле.
        if (! empty($arFieldsMap[$colName]['source']) && ! empty($userColumns[$arFieldsMap[$colName]['source']]))
        {
          $profile[$colName] = $user[$arFieldsMap[$colName]['source']];
          continue;
        }

        // Если установлен дополнительный сеттер для поля.
        if (! empty($arFieldsMap[$colName]['setter']))
        {
          $setterClass = $arFieldsMap[$colName]['setter']['class'];
          $setterMethod = $arFieldsMap[$colName]['setter']['method'];

          // Определение возможности вызова сеттера.
          $setterCall = new sfCallable(array($setterClass, $setterMethod));
          if (is_callable($setterCall->getCallable()))
          {
            $profile[$colName] = $setterCall->call($user);
            continue;
          }
        }
      }

      // Простое копирование.
      if (! empty($userColumns[$colName]) && $profileColumns[$colName]['type'] == $userColumns[$colName]['type']) {
        $profile[$colName] = $user[$colName];
      }
    }

    // Сохранение расширенного профиля пользователя.
    $profile->save();

    // Возврат обновленного профиля пользователя.
    return $profile;
  }
}
