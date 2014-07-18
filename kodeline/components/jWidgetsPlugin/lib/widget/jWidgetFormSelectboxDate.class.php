<?php
/**
 * apWidgetFormSelectDate
 *
 * @package     apWidgetsPlugin
 * @category    plugin
 * 
 * @author      chugarev
 * @version     $Id$
 */
class jWidgetFormSelectboxDate extends sfWidgetForm
{
  /**
   * List of extended options.
   * 
   * @staticvar array
   */
  protected static $arOptionsExtNames = array();
  
  /**
   * Список подписей для месяцев.
   * 
   * @staticvar array
   */
  protected static $arMonthsLabels = array('января', 'февраля', "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря");
  
  /**
   * @inheritDoc
   */
  public function __construct($options = array(), $attributes = array())
  {
    // Define extended options for input.
    $arInputOptions = array();
    if (! empty(self::$arOptionsExtNames))
    {
      $arInputOptions = array_combine(self::$arOptionsExtNames, self::$arOptionsExtNames);
      $this->arOptionsExt = array_intersect_key($options, $arInputOptions);
    }

    // Set for constructor standart options.
    parent::__construct(array_diff_key($options, $arInputOptions), $attributes);
  }

  
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   *  * format:       The phone format string (%country% %area% %number% by default)
   *  * can_be_empty: Whether the widget accept an empty value (true by default)
   *  * empty_values: An array of values to use for the empty value (empty string for country, area, and number by default)
   *
   * @param array $options     An array of options
   * @param array $attributes  An array of default HTML attributes
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {   
    $this->addOption('format', '%day%-%month%-%year%');
    $this->addOption('value_format', '%day%-%month%-%year%');
    
    // Set extended options for widget.
/*    foreach(self::$arOptionsExtNames as &$sOptionkey)
    {
      if (! empty($this->arOptionsExt[$sOptionkey]))
        $this->addOptionsExt($sOptionkey, $this->arOptionsExt[$sOptionkey]);
    }
*/

    // Set default values for extended options.
    // Years.
    $this->addOption('begin_year', (! empty($this->arOptionsExt['begin']['year']) ? $this->arOptionsExt['begin']['year'] : date('Y', strtotime('-100 years'))));
    $this->addOption('finish_year', (! empty($this->arOptionsExt['finish']['year']) ? $this->arOptionsExt['finish']['year'] : date('Y')));
    $this->addOption('order_year', (! empty($this->arOptionsExt['order']['year']) ? strtolower($this->arOptionsExt['order']['year']) : 'asc'));
    
    // Months.
    $this->addOption('months_label', (! empty($this->arOptionsExt['months']['label']) ? $this->arOptionsExt['months']['label'] : array()));
  }

  /**
   * @param  string $name        The element name
   * @param  string $value       The date displayed in this widget
   * @param  array  $attributes  An array of HTML attributes to be merged with the default HTML attributes
   * @param  array  $errors      An array of errors for the field
   *
   * @return string An HTML tag string
   *
   * @see sfWidgetForm
   */
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $default  = array('day' => '', 'month' => '', 'year' => '');
    $format   = $this->getOption('value_format');

    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }

    // Day select.
    $arDaysChoices = array_combine(range(1, 31), range(1, 31));
    $widget = new sfWidgetFormSelect(
                array('choices' => $arDaysChoices),
                array_merge(array(), $this->attributes, (isset($attributes['day']) ? $attributes['day'] : array()))
    );
    $dateWgt['%day%'] = $widget->render($name . '[day]', 0);

    // Month select.
    $arRange = range(1, 12);
    $arMonthsLabels = $this->getOption('months_label');
    if (! empty($arMonthsLabels) && 12 == sizeof($arMonthsLabels)) {
      $arMonthsChoices = array_combine($arRange, array_values($arMonthsLabels));
    }
    else {
      $arMonthsChoices = array_combine($arRange, $arRange);
    }
    
    $widget = new sfWidgetFormSelect(
                array('choices' => $arMonthsChoices),
                array_merge(array(), $this->attributes, (isset($attributes['month']) ? $attributes['month'] : array())));

    $dateWgt['%month%'] = $widget->render($name . '[month]', 0);

    // Year select.
    $arYearsChoices = array();
    $iBeginYear = $this->getOption('begin_year');
    $iFinishYear = $this->getOption('finish_year');
    for($i = $iBeginYear; $i <= $iFinishYear; $i++) $arYearsChoices[$i] = $i;
    if ('desc' == $this->getOption('order_year')) $arYearsChoices = array_reverse($arYearsChoices, true);
    
    $widget = new sfWidgetFormSelect(
                array('choices' => $arYearsChoices),
                array_merge(array(), $this->attributes, (isset($attributes['year']) ? $attributes['year'] : array()))
    );
    $dateWgt['%year%'] = $widget->render($name . '[year]', 0);
    
    // Return selects date.
    return strtr($this->getOption('format'), $dateWgt);
  }
}
