<?php

/**
 * Extends sfInflector.
 *
 * @package    yaCorePlugin
 * @subpackage lib.basic
 * @author     pinhead
 * @version    SVN: $Id: yaString.class.php 2409 2010-10-13 13:32:29Z pinhead $
 */
class yaString extends sfInflector
{
  const
    ENCODING_SEPARATOR = '__YASPLIT__';

  const
    DATE      = 1,
    TIME      = 2,
    DATETIME  = 3;


  protected static
    $transliterationMap = array();

  protected static
    $camelizeCache = array();

  public static function escape($text, $quoteStyle = ENT_QUOTES)
  {
    return htmlspecialchars($text, $quoteStyle, 'UTF-8');
  }

  /**
   * Clean dirty strings
   */
  public static function unixify($text)
  {
    return strtr($text, array(
        "\r"      => ''
      , "\t"      => '  '
      , "&#8217;" => "'"
      , 'вЂњ'       => '&lquot;'
      , 'вЂќ'       => '&rquot;'
      , 'В®'       => '&reg;'
      , 'вЂ�'       => '&lsquo;'
      , 'вЂ™'       => '&rsquo;'
    ));
  }

  /**
   * Adds a final 's'
   */
  public static function pluralize($word)
  {
    return $word[strlen($word)-1] == 's' ? $word : $word.'s';
  }

  public static function pluralizeNb($word, $nb, $specialPlural = false)
  {
    if($specialPlural)
    {
      return $nb > 1 ? $specialPlural : $word;
    }
    else
    {
      return $nb > 1 ? self::pluralize($word) : $word;
    }
  }

  /**
   * Returns a module formatted string
   * ModuleName => moduleName
   * module_name => moduleName
   */
  public static function modulize($something)
  {
    if ($model = self::camelize($something))
    {
      $model[0] = strtolower($model[0]);
    }

    return $model;
  }

  /**
   * Returns a camelized string from a lower case and underscored string by
   * upper-casing each letter preceded by an underscore.
   * modelName => ModelName
   * model_name => ModelName
   */
  public static function camelize($something)
  {
    if (is_object($something))
    {
      return get_class($something);
    }

    if (!is_string($something))
    {
      if (empty($something))
      {
        return '';
      }

      throw new yaException('Can not camelize '.$something);
    }

    if (isset(self::$camelizeCache[$something]))
    {
      return self::$camelizeCache[$something];
    }

    return self::$camelizeCache[$something] = preg_replace(
      '/_(\w)/e',
      "strtoupper('\\1')",
      ucfirst($something)
    );
  }

  public static function humanize($text)
  {
    return parent::humanize(self::underscore($text));
  }

  /**
   * Transform any text into a valid slug
   * @return string slug
   */
  public static function slugify($text, $preserveSlashes = false)
  {
    if ($preserveSlashes)
    {
      $text = str_replace('/', '_s_l_a_s_h_', $text);
    }

    $text = self::transliterate($text);

    // strip all non word chars
    // replace all white space sections with a dash
    $text = preg_replace(array('/\W/', '/\s+/'), array(' ', '-'), $text);

    // trim and lowercase
    $text = self::strtolower(trim($text, '-'));

    if ($preserveSlashes)
    {
      $text = str_replace('_s_l_a_s_h_', '/', $text);
    }

    return $text;
  }

  /**
   * Like slugify, but allows ".htm" and ".html" extensions
   * @return string slug
   */
  public static function urlize($text, $preserveSlashes = false)
  {
    if(!preg_match('|\.html?$|', $text))
    {
      return self::slugify($text, $preserveSlashes);
    }

    return preg_replace('|^(.*)-(html?)$|', '$1.$2', self::slugify($text, $preserveSlashes));
  }

  /**
   * Transform a slug into a human readable text with blank spaces
   * @return string text
   */
  public static function unSlugify($slug)
  {
    return str_replace('-', ' ', $slug);
  }

  public static function transliterate($text)
  {
    if (! preg_match('/[\x80-\xff]/', $text))
    {
      return $text;
    }

    if (! sfConfig::get('ya_string_transliteration'))
    {
      sfConfig::set('ya_string_transliteration', self::$transliterationMap);
    }

    $text = strtr($text, sfConfig::get('ya_string_transliteration'));

    return $text;
  }

  /**
   * Transform string options to array options
   * Symfony and jQuery styles are accepted
   * e.g. "#an_id.a_class.another_class an_option=a_value"
   * results in array(
   *    id => an_id
   *    class => array(a_class, another_class)
   *    an_option => a_value
   *  )
   * @return array options
   */
  public static function toArray($string, $implodeClasses = false)
  {
    if(is_array($string))
    {
      return $string;
    }

    if (empty($string))
    {
      return array();
    }

    $array = array();

    // JQUERY STYLE - css expression
    self::retrieveCssFromString($string, $array);

    // SYMFONY STYLE - string opt in name
    self::retrieveOptFromString($string, $array);

    if ($implodeClasses && isset($array['class']))
    {
      $array['class'] = implode(' ', $array['class']);
    }

    return $array;
  }

  /**
   * Transform css options to array options
   * e.g. "#an_id.a_class.another_class"
   * results in array(
   *    id => an_id
   *    class => array(a_class, another_class)
   *  )
   * only expressions before the first space are taken into account
   * @return array options
   */
  public static function retrieveCssFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }

    $string = trim($string);

    /*
     * Stop search for classes and id
     * if a space or an equal sign appears
     */
    $spacePos = strpos($string, ' ');
    $equalPos = strpos($string, '=');
    $stopPos = min(false === $spacePos ? PHP_INT_MAX : $spacePos, false === $equalPos ? PHP_INT_MAX : $equalPos);

    $firstSharpPos = strpos($string, '#');

    // if we have a # before the first space
    if (false !== $firstSharpPos && (false === $stopPos || $firstSharpPos < $stopPos))
    {
      // fetch id
      preg_match('/#([\w\-]*)/', $string, $id);
      if (isset($id[1]))
      {
        $opt['id'] = $id[1];
        $string = self::str_replace_once('#'.$id[1], '', $string);

        if (false != $stopPos)
        {
          $stopPos = $stopPos - strlen($id[1]) - 1;
        }
      }
    }

    // while we find dots in the string
    while(false !== ($firstDotPos = strpos($string, '.')))
    {
      // if the string contains a space, and the dot is after this space, then it's not a class
      if (false !== $stopPos && $firstDotPos > $stopPos)
      {
        break;
      }

      // fetch class
      preg_match('/\.([\w\-]*)/', $string, $class);

      if (isset($class[1]))
      {
        if (isset($opt['class']))
        {
          $opt['class'][] = $class[1];
        }
        else
        {
          $opt['class'] = array($class[1]);
        }

        if (false != $stopPos)
        {
          $stopPos = $stopPos - strlen($class[1]) - 1;
        }
      }

      $string = self::str_replace_once('.'.$class[1], '', $string);
    }
  }

  public static function retrieveOptFromString(&$string, &$opt)
  {
    if (empty($string))
    {
      return null;
    }

    $opt = array_merge($opt, sfToolkit::stringToArray($string));

    $string = '';
  }

  /**
   * Returns a random string
   */
  public static function random($length = 8)
  {
    $val = '';
    $values = 'abcdefghijklmnopqrstuvwxyz0123456789';
    for ( $i = 0; $i < $length; $i++ )
    {
      $val .= $values[rand( 0, 35 )];
    }

    return $val;
  }

  /**
   * Will use mb_strtolower if available, strtolower if not
   * @param   $str  the string
   * @return  $str  the lowercase string
   */
  public static function strtolower($str)
  {
    return function_exists('mb_strtolower') ? mb_strtolower($str) : strtolower($str);
  }

  public static function truncate($text, $length = 30, $truncateString = '...', $truncateLastspace = false)
  {
    if(is_array($text))
    {
      throw new yaException('Can not truncate an array: '.implode(', ', $text));
    }

    $text = (string) $text;

    if(extension_loaded('mbstring'))
    {
      $strlen = 'mb_strlen';
      $substr = 'mb_substr';
    }
    else
    {
      $strlen = 'strlen';
      $substr = 'substr';
    }

    if ($strlen($text) > $length)
    {
      $text = $substr($text, 0, $length - $strlen($truncateString));

      if ($truncateLastspace)
      {
        $text = preg_replace('/\s+?(\S+)?$/', '', $text);
      }

      $text = $text.$truncateString;
    }

    return $text;
  }

  public static function encode($value)
  {
    if (is_array($value))
    {
      $value = implode(self::ENCODING_SEPARATOR, $value);
    }

    return base64_encode($value);
  }

  public static function decode($coded_value)
  {
    $value = base64_decode($coded_value);

    if (strpos($value, self::ENCODING_SEPARATOR) !== false)
    {
      $value = explode(self::ENCODING_SEPARATOR, $value);
    }

    return $value;
  }

  public static function getBaseFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      return substr($url, 0, $pos);
    }

    return $url;
  }

  public static function getDataFromUrl($url)
  {
    if ($pos = strpos($url, '?'))
    {
      parse_str(str_replace('&amp;', '&', substr($url, $pos + 1)), $params);
      return $params;
    }

    return array();
  }

  /**
   * Returns a valid hex color uppercased without first #,
   * or null if not possible
   */
  public static function hexColor($color)
  {
    if (preg_match('|^#?[\dA-F]{6}$|i', $color))
    {
      return strtoupper(trim($color, '#'));
    }

    return null;
  }

  public static function lcfirst($string)
  {
    if (!empty($string))
    {
      $string{0} = self::strtolower($string{0});
    }

    return $string;
  }

  /**
   * replace $search by $replace in $subject, only once
   */
  public static function str_replace_once($search, $replace, $subject)
  {
    $firstChar = strpos($subject, $search);

    if($firstChar !== false)
    {
      return substr($subject,0,$firstChar).$replace.substr($subject, $firstChar + strlen($search));
    }
    else
    {
      return $subject;
    }
  }

  /**
   * Convert a shorthand byte value from a PHP configuration directive to an integer value
   * @param    string   $value
   * @return   int
   */
  public static function convertBytes( $value )
  {
    if ( is_numeric( $value ) )
    {
      return $value;
    }
    else
    {
      $valueLength = strlen( $value );
      $qty = substr( $value, 0, $valueLength - 1 );
      $unit = strtolower( substr( $value, $valueLength - 1 ) );

      switch ( $unit )
      {
        case 'k':
          $qty *= 1024;
          break;
        case 'm':
          $qty *= 1048576;
          break;
        case 'g':
          $qty *= 1073741824;
          break;
      }

      return $qty;
    }
  }

  /**
   * Converts seconds to human readable format
   *
   * @param int $secs
   *
   * @return string
   */
  public static function humanizeSeconds($secs, $delimiter = ' ', $addSymbols = true, $symbols = array('w', 'd', 'h', 'm', 's'))
  {
    $vals = array((int) ($secs / 86400 / 7),
                  $secs / 86400 % 7,
                  $secs / 3600 % 24,
                  $secs / 60 % 60,
                  $secs % 60);

    $ret = array();

    $added = false;
    foreach ($vals as $k => $v)
    {
      if ($v > 0 || $added)
      {
        $added = true;
        $ret[] = $v . ($addSymbols ? (isset($symbols[$k]) ? $symbols[$k] : '') : '');
      }
    }

    return join($delimiter, $ret);
  }

  /**
   * unixtimeToSQL()
   */
  public static function unixtimeToSql($unixtime, $mode = self::DATETIME)
  {
    $format = '%s%s';
    $format = trim(sprintf($format, ($mode & self::DATETIME == self::DATE) ? '%Y-%m-%d' : '', ($mode & self::DATETIME == self::TIME) ? ' %H:%M:%S' : ''));
    return strftime($format, $unixtime);
  }

  /**
   * sqlTimeToSec()
   */
  public static function sqlTimeToSec($sqlTime)
  {
    list($h, $m, $s) = explode(':', $sqlTime, 3);
    return (3600 * (int) $h) + (60 * (int) $m) + (int) $s;
  }

  /**
   * secToSqlTime()
   */
  public static function secToSqlTime($seconds)
  {
    $h = (int) floor($seconds / 3600);
    $m = (int) floor(($seconds % 3600) / 60);
    $s = (int) floor(($seconds % 3600) % 60);

    return sprintf('%02d:%02d:%02d', $h, $m, $s);
  }


}