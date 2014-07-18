<?php

/**
 * yaValidatorFloat validates an foat. It also converts the input value to an float.
 *
 * @package    symfony
 * @subpackage validator
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorFloat.class.php 694 2009-08-26 12:53:10Z pinhead $
 */
class yaValidatorFloat extends sfValidatorBase
{
  /**
   * Configures the current validator.
   *
   * Available options:
   *
   *  * max: The maximum value allowed
   *  * min: The minimum value allowed
   *
   * Available error codes:
   *
   *  * max
   *  * min
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max', '"%value%" must be less than %max%.');
    $this->addMessage('min', '"%value%" must be greater than %min%.');
    $this->addMessage('precision', '"%value%" precision must be %precision%.');

    $this->addOption('min');
    $this->addOption('max');
    $this->addOption('precision');

    $this->setMessage('invalid', '"%value%" is not an float.');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $clean = floatval($value);

    if (strval($clean) != $value)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    
    if (strval(round($clean, $this->getOption('precision'))) != strval($clean))
    {
      throw new sfValidatorError($this, 'precision', array('value' => $value));
    }
    
    if ($this->hasOption('max') && $clean > floatval($this->getOption('max')))
    {
      throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $this->getOption('max')));
    }

    if ($this->hasOption('min') && $clean < floatval($this->getOption('min')))
    {
      throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $this->getOption('min')));
    }

    return $clean;
  }
}
