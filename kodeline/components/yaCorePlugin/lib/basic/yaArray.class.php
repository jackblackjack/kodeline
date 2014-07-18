<?php

/**
 * Provides convenient methods for arrays.
 *
 * @package    yaCorePlugin
 * @subpackage lib.basic
 * @author     pinhead
 * @version    SVN: $Id: yaArray.class.php 2432 2010-11-03 01:28:23Z pinhead $
 */
class yaArray
{
  /**
   * Sets each array key to its corresponding value
   *
   * @param array $array source array
   *
   * @return array new array
   */
  public static function valueToKey(array $array)
  {
    $tmp = array();
    foreach($array as $value)
    {
      $tmp[$value] = $value;
    }
    unset($array);
    return $tmp;
  }

  /**
   * Returns the value of an array, if the key exists
   *
   * @param array      $array          source array
   * @param int|string $key            key to get
   * @param mixed      $default        default
   * @param boolean    $defaultIfEmpty activate check for if empty
   *
   * @return mixed
   */
  public static function get($array, $key, $default = null, $defaultIfEmpty = false)
  {
    if (!is_array($array))
    {
      return $default;
    }

    if (false === $defaultIfEmpty)
    {
      if(isset($array[$key]))
      {
        return $array[$key];
      }
      else
      {
        return $default;
      }
    }

    if(!empty($array[$key]))
    {
      return $array[$key];
    }
    else
    {
      return $default;
    }
  }

  /**
   * get first value of an array
   *
   * @param array $array source array
   *
   * @return mixed first value
   */
  public static function first($array)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $a = array_shift($array);
    unset($array);

    return $a;
  }

  /**
   * get last value of an array
   *
   * @param array $array source array
   *
   * @return mixed last value
   */
  public static function last($array)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $a = array_pop($array);
    unset($array);

    return $a;
  }

  /**
   * get first n values of an array
   *
   * @param array $array source array
   * @param int   $nb    how many values
   *
   * @return array the first n values
   */
  public static function firsts($array, $nb)
  {
    if(!is_array($array))
    {
      return $array;
    }

    if(empty($array))
    {
      return null;
    }

    $nb = min(array($nb, count($array)));

    $return_entries = array_slice($array, 0, $nb);
    unset($array);

    return $return_entries;
  }

  /**
   * Unset empty elements of the array
   *
   * @param array $array array to clean
   * @param array $keys  keys to clean
   *
   * @return array cleaned array
   */
  public static function unsetEmpty(array $array, array $keys)
  {
    foreach($keys as $key)
    {
      if(empty($array[$key]) && array_key_exists($key, $array))
      {
        unset($array[$key]);
      }
    }

    return $array;
  }

  /**
   * Transform an array of string to a css classes string
   *
   * @param array $classes single class or multi class values allowed
   *
   * @return string cleaned value
   */
  public static function toHtmlCssClasses(array $classes)
  {
    return implode(' ', array_unique(array_filter(array_map('trim', $classes))));
  }

  /**
   * Converts stdClass object to multidimensional array.
   *
   * @param object/array  $d
   *
   * @return array
   */
  public static function objectToArray($d)
  {
    if (is_object($d))
    {
      $d = get_object_vars($d);
    }

    if (is_array($d))
    {
      return array_map(array('yaArray', 'objectToArray'), $d);
    }

    return $d;
  }

  /**
   * Converts multidimensional array to stdClass object.
   *
   * @param array $d
   *
   * @return stdClass
   */
  public static function arrayToObject($d)
  {
    if (is_array($d))
    {
      return (object) array_map(array('yaArray', 'arrayToObject'), $d);
    }

    return $d;
  }


}
