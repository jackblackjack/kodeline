<?php
/**
 * Extends sfGuardSecurityUser class
 *
 * @package     yaCorePlugin
 * @subpackage  lib.user
 * @author      pinhead
 * @version     SVN: $Id: yaCoreUser.class.php 2365 2010-09-28 13:37:13Z pinhead $
 */
class klCoreUser extends sfBasicSecurityUser
{
  /**
   * Разделитель для сериализации атрибутов пользователя.
   * Используется в методах getAttribute и setAttribute.
   * 
   * @var string
   */
  protected static $sSerializeNullSeparator = '~~NULL_BYTE~~';

  /**
   * @var yaContext
   */
  protected
    $context = null;

  /**
   * @var yaHolder
   */
  protected
    $geoip = null;

  /**
   * @see sfGuardSecurityUser
   */
  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    parent::initialize($dispatcher, $storage, $options);

    $this->context = yaContext::getInstance();
  }

  /**
   * @see sfUser
   */
  public function getAttribute($name, $default = null, $ns = null)
  {
    return str_replace(self::$sSerializeNullSeparator, "\0", parent::getAttribute($name, $default, $ns));
  }

  /**
   * @see sfUser
   */
  public function setAttribute($name, $value, $ns = null)
  {
    return parent::setAttribute($name, str_replace("\0", self::$sSerializeNullSeparator, $value), $ns);
  }

  /**
   * Возвращает ID текущего пользователя.
   *
   * @return integer|null
   * @throws sfConfigurationException
   */
  public function getId()
  {
    //if (! $this->isAuthenticated()) return null;
    return $this->getKlUser()->getId();
  }

  /**
   * Retrieves user email address
   *
   * @return string
   */
  public function getEmail()
  {
    return $this->getGuardUser()->getEmailAddress();
  }

  /**
   * Retrieves user 'welcome' name
   *
   * @return string
   */
  public function getWelcomeName()
  {
    return 'hello';
    $sResultName = null;
    if ($profile = $this->getProfile() && !empty($profile['first_name']))
    {
      $sResultName = $profile->getFirstName();
    }

    return (! strlen($sResultName) ? $this->getUsername() : $sResultName);
  }

  /**
   * Checks current user is a guest user
   *
   * @return bool
   * @throws sfConfigurationException
   */
  public function isGuest()
  {
    if ($this->isAnonymous()) return true;

    if (null == ($group = sfConfig::get('app_ya_core_plugin_guest_group', 'guest')))
    {
      throw new sfConfigurationException('Guest group not configured.');
    }

    return $this->hasGroup($group);
  }

  /**
   * Retrieves user IP
   *
   * @return string
   */
  public static function getIP()
  {
    $ips = array();

    if (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) { $ips[] = trim(strtok($_SERVER['HTTP_X_FORWARDED_FOR'], ',')); }
    if (! empty($_SERVER['HTTP_CLIENT_IP'])) { $ips[] = $_SERVER['HTTP_CLIENT_IP']; }
    if (! empty($_SERVER['REMOTE_ADDR'])) { $ips[] = $_SERVER['REMOTE_ADDR']; }
    if (! empty($_SERVER['HTTP_X_REAL_IP'])) { $ips[] = $_SERVER['HTTP_X_REAL_IP']; }

    $_ =& $ips;
    foreach($ips as $ip) { if (false !== ip2long($ip)) { return $ip; } }
    return null;
  }

  /**
   * Lookups and returns GeoIP database record for current user IP
   *
   * @return yaHolder
   */
  public function lookupGeoipInfo()
  {
    if (null == $this->geoip &&
        function_exists('geoip_record_by_name') && ($data = @geoip_record_by_name($this->getIP())))
    {
      $cb = create_function('&$item, $key', '$item = utf8_encode($item);');
      if (array_walk($data, $cb))
      {
        $this->geoip = new yaHolder($data);
      }
    }

    return $this->geoip;
  }

  /**
   * Retrieves GeoIP record field value
   *
   * @return mixed
   */
  public function getGeoipField($field)
  {
    if ($info = $this->getGeoipInfo())
    {
      return $info[$field];
    }

    return null;
  }

  /**
   * Retrieves user country code from GeoIP database
   *
   * @return string
   */
  public function getGeoipCountry()
  {
    return $this->getGeoipField('country_code');
  }

  /**
   * Retrieves user city name from GeoIP database
   *
   * @return string
   */
  public function getGeoipCity()
  {
    return $this->getGeoipField('city');
  }
}
