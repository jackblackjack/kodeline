<?php
/**
 * Хелпер для вывода дат.
 *
 * @package     jHelpersPlugin
 * @subpackage  helper
 * @author      chugarev
 * @version     $Id$
 */
class jDatetime
{
  const MIN_SECONDS  = 60;          // количество секунд в минуте.
  const HOUR_SECONDS = 3600;        // количество секунд в часе.
  const DAY_SECONDS = 86400;        // количество секунд в дне (24 часа).
  const WEEK_SECONDS = 604800;      // количество секунд в неделе (168 часов).
  const MONTH_SECONDS = 2629743.83; // количество секнуд в месяце (среднее относительно года).
  const YEAR_SECONDS = 31557600;    // количество секнуд в году.

  /**
   * Список ключей для образования окончаний множественных чисел при пересчете времени.
   * @var string
   */
  const PLURAL_VAR_NAMES = 'y,mo,w,d,h,m,s';

  /**
   * Массив замещенных значений 
   * последним вызовом методов setPluralVars или setPluralVar.
   * @var array
   */
  static protected $arLastPluralVars = array('y' => array('год', 'лет', 'года'), 'mo'=> array('месяц', 'месяцев', 'месяца'), 'w' => array('неделя', 'недель', 'недели'), 'd' => array('день', 'дней', 'дня'), 'h' => array('час', 'часов', 'часа'),  'm' => array('минута', 'минут', 'минуты'), 's' => array('секунда', 'секунд', 'секунды'));

  /**
   * Массив текущих значений для формирований окончаний множественных значений.
   * @var array
   */
  static protected $arPluralVars = array('y' => array('год', 'лет', 'года'), 'mo'=> array('месяц', 'месяцев', 'месяца'), 'w' => array('неделя', 'недель', 'недели'), 'd' => array('день', 'дней', 'дня'), 'h' => array('час', 'часов', 'часа'),  'm' => array('минута', 'минут', 'минуты'), 's' => array('секунда', 'секунд', 'секунды'));


  /**
   * List of months.
   *
   * @staticvar array
   */
  static $arMonths = array(
      'nominative'  => array(1 => 'Январь', 'Февраль', 'Март', 'Апрель', 'Май', 'Июнь', 'Июль', 'Август', 'Сентябрь', 'Октябрь', 'Ноябрь', 'Декабрь'),
      'parental'    => array(1 => 'Января', 'Февраля', 'Марта', 'Апреля', 'Мая', 'Июня', 'Июля', 'Августа', 'Сентября', 'Октября', 'Ноября', 'Декабря'),
  );

  /**
   * Возвращает значения для вывода значений множественных чисел.
   * @return array
   */
  static function getPluralVars()
  {
    return self::$arPluralVars;
  }

  /**
   */
  static function setPluralVars($arValue)
  {
    // Filter keys of value for only allowed names.
    $arPluralVarNames = explode(',', self::PLURAL_VAR_NAMES);
    $arValue = array_change_key_case($arValue, CASE_LOWER);
    $arTainedVars = array_intersect_key($arValue, array_flip($arPluralVarNames));

    // Save last plural vars.
    self::$arLastPluralVars = self::$arPluralVars;

    // Update plural variables.
    self::$arPluralVars = array_intersect_key(self::$arPluralVars, $arTainedVars);

    return self::getPluralVars();
  }

  static function resetPluralVars()
  {
    self::$arPluralVars = self::$arLastPluralVars;
    return self::getPluralVars();
  }

  /**
   */
  static function setPluralVar($sPart, $arValue)
  {

  }

  /**
   *
   */
  static function date($sCase, $format, $timestamp = null)
  {
    // Define timestamp.
    $sCase = strtolower(trim($sCase));
    $sCase = (! array_key_exists($sCase, self::$arMonths) ? 'nominative' : $sCase);

    $timestamp = (null == $timestamp ? time() : $timestamp);

    // Define cureent month number.
    $iMonth = date('n', $timestamp);

    // Prepare date string.
    $format = str_replace('F', self::$arMonths[$sCase][$iMonth], $format);
    return date($format, $timestamp);
  }

  /**
   * Конвертирует секунды в года, месяцы, дни, часы, минуты и секунды.
   * Возвращает массив с пересчитанными данными.
   * 
   * @param integer $iSeconds  Секунды.
   * @param array   $arPeriods Ограничитель остановки пересчета (y,mo,w,d,h,m или s).
   * @param boolean $bConversion Флаг простого перерасчета (без отнимания того, что уже посчитано в течение расчетов).
   * @return array
   */
  static function duration($iSeconds, $arPeriods = array(), $bConversion = false)
  {
    // Изначальные периоды.
    $arCalc = array_combine(($t = array('y', 'mo', 'w', 'd', 'h', 'm', 's')), $t);

    // Если указаны периоды - оставляем только их.
    if (! empty($arPeriods) && is_array($arPeriods))
    {
      $arPeriods = array_combine($arPeriods, $arPeriods);
      $arPeriods = array_change_key_case($arPeriods, CASE_LOWER);
      $arCalc = array_intersect_key($arCalc, $arPeriods);
    }

    // Года.
    if (isset($arCalc['y'])) {
      $arCalc['y'] = floor ( $iSeconds / self::YEAR_SECONDS );
    }

    // Месяцы.
    if (isset($arCalc['mo'])) {
      $arCalc['mo'] = floor( ($iSeconds - ($bConversion ? 0 : ( 
        (isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0)
        ))) / self::MONTH_SECONDS );
    }
    
    // Недели.
    if (isset($arCalc['w'])) {
      $arCalc['w'] = floor( ($iSeconds - ($bConversion ? 0 : ( 
        (isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0) + 
        (isset($arCalc['mo']) ? ($arCalc['mo'] * self::MONTH_SECONDS) : 0)
        ))) / self::WEEK_SECONDS );
    }

    // Дни.
    if (isset($arCalc['d'])) {
      $arCalc['d'] = floor( ($iSeconds - ($bConversion ? 0 : ( 
        (isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0) + 
        (isset($arCalc['mo']) ? ($arCalc['mo'] * self::MONTH_SECONDS) : 0) + 
        (isset($arCalc['w']) ? ($arCalc['w'] * self::WEEK_SECONDS) : 0)
        ))) / self::DAY_SECONDS );
    }

    // Часы.
    if (isset($arCalc['h'])) {
      $arCalc['h'] = floor( ($iSeconds - ($bConversion ? 0 : ( 
        (isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0) + 
        (isset($arCalc['mo']) ? ($arCalc['mo'] * self::MONTH_SECONDS) : 0) + 
        (isset($arCalc['w']) ? ($arCalc['w'] * self::WEEK_SECONDS) : 0) +
        (isset($arCalc['d']) ? ($arCalc['d'] * self::DAY_SECONDS) : 0)
        ))) / self::HOUR_SECONDS );
    }

    // Минуты.
    if (isset($arCalc['m'])) {
      $arCalc['m'] = floor( ($iSeconds - ($bConversion ? 0 : ( 
        (isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0) + 
        (isset($arCalc['mo']) ? ($arCalc['mo'] * self::MONTH_SECONDS) : 0) + 
        (isset($arCalc['w']) ? ($arCalc['w'] * self::WEEK_SECONDS) : 0) +
        (isset($arCalc['d']) ? ($arCalc['d'] * self::DAY_SECONDS) : 0) +
        (isset($arCalc['h']) ? ($arCalc['h'] * self::HOUR_SECONDS) : 0)
        ))) / self::MIN_SECONDS );
    }

    // Секунды.
    if (isset($arCalc['s'])) {
      $arCalc['s'] = ($bConversion ? $iSeconds : 
        ($iSeconds -
        ((isset($arCalc['y']) ? ($arCalc['y'] * self::YEAR_SECONDS) : 0) + 
        (isset($arCalc['mo']) ? ($arCalc['mo'] * self::MONTH_SECONDS) : 0) + 
        (isset($arCalc['w']) ? ($arCalc['w'] * self::WEEK_SECONDS) : 0) +
        (isset($arCalc['d']) ? ($arCalc['d'] * self::DAY_SECONDS) : 0) +
        (isset($arCalc['h']) ? ($arCalc['h'] * self::HOUR_SECONDS) : 0) +
        (isset($arCalc['m']) ? ($arCalc['m'] * self::MIN_SECONDS) : 0)
        ))
      );
    }

    return $arCalc;
  }

  /**
   */
  static function age($iSeconds, $arPeriods = array(), $arSeparators = array(', ', ' и '), 
        $arPluralValues = array(
          'y' => array('год', 'лет', 'года'), 
          'mo'=> array('месяц', 'месяцев', 'месяца'),
          'w' => array('неделя', 'недель', 'недели'), 
          'd' => array('день', 'дней', 'дня'), 
          'h' => array('час', 'часов', 'часа'), 
          'm' => array('минута', 'минут', 'минуты'),
          's' => array('секунда', 'секунд', 'секунды')))
  {
    // Пересчет количества секунд в периоды времени.
    $arDuration = self::duration($iSeconds, $arPeriods);

    $arResult = '';

    $szDuration = count($arDuration);
    $arKeys = array_keys($arDuration);
    $szShowed = 0;
    for($i = 0; $i < $szDuration; $i++)
    {
      if ($arDuration[$arKeys[$i]])
      {
        $arCallArgs = $arPluralValues[$arKeys[$i]];
        array_unshift($arCallArgs, $arDuration[$arKeys[$i]]);
        $arResult[] = sprintf('%d %s', $arDuration[$arKeys[$i]], forward_static_call_array(array('jString', 'plural'), $arCallArgs));
      }
    }

    $arLast = (1 < count($arResult) ? array_pop($arResult) : null);
    return @implode($arSeparators[0], $arResult) . ($arLast ? $arSeparators[1] . $arLast : null);

              //(0 == $szShowed++ ? '' : (($i + 1 == $szDuration) ? $arSeparators[1] : $arSeparators[0])),

    return $strResult;
  }

  /**
   */
  static function ceilAge($iSeconds, $iPeriods = 1, $arSeparators = array(', ', ' и '))
  {
    // Пересчет количества секунд в периоды времени.
    $arDuration = self::duration($iSeconds);

    // Фильтрация пустых значений.
    $arDuration = array_filter($arDuration, create_function('$v', 'return intval($v);'));
    array_splice($arDuration, $iPeriods);

    $szDuration = count($arDuration);
    $arKeys = array_keys($arDuration);
    $szPrepared = 0;
    $arPluralVars = self::getPluralVars();

    for($i = 0; $i < $szDuration; $i++)
    {
      if ($arDuration[$arKeys[$i]])
      {
        $arCallArgs = $arPluralVars[$arKeys[$i]];
        array_unshift($arCallArgs, $arDuration[$arKeys[$i]]);
        $arResult[] = sprintf('%d %s', $arDuration[$arKeys[$i]], 
          forward_static_call_array(array('jString', 'plural'), $arCallArgs));
      }
    }

    $arLast = (1 < count($arResult) ? array_pop($arResult) : null);

    // Возврат результата.
    return implode($arSeparators[0], $arResult) . ($arLast ? $arSeparators[1] . $arLast : null);
  }
}
