<?php
/**
 * jWidgetJQueryPhoneNumber
 *
 * @package     yatutu
 * @subpackage  lib.helper
 * @author      pinhead
 * @version     SVN: $Id: yaWidgetPhoneNumber.class.php 2419 2010-10-16 08:56:54Z chugarev $
 */
class jWidgetFormInputPhoneNumber extends sfWidgetForm
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
    $this->addOption('format', '%country% %area% %number%');
    $this->addOption('value_format', '+%country%(%area%)%number%');
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
    $default  = array('country' => '', 'area' => '', 'number' => '');
    $format   = $this->getOption('value_format');

    if (is_array($value))
    {
      $value = array_merge($default, $value);
    }
    else
    {
      $regexp = strtr('/^' . preg_quote($format, '/\\') . '$/', array('%country%' => '(?<country>[0-9]+)', '%area%' => '(?<area>[0-9]+)', '%number%' => '(?<number>[0-9]+)'));

      $matches = array();
      preg_match($regexp, $value, $matches);

      $value = array_merge($default, array_intersect_key($matches, $default));
    }

    // Country.
    $widget = new sfWidgetFormInputText(array(), 
            array_merge(array('size' => 5, 'maxlength' => 5, 'style' => 'width: 10px;'), 
            $this->attributes, isset($attributes['country']) ? $attributes['country'] : array() )
    );
    $phone['%country%'] = $widget->render($name.'[country]', $value['country']);

    // Area.
    $widget = new sfWidgetFormInputText(array(), array_merge(array('size' => 5, 'maxlength' => 5, 'style' => 'width: 30px;'), $this->attributes, isset($attributes['area']) ? $attributes['area'] : array() ));
    $phone['%area%'] = $widget->render($name.'[area]', $value['area']);

    // Number.
    $widget = new sfWidgetFormInputText(array(), array_merge(array('size' => 10, 'maxlength' => 10, 'style' => 'width: 110px;'), $this->attributes, isset($attributes['number']) ? $attributes['number'] : array() ));
    $phone['%number%'] = $widget->render($name.'[number]', $value['number']);

    return strtr($this->getOption('format'), $phone);
  }
}

