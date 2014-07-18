<?php

/**
 * yaValidatorMonthYear validates than the value is one of the expected values.
 *
 * @package    symfony
 * @subpackage validator
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorMonthYear.class.php 1395 2010-03-25 19:29:49Z pinhead $
 */
class yaValidatorMonthYear extends sfValidatorBase
{
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
      $this->addOption('delimiter', '/');
      $this->addRequiredOption('years');

      $this->addMessage('invalid_year', 'Invalid year value "%value%"');
      $this->addMessage('invalid_month', 'Invalid month value "%value%"');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (! is_array($value))
    {
      $value = explode($this->getOption('delimiter'), $value);
      $value = array(
        'month' => isset($value[0]) && isset($value[1]) ? $value[0] : '',
        'year'  => isset($value[0]) && isset($value[1]) ? $value[1] : ''
      );
    }

    $year_choices = $this->getOption('years');

    if ($year_choices instanceof sfCallable)
    {
      $year_choices = $year_choices->call();
    }

    $year = intval($value['year']);
    $month = intval($value['month']);

    if ($this->getOption('required') && (empty($value['year']) || empty($value['month'])))
    {
    	throw new sfValidatorError($this, 'required', array('value' => $value));
	  }

	  if ($year > 0 && (($year != $value['year']) || (! self::inChoices($year, $year_choices))))
    {
    	throw new sfValidatorError($this, 'invalid_year', array('value' => $value['year']));
	  }

    if ($month > 0 && (($month != $value['month']) || ($month < date('m') && $year == date('Y')) || ($month < 1 || $month > 12)))
    {
    	throw new sfValidatorError($this, 'invalid_month', array('value' => $value['month']));
	  }

    return $value;
  }

  /**
   * Checks if a value is part of given choices (see bug #4212)
   *
   * @param  mixed $value   The value to check
   * @param  array $choices The array of available choices
   *
   * @return Boolean
   */
  static protected function inChoices($value, array $choices = array())
  {
    foreach ($choices as $choice)
    {
      if ((string) $choice === (string) $value)
      {
        return true;
      }
    }

    return false;
  }
}
