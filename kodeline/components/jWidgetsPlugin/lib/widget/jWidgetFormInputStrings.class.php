<?php
/**
 * jWidgetFormInputStrings
 * widget represents an inputs with the possibility of dynamic adding values.
 * 
 * @package   jWidgetsPlugin
 * @category  widget
 * @author    chugarev@gmail.com
 * @version   $Id$
 */
class jWidgetFormInputStrings extends sfWidgetForm
{ 
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
    $this->addOption('separator', '<br />');
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
    // Fix value format.
    if (! is_array($value)) $value = array($value);

    // Generate inputs.
    $widget = new sfWidgetFormInputText(array(), array());
    $widgets = array($widget->render($name.'[]', array_shift($value)));

    // Include JavascriptBase helper.
    sfProjectConfiguration::getActive()->loadHelpers(array('JavascriptBase'));

    foreach ($value as $val)
    {
      $widget = new sfWidgetFormInputText(array(), array());
      $widgets[] = $widget->render($name.'[]', $val);
    }

    // Generate javascript wrapper.
    $javascriptCode = javascript_tag(sprintf('jQuery(document).ready(function() { inputStringsJsHelper.init("%s") });', $this->generateId($name)));

    // Add last empty input
    $widget = new sfWidgetFormInputText(array(), array());
    $widgets[] = sprintf('%s %s', $widget->render($name.'[]'), $javascriptCode);
    
    // Return widgets.    
    return implode($this->getOption('separator'), $widgets);
  }

  /**
   * Gets the stylesheet paths associated with the widget.
   *
   * @return array An array of stylesheet paths
   */
  public function getStylesheets1()
  {
    //return array('/jWidgetsPlugin/js/jwidgets/select2/select2.css' => 'all');
  }

  /**
   * Gets the JavaScript paths associated with the widget.
   *
   * @return array An array of JavaScript paths
   */
  public function getJavaScripts()
  {
    return array_merge(parent::getJavascripts(), array(
      '//yandex.st/jquery/2.0.3/jquery.min.js',
      '/jWidgetsPlugin/js/inputstrings.js'
    ));
  }
}

