<?php
/**
 * This behavior implements trnsliteration for sluggable doctrine extension.
 *
 * @package     yaCorePlugin
 * @subpackage  behavior
 * @category    utility
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klSluggableBuilder
{
  /**
   * Separator for words.
   * 
   * @var string
   */
  private static $sSeparator = '-';

  /**
   * Sheme of the translit.
   * 
   * @var array
   */
  static $arConvertSchema = array(
    'а' => 'a',   'б' => 'b',   'в' => 'v',
    'г' => 'g',   'д' => 'd',   'е' => 'e',
    'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
    'и' => 'i',   'й' => 'y',   'к' => 'k',
    'л' => 'l',   'м' => 'm',   'н' => 'n',
    'о' => 'o',   'п' => 'p',   'р' => 'r',
    'с' => 's',   'т' => 't',   'у' => 'u',
    'ф' => 'f',   'х' => 'h',   'ц' => 'c',
    'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
    'ь' => "_",   'ы' => 'y',   'ъ' => "_",
    'э' => 'e',   'ю' => 'yu',  'я' => 'ya',
    'А' => 'A',   'Б' => 'B',   'В' => 'V',
    'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
    'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
    'И' => 'I',   'Й' => 'Y',   'К' => 'K',
    'Л' => 'L',   'М' => 'M',   'Н' => 'N',
    'О' => 'O',   'П' => 'P',   'Р' => 'R',
    'С' => 'S',   'Т' => 'T',   'У' => 'U',
    'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
    'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
    'Ь' => "_",   'Ы' => 'Y',   'Ъ' => "_",
    'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
  );

  /**
   * Transliteration string.
   * 
   * @param $text string String for transliteration.
   * @return string
   */
  static public function translit($text, $bSeparator = true)
  {
    return mb_strtolower(str_replace(' ', ($bSeparator ? self::$sSeparator : null), strtr(preg_replace('!\s+!', ' ', $text), self::$arConvertSchema)));
  }
}