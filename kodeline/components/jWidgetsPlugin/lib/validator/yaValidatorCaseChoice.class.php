<?php

/**
 * yaValidatorCaseChoice case-insensitive validation than the value is one of the expected values.
 *
 * @package    yatutu
 * @subpackage validator
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorCaseChoice.class.php 694 2009-08-26 12:53:10Z pinhead $
 */
class yaValidatorCaseChoice extends sfValidatorChoice
{

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $choices = $this->getOption('choices');
    if ($choices instanceof sfCallable)
    {
      $choices = $choices->call();
    }

    if ($this->getOption('multiple'))
    {
      if (!is_array($value))
      {
        $value = array($value);
      }

      foreach ($value as $v)
      {
        if (!self::inChoices($v, $choices))
        {
          throw new sfValidatorError($this, 'invalid', array('value' => $v));
        }
      }
    }
    else
    {
      if (!self::inChoices($value, $choices))
      {
        throw new sfValidatorError($this, 'invalid', array('value' => $value));
      }
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
      if (strtolower((string) $choice) == strtolower((string) $value))
      {
        return true;
      }
    }

    return false;
  }
}
