<?php
/**
 * yaMorphyToolkit manages pages.
 *
 * @package     yaMorphyPlugin
 * @subpackage  toolkit
 * @category    morphology
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class yaMorphyToolkit
{
  /**
   * Список поставщиков словарей.
   * 
   * @var array
   */
  static private $arDictVendors = array('aot', 'ispell');

  /**
   * Экземпляры морфологических словарей.
   * 
   * @var array
   */
  static private $arMorphologists = array();

  /**
   * Список кодировок.
   */
  static private $arMultiByteCodepages = array('utf-8');

  /**
   * Возвращает экземпляр класса, работы со словарем.
   * 
   * @param string $codepage Кодировка, относительно которой следует производить поиск морфологии.
   * @param string $locale Локаль, относительно которой следует производить поиск морфологии.
   * @param string $vendor Тип словаря, относительно которого следует производить поиск морфологии.
   * @param boolean $bLight Вариант словаря, относительно которого следует производить поиск морфологии (например для русского языка: full - с поддержкой буквы ё, light - без)
   * @return yaMorphyMorphologist
   */
  static public function getGlossary($locale, $codepage, $vendor = 'aot', $bLight = false)
  {
    $codepage = strtolower($codepage);
    $locale = strtolower($locale);

    // Если экземпляр морфологии определен - возвращаем его.
    if (array_key_exists($codepage, self::$arMorphologists) && array_key_exists($locale, self::$arMorphologists[$codepage])) {
      return self::$arMorphologists[$codepage][$locale];
    }

    // Проверка активности плагина.
    if (! sfConfig::get('app_yaMorphyPlugin_enable', false)) {
      throw new sfException('Plugin yaMorphy is disabled');
    }
  
    // Создание экземляра класса морфологизатора.
    self::$arMorphologists[$codepage][$locale] = new yaMorphyMorphologist($codepage, $locale, $vendor, $bLight);

    // Возврат экзмепляра словаря.
    return self::$arMorphologists[$codepage][$locale];
  }

  public static function isMultiCodepage($codepage) {
    return (false !== in_array($codepage, self::$arMultiByteCodepages));
  }
}

