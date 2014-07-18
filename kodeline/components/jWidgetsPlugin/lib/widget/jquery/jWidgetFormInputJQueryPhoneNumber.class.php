<?php
/**
 * jWidgetFormInputJqPhoneNumber
 *
 * @package     jWidgetsPlugin
 * @category    widget
 * 
 * @author      chugarev
 * @version     $Id$
 */
class jWidgetFormInputJQueryPhoneNumber extends jWidgetExtended
{
  /**
   * @inheritDoc
   */
  protected $arOptionsExtended = array(
      'format'        => '%country% %area% %number%',
      'value_format'  => '+%country%(%area%)%number%'
  );
  
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
            array_merge(array('size' => 5, 'maxlength' => 5), 
            $this->attributes, isset($attributes['country']) ? $attributes['country'] : array() )
    );
    $phone['%country%'] = $widget->render($name.'[country]', $value['country']);

    // Area.
    $widget = new sfWidgetFormInputText(array(),
            array_merge(array('size' => 5, 'maxlength' => 5),
            $this->attributes, isset($attributes['area']) ? $attributes['area'] : array() ));
    $phone['%area%'] = $widget->render($name.'[area]', $value['area']);

    // Number.
    $widget = new sfWidgetFormInputText(array(), 
            array_merge(array('size' => 10, 'maxlength' => 10),
            $this->attributes, isset($attributes['number']) ? $attributes['number'] : array() ));
    $phone['%number%'] = $widget->render($name.'[number]', $value['number']);

    return strtr($this->getOption('format'), $phone);
  }
  
  /**
   * @inheritDoc
   */
  public function getJavascripts()
  {
    return array_merge(parent::getJavascripts(),
      array(
        'jwidgets/jquery.validate'
      )
    );
  }
}

