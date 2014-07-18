<?php
/**
 * Kodeline cms events layers list.
 * 
 * @package     klCmsEventPlugin
 * @category    utility
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klEventLayer
{ 
  /**
   * Name of supported level.
   * @var string
   */
  const PLUGIN  = 'plugin';

  /**
   * Name of supported level.
   * @var string
   */
  const COMPONENT  = 'component';

  /**
   * List of supported constants.
   * @var array
   */
  protected static $arCachedConstants = array();

  /**
   * Return list of supported constants.
   * @return array
   */
  public static function getSupported()
  {
    if (count(self::$arCachedConstants)) return self::$arCachedConstants;

    $reflection = new ReflectionClass(__CLASS__);
    self::$arCachedConstants = $reflection->getConstants();

    return self::$arCachedConstants;
  }
}