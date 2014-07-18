<?php
/**
 * Widget for set value of date to field.
 * Use JQuery ui datepicker.
 * 
 * @package     jWidgetsPlugin
 * @subpackage  jquery-widget
 * @category    date
 * @link        http://trentrichardson.com/examples/timepicker/
 * 
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jWidgetFormInputJQueryTimepicker extends sfWidgetForm
{
  /**
   * Configures the current widget.
   *
   * Available options:
   *
   * @param string   culture           Sets culture for the widget
   * @param boolean  change_month      If date chooser attached to widget has month select dropdown, defaults to false
   * @param boolean  change_year       If date chooser attached to widget has year select dropdown, defaults to false
   * @param integer  number_of_months  Number of months visible in date chooser, defaults to 1
   * @param boolean  show_button_panel If date chooser shows panel with 'today' and 'done' buttons, defaults to false
   * @param string   theme             css theme for jquery ui interface, defaults to '/sfJQueryUIPlugin/css/ui-lightness/jquery-ui.css'
   * 
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    $this->addOption('culture', (sfContext::hasInstance() ? sfContext::getInstance()->getUser()->getCulture() : 'en'));

    $this->addOption('hourGrid',  4);
    $this->addOption('minuteGrid',  10);
    //$this->addOption('timeFormat',  "hh:mm tt");

    $this->addOption('stepMinute',  1);
    $this->addOption('show_date', true);
    $this->addOption('showButtonPanel', true);
    $this->addOption('config', '{}');

    $this->addOption('image', false);
    $this->addOption('theme', '/jWidgetsPlugin/css/timepicker/jquery-ui-timepicker-addon.css');

    parent::configure($options, $attributes);
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
    // Define input object.
    $attributes = $this->getAttributes();

    $input = new sfWidgetFormInput(array(), $attributes);
    $html = $input->render($name, $value);
    $id = $input->generateId($name);

    // Define datepicker config.
    $arDatepickerConfig = json_encode($this->getDatepickerConfig());

    $culture = $this->getOption('culture');
    $scriptCode = '';

    if ($this->getOption('show_date', true))
    {
      $scriptCode = <<<EOF
jQuery(document).ready(function() {
  if ('undefined' != typeof(jQuery.timepicker.regional['$culture'])) { jQuery("#$id").datetimepicker(jQuery.merge($arDatepickerConfig, jQuery.timepicker.regional['$culture'])); }
  else { jQuery("#$id").datetimepicker($arDatepickerConfig); }
});
EOF;
    }
    else
    {
      $scriptCode = <<<EOF
jQuery(document).ready(function() {
  if ('undefined' != typeof(jQuery.timepicker.regional['$culture'])) { jQuery("#$id").timepicker(jQuery.merge($arDatepickerConfig, jQuery.timepicker.regional['$culture'])); }
  else { jQuery("#$id").timepicker($arDatepickerConfig); }
});
EOF;
    }

    sfProjectConfiguration::getActive()->loadHelpers(array('JavascriptBase'));
    return sprintf('%s%s', $html, javascript_tag($scriptCode));
  }

  /**
   * Generate datepicker.
   * 
   * @return array
   */
  public function getDatepickerConfig()
  {
    $arConfig = array();

    $arConfig['hourGrid'] = $this->getOption("hourGrid");
    $arConfig['minuteGrid'] = $this->getOption("minuteGrid");
    $arConfig['stepMinute'] = $this->getOption("stepMinute");
    //$arConfig['timeFormat'] = $this->getOption("timeFormat");

    $arConfig['showButtonPanel'] = $this->getOption("showButtonPanel") ? "true" : "false";

    return array_merge($arConfig, json_decode($this->getOption("config"), true));
  }

  /*
   *
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets()
  {
    return array_merge(parent::getStylesheets(), array($this->getOption('theme') => 'screen'));
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    $arJavascript = array();

    $sliderFilePath = '/jWidgetsPlugin/js/jquery/plugin/jquery-ui-sliderAccess.js';
    if (file_exists(sfConfig::get('sf_web_dir') . $sliderFilePath))
    {
      $arJavascript[] = $sliderFilePath;
    }

    $addonFilePath = '/jWidgetsPlugin/js/jquery/plugin/jquery-ui-timepicker-addon.js';
    if (file_exists(sfConfig::get('sf_web_dir') . $sliderFilePath))
    {
      $arJavascript[] = $addonFilePath;
    }

    $i18nFilePath = '/jWidgetsPlugin/js/jquery/i18n/timepicker/jquery-ui-timepicker-' . $this->getOption('culture') . '.js';
    if ('en' != $this->getOption('culture') && file_exists(sfConfig::get('sf_web_dir') . $i18nFilePath))
    {
      $arJavascript[] = $i18nFilePath;
    }
    
    return array_merge(parent::getJavascripts(), $arJavascript);
  }
}
