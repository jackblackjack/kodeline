<?php
class PluginklUserTable extends myDoctrineTable
{
  /**
   * Returns an instance of this class.
   * @return object PluginklUserTable
   */
  public static function getInstance()
  {
    return Doctrine_Core::getTable('PluginklUser');
  }

  /**
   * Retrieves a sfGuardUser object by username and is_active flag.
   * @param  string  $username The username
   * @param  boolean $isActive The user's status
   * @return klUser
   */
  public function retrieveByUsername($username, $isActive = true)
  {
    // Define query.
    $query = Doctrine_Core::getTable('klUser')->createQuery('u')
              ->where('u.username = ?', $username)
              ->addWhere('u.is_active = ?', (int) $isActive);
    
    return $query->fetchOne();
  }

  /**
   * Retrieves a sfGuardUser object by username or email_address and is_active flag.
   * @param  string  $username The username
   * @param  boolean $isActive The user's status
   * @return klUser
   */
  public function retrieveByUsernameOrEmailAddress($username, $isActive = true)
  {
    // Define query.
    $query = Doctrine_Core::getTable('klUser')->createQuery('u')
              ->where('u.username = ? OR u.email_address = ?', array($username, $username))
              ->addWhere('u.is_active = ?', $isActive);

    return $query->fetchOne();
  }
}