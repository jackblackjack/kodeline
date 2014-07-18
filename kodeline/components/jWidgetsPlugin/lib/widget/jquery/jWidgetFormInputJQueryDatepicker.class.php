<?php
/**
 * Widget for set value of date to field.
 * Use JQuery ui datepicker.
 * 
 * @package     jWidgetsPlugin
 * @subpackage  jquery-widget
 * @category    date
 * @link        http://jqueryui.com/datepicker/
 * 
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jWidgetFormInputJQueryDatepicker extends sfWidgetForm
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
    $this->addOption('change_month',  false);
    $this->addOption('change_year',  false);
    $this->addOption('number_of_months', 1);
    $this->addOption('show_button_panel',  false);

    $this->addOption('name',  null);
    $this->addOption('config', '{}');

    $this->addOption('image', false);
    $this->addOption('theme', '/jWidgetsPlugin/css/ui-lightness/jquery-ui.css');

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
    if ('en' != $culture)
    {
      $scriptCode = <<<EOF
jQuery(document).ready(function() {
  if ('undefined' != typeof(jQuery.datepicker.regional['$culture'])) { jQuery("#$id").datepicker(jQuery.merge($arDatepickerConfig, jQuery.datepicker.regional['$culture'])); }
  else { jQuery("#$id").datepicker($arDatepickerConfig); }
});
EOF;
    }
    else
    {
      $scriptCode = 'jQuery(document).ready(function() { jQuery("#' . $id . '").datepicker(' . $arDatepickerConfig . '); });';
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

    // Define iconf of datepiecker.
    $datepickerImage = '';
    if (false !== $this->getOption('image'))
    {
      $arConfig['buttonImage'] = sprintf('"%s"', $this->getOption('image'));
      $arConfig['buttonImageOnly'] = true;
    }

    $arConfig['changeMonth'] = $this->getOption("change_month") ? true : false;
    $arConfig['changeYear'] = $this->getOption("change_year") ? true : false;
    $arConfig['numberOfMonths'] = $this->getOption("number_of_months");
    $arConfig['showButtonPanel'] = $this->getOption("show_button_panel") ? true : false;

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

    $i18nFilePath = '/jWidgetsPlugin/js/jquery/i18n/datepicker/ui.datepicker-' . $this->getOption('culture') . '.js';
    if ('en' != $this->getOption('culture') && file_exists(sfConfig::get('sf_web_dir') . $i18nFilePath)) {
      $arJavascript[] = $i18nFilePath;
    }
    
    return array_merge(parent::getJavascripts(), $arJavascript);
  }

}
