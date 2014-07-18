<?php

class yaProject
{

  protected static
    $key,
    $hash,
    $models;

  /**
   * Returns project key based on his dir_name
   */
  public static function getKey()
  {
    if (null === self::$key)
    {
      self::$key = basename(sfConfig::get('sf_root_dir'));
    }
    
    return self::$key;
  }

  /**
   * Returns project key based on his root dir
   */
  public static function getHash()
  {
    if (null === self::$hash)
    {
      self::$hash = substr(md5(sfConfig::get('sf_root_dir')), -8);
    }
    
    return self::$hash;
  }
  
  public static function getRootDir()
  {
    return yaOs::normalize(sfConfig::get('sf_root_dir'));
  }
  
  public static function getNormalizedRootDir()
  {
    return yaOs::normalize(self::getRootDir());
  }
  
  /**
   * remove sfConfig::get('sf_root_dir') from path
   */
  public static function unRootify($path)
  {
    if (self::isInProject($path))
    {
      $path = substr($path, strlen(self::getRootDir()));
    }
    
    return trim($path, '/');
  }
  
  /**
   * add sfConfig::get('sf_root_dir') to path
   */
  public static function rootify($path)
  {
    if (!self::isInProject($path))
    {
      $path = yaOs::join(self::getRootDir(), $path);
    }
    else
    {
      $path = yaOs::join($path);
    }
    
    return $path;
  }
  
  public static function isInProject($path)
  {
    return strpos(yaOs::normalize($path), self::getRootDir()) === 0;
  }
  
  public static function appExists($application)
  {
    return file_exists(self::rootify('apps/'.$application.'/config/'.$application.'Configuration.class.php'));
  }
}