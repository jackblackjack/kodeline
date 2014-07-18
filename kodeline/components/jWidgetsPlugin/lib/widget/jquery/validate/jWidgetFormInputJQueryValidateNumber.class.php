<?php
/**
 * Widget for set value of number.
 * Use JQuery validate plugin.
 * 
 * @package     jWidgetsPlugin
 * @subpackage  jquery-widget-validate
 * @category    number
 * @link        http://plugins.jquery.com/validate/
 * 
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jWidgetFormInputJQueryValidateNumber extends sfWidgetFormInputText
{
  /**
   * Extended options.
   * @var array
   */
  protected $extended_widget_options = array(
    'is_decimal'    => false,
    'culture'       => 'en',
    'pattern'       => null,
    'conditional'   => null,
    // Uses css classes.
    'css_success'   => 'success',
    'css_error'     => 'error'
  );

  /**
   * Configure widget.
   *
   * @see sfWidgetForm
   */
  protected function configure($options = array(), $attributes = array())
  {
    // Set values of the default options.
    $arKeys = array_keys($this->extended_widget_options);
    $szKeys = count($arKeys);
    for($i = 0; $i < $szKeys; $i++) { $this->addOption($arKeys[$i], $this->extended_widget_options[$arKeys[$i]]); }

    // Configure.
    parent::configure($options, $attributes);

    // Set options list for widget.
    $this->setOptions(array_merge(($this->getOptions() + $this->extended_widget_options), $options));

    // Set culture.
    $this->setOption('culture', (sfContext::hasInstance() ? sfContext::getInstance()->getUser()->getCulture() : 'en'));

    // Set value of the option "template".
    $this->addOption('template', <<<EOF
%tag%
<script type="text/javascript">/* <[CDATA[ */
jQuery(document).ready(function() { jQuery.validateExtend(%rule%); });
/* ]]> */</script>
EOF
    );
  }

  /**
   * Renders the widget.
   * 
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
    // Generate id for field.
    $id = $this->generateId($name);

    //???$attributes['data-required']
    $attributes['data-validate'] = $id;

    return strtr($this->getOption('template'), array(
      '%tag%'               => parent::render($name, $value, $attributes, $errors),
      '%rule%'              => $this->getValidateRule($id)
    ));
  }

  /**
   * Generate rule for validate.
   * 
   * @return array
   */
  public function getValidateRule($widgetId)
  {
    // Define rules.
    $arRules = array();

    // Set conditional.
    if ($this->hasOption('conditional') && (null != $this->getOption('conditional'))) {
      $arRules['conditional'] = $this->getOption('conditional');
    }

    if ($this->getOption('is_decimal')) {
      $arRules['pattern'] = (null === $this->getOption('pattern') ? "new RegExp('/^[0-9\.,]+$/')" : $this->getOption('pattern'));
    }
    else {
      $arRules['pattern'] = (null === $this->getOption('pattern') ? "new RegExp('/^[0-9]+$/')" : $this->getOption('pattern'));
    }

    // Return rule as json string.
    return json_encode(array($widgetId => $arRules), JSON_UNESCAPED_SLASHES);
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array Merged array of javascript paths.
   */
  public function getJavaScripts()
  {
    return array_merge(parent::getJavascripts(), array('/jWidgetsPlugin/js/jquery/plugin/jquery.validate.min.js'));
  }

}
