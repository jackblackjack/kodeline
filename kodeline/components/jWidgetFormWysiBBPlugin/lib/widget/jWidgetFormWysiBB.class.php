<?php
/**
 * WysiBB Widget
 *
 * @package jWidgetFormWysiBBPlugin
 * @author Chugarev Alexey <chugarev@gmail.com>
 **/
class jWidgetFormWysiBB extends sfWidgetFormTextarea
{
  /**
   * Widget options for WysiBB editor.
   * @var array
   */
  protected $default_widget_options = array(
    'bbmode'            => false,
    'onlyBBmode'        => false,
    'themeName'         => "default", 
    'bodyClass'         => "",
    'lang'              => "ru",
    'tabInsert'         => true,
    'imgupload'         => false,
    'img_uploadurl'     => null,
    'img_maxwidth'      => 800,
    'img_maxheight'     => 800,
    'hotkeys'           => true,
    'showHotkeys'       => true,
    'autoresize'        => true,
    'resize_maxheight'  => 800,
    'loadPageStyles'    => true,
    'traceTextarea'     => true,
    'smileConversion'   => false,
    'buttons'           => "bold,italic,underline,strike,sup,sub,|,img,video,link,|,bullist,numlist,smilebox,|,fontcolor,fontsize,fontfamily,|,justifyleft,justifycenter,justifyright,|,quote,code,offtop,table,removeFormat",
    'allButtons'        => array(),
    'systr'             => array(),
    'customRules'       => array(),
    'smileList'         => array(),
    'attrWrap'          => array()
  );

  /**
   * Widget options for internal options.
   * @var array
   */
  protected $internal_widget_options = array(
    'wbb_options' => array('buttons' => "bold,italic,underline,|,img,link"),
    'wbb_path'    => '/jWidgetFormWysiBBPlugin/js/',
    'wbb_debug'   => false
  );

  /**
   * Constructor.
   *
   * Available options:
   *  * wbb_options     : Associative array of WysiBB options.
   *  * wbb_path        : Path to WysiBB editor scenario.
   *  * wbb_debug       : Flag to enable|disable debugging for editor.
   *
   * @see sfWidgetFormTextarea
   **/    
  protected function configure($options = array(), $attributes = array())
  {
    parent::configure($options, $attributes);

    /*
    $this->addOption('wbb_options', sfConfig::get('app_wysibb_default', array('buttons' => "bold,italic,underline,|,img,link")));
    $this->addOption('wbb_path', sfConfig::get('app_wysibb_path', '/jWidgetFormWysiBBPlugin/js/'));
    $this->addOption('wbb_debug', (bool) sfConfig::get('app_wysibb_debug', false));
    */

    // Set options list for widget.
    $this->setOptions(array_merge(($this->getOptions() + array_merge($this->default_widget_options, $this->internal_widget_options['wbb_options']) + $this->internal_widget_options), $options));

    // Set value of the option "template".
    $this->addOption('template', <<<EOF
%associated%
<script type="text/javascript">/* <[CDATA[ */
(function(){ if ('undefined' !== typeof(jQuery)) { jQuery(document).ready(function() { jQuery("#%id%").wysibb(%widget_config%); wbbdebug=%wbbdebug%; });}})();
/* ]]> */</script>
EOF
);
  }
  
  /**
   * @see sfWidget
   **/    
  public function render($name, $value = null, $attributes = array(), $errors = array())
  { 
    // Generate widget id.
    $widgetId = $this->generateId($name, $value);

    // Return template widget.
    return strtr($this->getOption('template'), array(
      '%id%'                      => $widgetId,
      '%widget_config%'           => $this->optionsToJavascriptObject($this->getOptions()),
      '%wbbdebug%'                => ($this->getOption('wbb_debug') ? 'true' : 'false'),
      '%associated%'              => parent::render($name, $value, $attributes, $errors)
    ));
  }

  /**
   * Return list of widget's options as json.
   * 
   * @param array $options Options for json object.
   * @return string
   */
  public function optionsToJavascriptObject($options)
  {
    // Prepare options for remove nulled options.
    $options = array_filter(array_intersect_key($options, $this->default_widget_options), function($value) { 
      return ((is_string($value) && '' !== trim($value)) || is_bool($value) || (is_array($value) && count($value)));
    });

    // Convert array to compatible javascript json configuration.
    $_self = &$this;
    return '{' . implode(',', array_map(function($k, $v) use($_self) { 
                                return sprintf('"%s":%s', $k, $_self->jsonEscape($v)); 
                              }, 
            array_keys($options), array_values($options))) . '}';
  }

  /**
   * Escapes a string for json config
   *
   * @param  string $value  string to escape
   * @return string escaped string
   */
  public static function jsonEscape($value)
  {
    // Recursive call if preparing value its array.
    if (is_array($value)) { 
      return '{' . implode(',', array_map(function($k, $v) { return sprintf('"%s":%s', $k, static::jsonEscape($v)); }, array_keys($value), array_values($value))) . '}';
    }

    // Boolean php-values to boolean javascript-values.
    if (is_bool($value)) return ($value ? 'true' : 'false');

    // Clean string if its function.
    if (preg_match('/(?<=)function(?:\s+)?\((?:(?!}").)*}/si', $value)) {
      return sprintf('%s', str_replace(array('\n','\t','\"', '\\\\','\\/'), array('','','"','\\','/'), $value));
    }

    // Return escaped string value.
    return sprintf('"%s"', self::fixDoubleEscape(htmlspecialchars((string) $value, ENT_QUOTES, self::getCharset())));   
  }

  /**
   * @see sfWidget
   */
  public function getStylesheets()
  {
    return array_unique(array_merge(
        parent::getStylesheets(),
        array($this->getOption('wbb_path') . '/theme/default/wbbtheme.css' => 'all')
      )
    );
  }

  /**
   * @see sfWidget
   */
  public function getJavaScripts()
  {
    return array_unique(array_merge(
        parent::getJavaScripts(),
        array($this->getOption('wbb_path') . '/jquery.wysibb.min.js')
      )
    );
  }
}