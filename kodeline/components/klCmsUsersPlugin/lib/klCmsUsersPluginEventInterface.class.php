<?php
interface klCmsUsersPluginEventInterface
{
  /**
   * Process method for "user.update".
   * @param sfEvent $event
   */
  public static function listenToUpdateUserEvent(sfEvent $event);

  /**
   * Process method for "user.new".
   * @param sfEvent $event
   */
  public static function listenToNewUserEvent(sfEvent $event);


  /**
   * Создает или обновляет объект
   * расширенного профиля для базового пользователя.
   * 
   * @param sfGuardUser $user Пользователь.
   * @param string $componentName Наименование компонента, используемого для расширенного профиля пользователя.
   * @return instance
   */
  public static function updateFieldsFromUser(sfGuardUser $user, $componentName);

  /**
   * Добавляет поле в список игнорируемых при клонировании
   * данных пользователя в расширенный профиль.
   * 
   * @param string $fname Имя поля (столбца).
   */
  public static function addExcludeField($fname);

  /**
   * Удаляет поле из списка игнорируемых при клонировании
   * данных пользователя в расширенный профиль.
   * 
   * @param string $fname Имя поля (столбца).
   */
  public static function removeExcludeField($fname);
}