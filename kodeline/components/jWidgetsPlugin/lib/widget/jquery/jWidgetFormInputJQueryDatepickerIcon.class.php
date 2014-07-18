<?php
/**
 * Text form input contained JQ calendar object.
 *
 * Supported extended options: javascript, icon, calendar
 * <code>
 *   'javascript' => array(
 *    'subscribe' => array('selectevent' => 'handleDate')
 *   ),
 *   'icon' => array('
 *    'style'   => 'z-index: 1',
 *    'class'   => '',
 *   ),
 *   'calendar' => array('name' => 'yuical')
 * </code>
 *
 * Require yui javascript:
 *   jquery/jquery-1.4.4.min.js',
 *   jquery/jquery-ui-1.8.8.custom.min.js'
 * 
 *
 * Require JQ stylesheet: datepicker.css
 *
 * @package     yaWidgetsPlugin
 * @subpackage  widgets
 * @author      step
 * @version     $Id$
 */
class jWidgetFormInputJQueryDatepickerIcon extends sfWidgetFormInputText
{
  /**
   * YUI calendar name.
   *
   * @var string
   */
  protected $sCalendarName = '';

  /**
   * List of extended options.
   * @staticvar array
   */
  protected static $arOptionsExtNames = array('javascript', 'icon', 'calendar', 'config', 'culture');

  /**
   * List of values extended options.
   * @var array
   */
  protected $arOptionsExt = array();

  /**
   * @inheritDoc
   */
  public function  getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array('jquery/common.css' => 'screen'));
  }

  /**
   * @inheritDoc
   */
  public function getJavascripts()
  {
    return array_merge(parent::getJavascripts(),
      array(
        '//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js',
        '//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js'
      )
    );
  }

  /**
   * @inheritDoc
   */
  public function __construct($options = array(), $attributes = array())
  {
    // Set format date option value.
    $this->addOption('format', '%month%/%day%/%year%');

    // Define extended options for input.
    $arInputOptions = array_combine(self::$arOptionsExtNames, self::$arOptionsExtNames);
    $this->arOptionsExt = array_intersect_key($options, $arInputOptions);

    // Set for constructor standart options.
    parent::__construct(array_diff_key($options, $arInputOptions), $attributes);

    if ('en' == $this->getOption('culture'))
    {
      $this->setOption('culture', 'en');
    }
  }

  /**
   * {@inheritDoc}
   */
  protected function configure($options = array(), $attributes = array())
  {
    // Set extended options for widget.
    foreach(self::$arOptionsExtNames as &$sOptionkey)
    {
      if (! empty($this->arOptionsExt[$sOptionkey]))
      {
        $this->addOptionsExt($sOptionkey, $this->arOptionsExt[$sOptionkey]);
      }
    }

    // Set default values for extended options.
    $this->addOption('icon_style', (! empty($this->arOptionsExt['icon']['style']) ? $this->arOptionsExt['icon']['style'] : null));
    $this->addOption('icon_class', (! empty($this->arOptionsExt['icon']['class']) ? $this->arOptionsExt['icon']['class'] : null));
  }

  /**
   * Recursive method for adding options
   *
   * @param string $keyPrefix Option key name.
   * @param array $arValues Array values.
   */
  protected function addOptionsExt($keyPrefix, $arValues)
  {
    $szValues = count($arValues);
    for ($i = 0; $i < $szValues; $i++)
    {
      list($key, $value) = each($arValues);
      $curPrefix = $keyPrefix . '_' . $key;

      if (is_array($value))
      {
        $this->addOptionsExt($curPrefix, $value);
      }
      else {
        $this->addOption($curPrefix, $value);
      }
    }
  }

  /**
   * Return extended options names for widget.
   *
   * @return array
   */
  public static function getOptionsExtended()
  {
    return self::$arOptionsExtNames;
  }

  /**
   * @inheritDoc
   */
  public function renderTag($tag, $attributes = array())
  {
    if (empty($tag))
    {
      return '';
    }

    // Define unique calendar id.
    $sUniqId = $this->getCalendarName();

    return sprintf('<%s%s%s<img src="/images/ico-cal.png" width="16" height="14" %s onclick="$(\'#%s\').datepicker(\'show\')" id="%s"%s%s<span class="date">mm/dd/yy</span>%s',
            $tag, $this->attributesToHtml($attributes),
            self::$xhtml ? ' />' : (strtolower($tag) == 'input' ? '>' : sprintf('></%s>', $tag)),
            (! ($sClass = $this->getOption('icon_class')) ? '' : ' class="' . $sClass . '"'),
            $sUniqId,
            $sUniqId . 'but',
            (! ($sStyle = $this->getOption('icon_style')) ? '' : ' style="' . $sStyle . '"'),
            (self::$xhtml ? ' />' : '>'),
            $this->getTagJavaScript($sUniqId, $attributes)
           );
  }

  /**
   * Generate and return yui calendar name.
   *
   * @return string
   */
  public function getCalendarName()
  {
    if (strlen($this->getOption('calendar_name'))) return $this->getOption('calendar_name');

    if (strlen($this->getAttribute('name')))
    {
      $this->sCalendarName = preg_replace('/_+/', '_', preg_replace('/[^\w]/', '_', $this->getAttribute('name') . '_'));
      return $this->sCalendarName;
    }

    $this->sCalendarName = $this->generateId(md5(time()));
    return $this->sCalendarName;
  }

  /**
   * Generate javascript for initiate yui calendar for widget.
   *
   * @return string
   */
  protected function getTagJavaScript($sUniqId, $arAttributes = array())
  {
    // Configure calendar config.
    $arCalendarConfig = array();
    if ($this->hasOption('calendar_min_date'))
    {
      $arCalendarConfig['minDate'] = $this->getOption('calendar_min_date');
    }
    
    if ($this->hasOption('calendar_max_date'))
    {
      $arCalendarConfig['maxDate'] = $this->getOption('calendar_max_date');
    }

    $sJavascriptCode = "$('#".$sUniqId."').datepicker(".json_encode($arCalendarConfig).");";
   
    // Set subscribe to yui calendar events.
    if (($value = $this->getOption('javascript_subscribe_selectevent')))
      // $sJavascriptCode .= PHP_EOL . "YAHOO.{$sUniqId}.cal.selectEvent.subscribe({$value}, YAHOO.{$sUniqId}.cal, true);";

    sfProjectConfiguration::getActive()->loadHelpers(array('JavascriptBase'));
    return javascript_tag($sJavascriptCode);
  }
}

