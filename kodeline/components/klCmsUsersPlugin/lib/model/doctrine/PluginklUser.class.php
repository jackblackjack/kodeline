<?php
abstract class PluginklUser extends BaseklUser
{
  protected
    $_groups         = null,
    $_permissions    = null,
    $_allPermissions = null;

  /**
   * Returns the string representation of the object: "Username".
   * @return string
   */
  public function __toString()
  {
    return (string) $this->getUsername();
  }

  /**
   * Return random string from $library.
   * @param integer $length Leight of return string
   * @param string  $library Symbols library
   * @return string
   */
  public function getSaltString($length = 10, $library = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
  {
    $retval = '';
    $szlib = mb_strlen($library);
    while ($length--) { $retval .= $library[mt_rand(0, ($szlib - 1))]; }
    return $retval;
  }

  /**
   * Sets the user password.
   * @param string $password
   * @throws sfException
   */
  public function setPassword($password)
  {
    if (! $password && 0 == strlen(trim($password)))
    {
      return;
    }

    // Generate salt for password.
    if (! $salt = $this->getSalt())
    {
      $salt = md5($this->getSaltString(10) . $this->getUsername());
      $this->setSalt($salt);

    }

    // Fetch modified fields.
    $modified = $this->getModified();

    // Define the algorithm method.
    if ((!$algorithm = $this->getAlgorithm()) || (isset($modified['algorithm']) && $modified['algorithm'] == $this->getTable()->getDefaultValueOf('algorithm')))
    {
      $algorithm = sfConfig::get('app_klCmsUsersPlugin_algorithm_callable', 'sha1');
    }

    // If algorithm for crypt password has redefined - use it.
    $algorithmAsStr = (is_array($algorithm) && 1 < count($algorithm)) ? sprintf('%s::%s', array_shift($algorithm), array_shift($algorithm)) : $algorithm;
    
    if (! is_callable($algorithm))
    {
      throw new sfException(sprintf('The algorithm callable "%s" is not callable.', $algorithmAsStr));
    }

    // Sets the crypted algorithm.
    $this->setAlgorithm($algorithmAsStr);

    // Sets the password.
    $this->_set('password', call_user_func_array($algorithm, array($salt . $password)));
  }

  /**
   * Returns whether or not the given password is valid.
   * @param string $password
   * @return boolean
   */
  public function checkPassword($password)
  {
    if ($callable = sfConfig::get('app_klCmsUsersPlugin_check_password_callable'))
    {
      return call_user_func_array($callable, array($this->getUsername(), $password, $this));
    }
    else
    {
      return $this->checkPasswordByGuard($password);
    }
  }

  /**
   * Returns whether or not the given password is valid.
   * @param string $password
   * @return boolean
   * @throws sfException
   */
  public function checkPasswordByGuard($password)
  {
    // Fetch algorithm value.
    $algorithm = $this->getAlgorithm();

    // Check redefined algorithm.
    if (false !== ($pos = strpos($algorithm, '::'))) {
      $algorithm = array(substr($algorithm, 0, $pos), substr($algorithm, $pos + 2));
    }

    // Check crypt algorithm callable.
    if (! is_callable($algorithm))
    {
      throw new sfException(sprintf('The algorithm callable "%s" is not callable.', $algorithm));
    }

    // Returns results of check.
    return $this->getPassword() == call_user_func_array($algorithm, array($this->getSalt() . $password));
  }

  /**
   * Adds the user a new group from its name.
   * @param array|string $arNames The groups names
   * @param Doctrine_Connection $con A Doctrine_Connection object
   * @return Doctrine_Collection
   * @throws sfException
   * @throws Exception
   */
  public function addGroupByName($arNames, $con = null)
  {
    // Fix list of groups.
    $arNames = (! is_array($arNames) ? array($arNames) : $arNames);

    // Fetch groups by name.
    $usergroups = Doctrine_Core::getTable('klUserGroup')->createQuery()
                    ->whereIn('name', $arNames)->execute(array(), Doctrine::HYDRATE_RECORD);

    if (! $usergroups) {
      throw new sfException(sprintf('The user\'s groups "%s" does not exist.', implode(',', $arNames)));
    }

    // Create collection for groups.
    $collection = new Doctrine_Collection('klUserGroup');

    // Create list for save.
    foreach ($usergroups as $group)
    {
      $ug = new klUserGroup();
      $ug->setUser($this);
      $ug->setGroup($group);

      $collection->add($ug);
    }

    // Returns save result.
    return $collection->save($con);
  }

  /**
   * Adds the user a new group from its id.
   * @param array|integer $arId The groups id
   * @param Doctrine_Connection $con A Doctrine_Connection object
   * @return Doctrine_Collection
   * @throws sfException
   * @throws Exception
   */
  public function addGroupById($arId, $con = null)
  {
    // Fix list of groups.
    $arId = (! is_array($arId) ? array($arId) : $arId);

    // Fetch groups by id.
    $usergroups = Doctrine_Core::getTable('klUserGroup')->createQuery()
                    ->whereIn('id', $arId)->execute(array(), Doctrine::HYDRATE_RECORD);

    if (! $usergroups) {
      throw new sfException(sprintf('The user\'s groups #"%s" does not exist.', implode(',', $arId)));
    }

    // Create collection for groups.
    $collection = new Doctrine_Collection('klUserGroup');

    // Create list for save.
    foreach ($usergroups as $group)
    {
      $ug = new klUserGroup();
      $ug->setUser($this);
      $ug->setGroup($group);

      $collection->add($ug);
    }

    // Returns save result.
    return $collection->save($con);
  }

  /**
   * Adds the user a permission from its name.
   * @param array|string $arNames The permissions name
   * @param Doctrine_Connection $con A Doctrine_Connection object
   * @return Doctrine_Collection
   * @throws sfException
   * @throws Exception
   */
  public function addPermissionByName($arNames, $con = null)
  {
    // Fix list of permissions.
    $arNames = (! is_array($arNames) ? array($arNames) : $arNames);

    // Fetch permissions by name.
    $permissions = Doctrine_Core::getTable('klPermission')->createQuery()
                    ->whereIn('name', $arNames)->execute(array(), Doctrine::HYDRATE_RECORD);

    if (! $permissions) {
      throw new sfException(sprintf('The permissions "%s" does not exist.', implode(',', $arNames)));
    }

    // Create collection for groups.
    $collection = new Doctrine_Collection('klPermission');

    // Create list for save.
    foreach ($permissions as $permission)
    {
      $up = new klPermission();
      $up->setUser($this);
      $up->setPermission($permission);

      $collection->add($ug);
    }

    // Returns save result.
    return $collection->save($con);
  }

  /**
   * Adds the user a permission from its id.
   * @param array|integer $arId The permissions id
   * @param Doctrine_Connection $con A Doctrine_Connection object
   * @return Doctrine_Collection
   * @throws sfException
   * @throws Exception
   */
  public function addPermissionById($arId, $con = null)
  {
    // Fix list of permissions.
    $arId = (! is_array($arId) ? array($arId) : $arId);

    // Fetch permissions by id.
    $permissions = Doctrine_Core::getTable('klPermission')->createQuery()
                    ->whereIn('id', $arId)->execute(array(), Doctrine::HYDRATE_RECORD);

    if (! $permissions) {
      throw new sfException(sprintf('The permissions #"%s" does not exist.', implode(',', $arId)));
    }

    // Create collection for groups.
    $collection = new Doctrine_Collection('klPermission');

    // Create list for save.
    foreach ($permissions as $permission)
    {
      $up = new klPermission();
      $up->setUser($this);
      $up->setPermission($permission);

      $collection->add($ug);
    }

    // Returns save result.
    return $collection->save($con);
  }

  /**
   * Checks whether or not the user belongs to the given group.
   *
   * @param string $name The group name
   * @return boolean
   */
  public function hasGroup($name)
  {
    $this->loadGroupsAndPermissions();
    return isset($this->_groups[$name]);
  }

  /**
   * Returns all related groups names.
   *
   * @return array
   */
  public function getGroupNames()
  {
    $this->loadGroupsAndPermissions();
    return array_keys($this->_groups);
  }

  /**
   * Returns whether or not the user has the given permission.
   *
   * @return boolean
   */
  public function hasPermission($name)
  {
    $this->loadGroupsAndPermissions();
    return isset($this->_allPermissions[$name]);
  }

  /**
   * Returns an array of all user's permissions names.
   *
   * @return array
   */
  public function getPermissionNames()
  {
    $this->loadGroupsAndPermissions();
    return array_keys($this->_allPermissions);
  }

  /**
   * Returns an array containing all permissions, including groups permissions
   * and single permissions.
   *
   * @return array
   */
  public function getAllPermissions()
  {
    if (! $this->_allPermissions)
    {
      $this->_allPermissions = array();
      $permissions = $this->getPermissions();

      foreach ($permissions as $permission)
      {
        $this->_allPermissions[$permission->getName()] = $permission;
      }

      foreach ($this->getGroups() as $group)
      {
        foreach ($group->getPermissions() as $permission)
        {
          $this->_allPermissions[$permission->getName()] = $permission;
        }
      }
    }

    return $this->_allPermissions;
  }

  /**
   * Returns an array of all permission names.
   *
   * @return array
   */
  public function getAllPermissionNames()
  {
    return array_keys($this->getAllPermissions());
  }

  /**
   * Loads the user's groups and permissions.
   *
   */
  public function loadGroupsAndPermissions()
  {
    $this->getAllPermissions();
    
    if (!$this->_permissions)
    {
      $permissions = $this->getPermissions();
      foreach ($permissions as $permission)
      {
        $this->_permissions[$permission->getName()] = $permission;
      }
    }
    
    if (!$this->_groups)
    {
      $groups = $this->getGroups();
      foreach ($groups as $group)
      {
        $this->_groups[$group->getName()] = $group;
      }
    }
  }

  /**
   * Reloads the user's groups and permissions.
   */
  public function reloadGroupsAndPermissions()
  {
    $this->_groups         = null;
    $this->_permissions    = null;
    $this->_allPermissions = null;
  }

  /**
   * Sets the password hash.
   *
   * @param string $v
   */
  public function setPasswordHash($v)
  {
    if (!is_null($v) && !is_string($v))
    {
      $v = (string) $v;
    }

    if ($this->password !== $v)
    {
      $this->_set('password', $v);
    }
  }
}