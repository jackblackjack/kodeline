<?php
/**
 * jValidatorPhoneNumber
 *
 * @package     jWidgetsPlugin
 * @category    validator
 * 
 * @author      chugarev
 * @version     $Id$
 */
class jValidatorPhoneNumber extends jValidatorExtended
{
  /**
   * @inheritDoc
   */
  protected $arOptionsExtended = array(
      'value_format'      => '+%country%(%area%)%number%',
      'country_maxlength' => 5,
      'country_minlength' => 1,
      'area_maxlength'    => 5,
      'area_minlength'    => 1,
      'number_maxlength'  => 10,
      'number_minlength'  => 1
  );
  
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * choices: An array of expected values (required)
   *
   * @param array $options    An array of options
   * @param array $messages   An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {   
    $this->addMessage('invalid_country', 'Please enter correct country code.');
    $this->addMessage('required_country', 'Please enter country code.');
    $this->addMessage('invalid_area', 'Please enter correct area code.');
    $this->addMessage('required_area', 'Please enter area code.');
    $this->addMessage('invalid_number', 'Please enter correct phone number.');
    $this->addMessage('required_number', 'Please enter phone number.');
    
    // Call constructor with standart options.
    parent::configure($options, $messages);
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $format = $this->getOption('value_format');
    $empty  = array('country' => '', 'area' => '', 'number' => '');

    if (! is_array($value))
    {
      $regexp = strtr('/^' . preg_quote($format, '()[]$^.*/\\<>{}') . '$/', array('%country%' => '(?<country>.*)', '%area%' => '(?<area>.*)', '%number%' => '(?<number>.*)'));
      $matches = array();
      preg_match($regexp, $value, $matches);
      $value = array_merge($empty, array_intersect_key($matches, $empty));
    }

    foreach ($value as $key => $v)
    {
      $value[$key] = preg_replace('/\D/i', '', $v);
    }

    if (empty($value['country']) && empty($value['area']) && empty($value['number']))
    {
      if ($this->getOption('required'))
      {
        throw new sfValidatorError($this, 'required');
      }
      return null;
    }

    if (! preg_match(sprintf('/^(\d{%s,%s})$/', $this->getOption('country_minlength'), $this->getOption('country_maxlength')), $value['country']))
    {
      throw new sfValidatorError($this, 'invalid_country', array('value' => $value['country']));
    }

    if (! preg_match(sprintf('/^(\d{%s,%s})$/', $this->getOption('area_minlength'), $this->getOption('area_maxlength')), $value['area'], $matches))
    {
      throw new sfValidatorError($this, 'invalid_area', array('value' => $value['area']));
    }

    if (! preg_match(sprintf('/^(\d{%s,%s})$/', $this->getOption('number_minlength'), $this->getOption('number_maxlength')), $value['number'], $matches))
    {
      throw new sfValidatorError($this, 'invalid_number', array('value' => $value['number']));
    }

    return strtr($format, array(
      '%country%' => $value['country'],
      '%area%'    => $value['area'],
      '%number%'  => $value['number']
    ));
  }

}
