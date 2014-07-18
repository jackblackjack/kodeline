<?php
/**
 * 
 */
class jString
{
  /**
   * Возвращает строку со случайными символами из $library.
   * 
   * @param integer $length Длинна результата
   * @param string  $library Библиотека символов для генерации.
   * @return string
   */
  static public function getRandomString($length = 10, $library = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
  {
    $retval = '';
    $szlib = mb_strlen($library);
    while ($length--) { $retval .= $library[mt_rand(0, ($szlib - 1))]; }
    return $retval;
  }

  /**
   * Возвращает сумму прописью
   * @author runcore
   * @uses morph(...)
   */
  function num2str($num) {
      $nul='ноль';
      $ten=array(
          array('','один','два','три','четыре','пять','шесть','семь', 'восемь','девять'),
          array('','одна','две','три','четыре','пять','шесть','семь', 'восемь','девять'),
      );
      $a20=array('десять','одиннадцать','двенадцать','тринадцать','четырнадцать' ,'пятнадцать','шестнадцать','семнадцать','восемнадцать','девятнадцать');
      $tens=array(2=>'двадцать','тридцать','сорок','пятьдесят','шестьдесят','семьдесят' ,'восемьдесят','девяносто');
      $hundred=array('','сто','двести','триста','четыреста','пятьсот','шестьсот', 'семьсот','восемьсот','девятьсот');
      $unit=array( // Units
          array('копейка' ,'копейки' ,'копеек',  1),
          array('рубль'   ,'рубля'   ,'рублей'    ,0),
          array('тысяча'  ,'тысячи'  ,'тысяч'     ,1),
          array('миллион' ,'миллиона','миллионов' ,0),
          array('миллиард','милиарда','миллиардов',0),
      );
      //
      list($rub,$kop) = explode('.',sprintf("%015.2f", floatval($num)));
      $out = array();
      if (intval($rub)>0) {
          foreach(str_split($rub,3) as $uk=>$v) { // by 3 symbols
              if (!intval($v)) continue;
              $uk = sizeof($unit)-$uk-1; // unit key
              $gender = $unit[$uk][3];
              list($i1,$i2,$i3) = array_map('intval',str_split($v,1));
              // mega-logic
              $out[] = $hundred[$i1]; # 1xx-9xx
              if ($i2>1) $out[]= $tens[$i2].' '.$ten[$gender][$i3]; # 20-99
              else $out[]= $i2>0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
              // units without rub & kop
              if ($uk>1) $out[]= morph($v,$unit[$uk][0],$unit[$uk][1],$unit[$uk][2]);
          } //foreach
      }
      else $out[] = $nul;
      $out[] = morph(intval($rub), $unit[1][0],$unit[1][1],$unit[1][2]); // rub
      $out[] = $kop.' '.morph($kop,$unit[0][0],$unit[0][1],$unit[0][2]); // kop
      return trim(preg_replace('/ {2,}/', ' ', join(' ',$out)));
  }

  /**
   * Склоняем словоформу
   * @ author runcore
   */
  function morph($n, $f1, $f2, $f5) {
      $n = abs(intval($n)) % 100;
      if ($n>10 && $n<20) return $f5;
      $n = $n % 10;
      if ($n>1 && $n<5) return $f2;
      if ($n==1) return $f1;
      return $f5;
  }

  /**
   * Возвращает одно из указанных окончаний в зависимости от указанного значения.
   * 
   * @param $mValue Значение.
   * @param $singular Единственная форма (1 яблоко)
   * @param $plural_ver1 Множественная форма, третье лицо (5 яблок)
   * @param $plural_ver2 Множественная форма, второе лицо (3 яблока)
   * @return string
   */
  public static function plural($mValue, $ver1, $ver2 = null, $ver3 = null)
  {
    if (null === $ver2) $ver2 = $ver1;
    if (null === $ver3) $ver3 = $ver2;

    // Определение значения, с которым придется работать.
    $val = is_array($mValue) ? count($mValue) : ((is_object($mValue) && is_callable(array($mValue, 'count'))) ? $mValue->count() : intval($mValue));

    return (($val % 10 == 1 && $val % 100 != 11) ? $ver1 : ($val % 10 >= 2 && $val % 10 <= 4 && ($val % 100 < 10 || $val % 100 >= 20) ? $ver3 : $ver2));
  }

  /**
   * Переносит строку по указанному количеству символов.
   * 
   * @see wordwrap
   * 
   * @param $str
   * @param $width
   * @param $break
   * @param $bCut
   */
  public static function wordwrap($str, $width, $break = '&hellip;', $bCut = true)
  {
    // Определение регулярного выражения.
    $regexp = ($bCut ? '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.'}#' : '#^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){'.$width.',}\b#U');

    // Определение длинны строки.
    $str_len = (function_exists('mb_strlen') ? mb_strlen($str,'UTF-8') : preg_match_all('/[\x00-\x7F\xC0-\xFD]/', $str, $var_empty));
    
    if ($str_len < $width) return $str;

    $while_what = ceil($str_len / $width);
    $i = 1;
    $return = '';
    //while ($i < $while_what) {
      preg_match($regexp, $str, $matches);
      $string = $matches[0];
      $return .= $string . $break;
      $str = substr($str, strlen($string));
      //$i++;
    //}
    return $return;//.$str;
}

  /**
   */
  public static function substrwords($text, $maxchar, $end='&hellip;')
  {
    if (strlen($text) > $maxchar || $text == '')
    {
      $words = preg_split('/\s/', $text);
      $output = '';
            $i      = 0;
            while (1) {
                $length = strlen($output)+strlen($words[$i]);
                if ($length > $maxchar) {
                    break;
                } 
                else {
                    $output .= " " . $words[$i];
                    ++$i;
                }
            }
            $output .= $end;
        } 
        else {
            $output = $text;
        }
        return $output;
  }

  /**
   * Check value to find if it was serialized.
   *
   * If $data is not an string, then returned value will always be false.
   * Serialized data is always a string.
   *
   * @param mixed $data Value to check to see if was serialized.
   * @return bool False if not serialized and true if it was.
   */
  public static function is_serialized( $data )
  {
    // if it isn't a string, it isn't serialized
    if ( !is_string( $data ) )
      return false;
    $data = trim( $data );
    if ( 'N;' == $data )
      return true;
    if ( !preg_match( '/^([adObis]):/', $data, $badions ) )
      return false;
    switch ( $badions[1] ) {
      case 'a' :
      case 'O' :
      case 's' :
        if ( preg_match( "/^{$badions[1]}:[0-9]+:.*[;}]\$/s", $data ) )
          return true;
        break;
      case 'b' :
      case 'i' :
      case 'd' :
        if ( preg_match( "/^{$badions[1]}:[0-9.E-]+;\$/", $data ) )
          return true;
        break;
    }
    return false;
  }

  /**
   * Check whether serialized data is of string type.
   *
   * @param mixed $data Serialized data
   * @return bool False if not a serialized string, true if it is.
   */
  public static function is_serialized_string( $data ) {
    // if it isn't a string, it isn't a serialized string
    if ( !is_string( $data ) )
      return false;
    $data = trim( $data );
    if ( preg_match( '/^s:[0-9]+:.*;$/s', $data ) ) // this should fetch all serialized strings
      return true;
    return false;
  }

  /**
   * Serialize data, if needed.
   *
   * @param mixed $data Data that might be serialized.
   * @return mixed A scalar data
   */
  public static function maybe_serialize( $data ) {
    if ( is_array( $data ) || is_object( $data ) )
      return serialize( $data );

    if ( self::is_serialized( $data ) )
      return serialize( $data );

    return $data;
  }
}
