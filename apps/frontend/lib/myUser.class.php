<?php
/**
 * myUser class.
 *
 * @package     frontend
 * @subpackage  apps.frontend.lib
 * @author      chugarev
 * @version     $Id$
 */
class myUser extends yaCoreUser
{
  /**
   * Возвращает ID текущего пользователя.
   *
   * @return integer|null
   * @throws sfConfigurationException
   */
  public function getId()
  {
    if (! $this->isAuthenticated()) return null;
    return $this->getGuardUser()->getId();
  }
}