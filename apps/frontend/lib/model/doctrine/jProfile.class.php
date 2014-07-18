<?php
/**
 * jProfile
 * 
 * @package    stilum.ru
 * @subpackage model
 * @author     chuga
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class jProfile extends BasejProfile
{
  /**
   * Retrieve avatar uri path.
   * 
   * @param boolean $type Тип вывода изображения.
   * @param string $image Изображение, заменяющее вывод при отсутствии изображения у пользователя.
   * @return string Относительный путь до изображения.
   */
  public function getAvatar($type = 'medium', $image = null)
  {
    // Определение изображения для вывода.
    $image = (($picture = $this->getPicture()) ? $picture : 
              (null == $image ? sprintf('%s_default_avatar.png', $type) : $image));
    
    stAccountUpdateFormPrivate::fetchConfiguration();
    $config = stAccountUpdateFormPrivate::$configUpload;

    if (file_exists($config[$type]['path'] . DIRECTORY_SEPARATOR . basename($image)))
    {
      return $config[$type]['rel_path'] . basename($image); 
    }
   
    // Default return.
    return sprintf('%s/%s', '/images', $image);
  }

  /**
   * Возвращает ФИО пользователя.
   * 
   * @param boolean $bMiddle Флаг вывода middle_name пользователя.
   * @return string
   */
  public function getFullName($bMiddle = false)
  {
    $sFirstName = $this->getFirstName();
    $sLastName = $this->getLastName();
    $sMiddleName = $this->getMiddleName();

    $sResultName = ($sFirstName . ' ' . (! empty($sLastName) ? $sLastName : '') . ' ' . ((! empty($sMiddleName) && $bMiddle) ? $sMiddleName : ''));
    return (! strlen(trim($sResultName)) ? $this->getUser()->getUsername() : $sResultName);
  }

  /**
   * Возвращает профиль по ID пользователя.
   * 
   * @param integer $idUser ID пользователя.
   * @return jProfile
   */
  public static function getUserById($idUser)
  {
    return Doctrine::getTable('jProfile')->createQuery()->where('user_id = ?', $idUser)->fetchOne();
  }

  /**
   * Создает или обновляет объект 
   * расширенного профиля для базового пользователя.
   * 
   * @param integer $idUser ID пользователя.
   * @return boolean
   */
  public static function copyUser($idUser)
  {
    // Выборка данных пользователя.
    $user = Doctrine::getTable('sfGuardUser')->createQuery()->where('id = ?', $idUser)->fetchOne();
    if (! $user) return false;

    // Выборка данных профиля пользователя.
    $profile = Doctrine::getTable('jProfile')->createQuery()->where('user_id = ?', $idUser)->fetchOne();

    // Сохранение информации о пользователе.
    $newProfile = new self();
    if ($profile) $newProfile->assignIdentifier($profile['id']);
    $newProfile['user_id'] = $idUser;
    $newProfile['is_active'] = $user['is_active'];
    $newProfile['first_name'] = $user['first_name'];
    $newProfile['last_name'] = $user['last_name'];
    $newProfile['email'] = $user['email_address'];   
    $newProfile->save();

    return $newProfile['id'];
  }

  /**
   * Создает или обновляет объект
   * расширенного профиля для базового пользователя.
   * 
   * @see copyUser
   * @param sfGuardUser $user Пользователь.
   * @return boolean
   */
  public static function copySfGuardUser(sfGuardUser $user)
  {
    return self::copyUser($user->getId());
  }
}
