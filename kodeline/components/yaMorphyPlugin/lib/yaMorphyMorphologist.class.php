<?php
/**
 * Морфологизатор строк.
 *
 * @package     yaMorphyPlugin
 * @subpackage  morphologist
 * @link        http://phpmorphy.sourceforge.net/dokuwiki/
 * @category    morphology
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
include( __DIR__ . "/vendor/phpmorphy/src/common.php");

class yaMorphyMorphologist
{ 
  /**
   * Объект словаря phpMorphy
   * 
   * @var phpMorphy
   */
  private $morphy = null;

  /**
   * Локаль, относительно которой производится поиск морфологии.
   * 
   * @var string
   */
  private $locale = null;

  /**
   * Кодировка, относительно которой производится поиск морфологии.
   * 
   * @var string
   */
  private $codepage = null;

  /**
   * Тип словаря, относительно которого производится поиск морфологии.
   * 
   * @var string
   */
  private $vendor = null;

  /**
   * __construct
   *
   * @param string $codepage Кодировка, относительно которой следует производить поиск морфологии.
   * @param string $locale Локаль, относительно которой следует производить поиск морфологии.
   * @param string $vendor Тип словаря, относительно которого следует производить поиск морфологии.
   * @param boolean $bLight Вариант словаря, относительно которого следует производить поиск морфологии (например для русского языка: full - с поддержкой буквы ё, light - без)
   * @return void
   */
  public function __construct($codepage = 'utf-8', $locales = array('ru', 'en'), $vendor = 'ispell', $bLight = false)
  {
    // Check enabled plugin.
    $arConfigs = array_change_key_case(sfConfig::get('app_yaMorphyPlugin_instances', array()), CASE_LOWER);

    if (! array_key_exists($locale, $arConfigs)) {
      throw new sfException(sprintf('Config for locale "%s" is not found', $locale));
    }

    if (! array_key_exists($codepage, $arConfigs[$locale])) {
      throw new sfException(sprintf('Config for codepage "%s" in locale "%s" is not found', $codepage, $locale));
    }

    // Сохранение параметров локали и кодировки.
    $this->locales = $locales;
    $this->codepage = $codepage;
    $this->vendor = $vendor;

    // Определение директории со словарями.
    $dictDirSuffix = sprintf('%s%s%s%s.%s', DIRECTORY_SEPARATOR, $codepage, DIRECTORY_SEPARATOR, $vendor, $locale) . ($bLight ? '.light' : '.full');

    // Пробуем создать экземпляр морфологизатора.
    $this->morphy = new phpMorphy(
      new phpMorphy_FilesBundle(
        __DIR__ . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'phpmorphy' . DIRECTORY_SEPARATOR .
        (isset($arConfigs[$locale][$codepage]['dictionary_directory']) ? $arConfigs[$locale][$codepage]['dictionary_directory'] : 'dicts') . $dictDirSuffix,  $locale), 
      (isset($arConfigs[$locale][$codepage]['options']) ? $arConfigs[$locale][$codepage]['options'] : array()));
  }

  /**
   * Возвращает тип словаря,
   * относительно которого производится поиск морфологии.
   * 
   * @return string
   */
  public function getVendor() {
    return $this->vendor;
  }

  /**
   * Возвращает локаль, 
   * относительно которой производится поиск морфологии.
   * 
   * @return string
   */
  public function getLocale() {
    return $this->morphy->getLocale();
  }

  /**
   * Возвращает кодировку,
   * относительно которой производится поиск морфологии.
   * 
   * @return string
   */
  public function getCodepage() {
    return $this->morphy->getEncoding();
  }

  /**
   * Подготавливает строку к переводу.
   */
  public function prepareString($string)
  {
    // Определение метода нормализации строки.
    $normalizeFuncName = 'normalize_' . $this->locale;
    if (method_exists($this, $normalizeFuncName)) {
      $string = call_user_func(array($this, $normalizeFuncName), $string);
    }

    return (yaMorphyToolkit::isMultiCodepage($this->codepage) ? mb_strtoupper($string, $this->codepage) : strtoupper($string));
  }

  /**
   * Конвертирует строку в массив.
   * 
   * @param string $string Строка для определение морфологии.
   * @return array
   */
  public function convertStringToArray($string)
  {
    $array = explode(' ', $string);
    return  (! is_array($array) ? array($string) : $array);
  }

  /**
   * Нормализует строку для русской локали.
   * Заменяет все латинские символы, 
   * похожие по написанию на русские, на русские символы.
   * 
   * @param string $string Строка для преобразования
   * @return string
   */
  public function normalize_ru_ru($string)
  {
    return str_ireplace(
            array("a", "c", "e", "y", "o", "p", "x"),
            array("а", "с", "е", "у", "о", "р", "х"),
            $string);
  }

  /**
   * Возвращает все формы слова и возвращает массив с 
   * привязанными морфологическими формами в дополнение к словам, 
   * перечисленным в слове.
   * 
   * @param string $string
   * @return array
   */
  public function getAllForms($string)
  {
    $string = $this->prepareString($string);
    $tarray = $this->convertStringToArray($string);
  
    // Получение всех форм слов в переданной строке.
    $result = $this->morphy->getAllFormsWithAncodes($tarray);

    // Привязка
    $tarray = array_combine($tarray, $tarray);
    $arKeys = array_keys($tarray);
    $szKeys = count($arKeys);

    for($i = 0; $i < $szKeys; $i++) {
      if (array_key_exists($arKeys[$i], $result)) {
        $tarray[$arKeys[$i]] = $result[$arKeys[$i]];
      }
      else {
        $tarray[$arKeys[$i]] = false;
      }
    }
    return $tarray;
  }

  /**
   * Возвращает все формы слова и возвращает массив с 
   * привязанными морфологическими формами в дополнение к словам, 
   * перечисленным в слове.
   * 
   * @param string $string
   * @return array
   */
  public function getBaseForm($string)
  {
    $string = $this->prepareString($string);
    $tarray = $this->convertStringToArray($string);
  
    // Получение всех форм слов в переданной строке.
    $result = $this->morphy->getBaseForm($tarray);

    // Привязка
    $tarray = array_combine($tarray, $tarray);
    $arKeys = array_keys($tarray);
    $szKeys = count($arKeys);

    for($i = 0; $i < $szKeys; $i++) {
      if (array_key_exists($arKeys[$i], $result)) {
        $tarray[$arKeys[$i]] = $result[$arKeys[$i]];
      }
      else {
        $tarray[$arKeys[$i]] = false;
      }
    }
    return $tarray;
  }

  /**
   * Возвращает общую часть слов в строке.
   * 
   * @param string $string Строка для получения общей части слов.
   * @return array
   */
  public function getRoot($string)
  {
    $string = $this->prepareString($string);
    $tarray = $this->convertStringToArray($string);
    return $this->morphy->getPseudoRoot($tarray);
  }
}