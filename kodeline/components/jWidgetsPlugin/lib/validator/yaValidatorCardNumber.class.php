<?php

/**
 * yaValidatorCardNumber validates an credit/debit card number agains LUHN algo.
 *
 * @package    symfony
 * @subpackage validator
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorCardNumber.class.php 3570 2011-03-12 14:51:11Z pinhead $
 */
class yaValidatorCardNumber extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *
   * Available error codes:
   *
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->setMessage('invalid', 'invalid card number');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $clean = trim($value);

    if (!self::validateCardNumber($clean))
    {
      throw new sfValidatorError($this, 'invalid', array());
    }

    return $clean;
  }

  /**
   * Validate card number using LUHN mod 10 algorithm
   *
   * @param string $number - card number
   *
   * @return bool
   */
  public static function validateCardNumber($cardNumber)
  {
    if (empty($cardNumber))
      return false;

    $number = preg_replace('/[^0-9]+/', '', substr($cardNumber, 0, 30));
    if (strcmp($number, $cardNumber) != 0)
    {
      return false;
    }

    $length = strlen($number);

    //  start the Mod10 checksum process...
    $checksum = 0;
    for ($i = 1 - ($length % 2); $i < $length; $i += 2)
      $checksum += substr($number, $i, 1);

    // Analyze odd digits in even length strings
    // or even digits in odd length strings.
    for ($i = ($length % 2); $i < $length; $i += 2)
    {
      $digit = substr($number, $i, 1) * 2;
      if ($digit < 10)
        $checksum += $digit;
      else
        $checksum += $digit - 9;
    }

    // If the checksum is divisible by 10, the number passes.
    if ($checksum % 10 == 0)
      return true;
    else
      return false;
  }
}
