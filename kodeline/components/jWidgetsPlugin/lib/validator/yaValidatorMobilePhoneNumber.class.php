<?php

/**
 * yaValidatorMobilePhoneNumber
 *
 * @package    yaWidgetsPlugin
 * @subpackage validator
 * @author     pinhead
 * @version    SVN: $Id: yaValidatorMobilePhoneNumber.class.php 711 2009-09-04 18:29:47Z pinhead $
 */
class yaValidatorMobilePhoneNumber extends sfValidatorBase
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
    $this->addOption('delimiter', '-');
    $this->addOption('country_maxlength', 5);
    $this->addOption('country_minlength', 1);
    $this->addOption('number_maxlength', 20);
    $this->addOption('number_minlength', 3);

    $this->addMessage('invalid_country', 'Invalid country code.');
    $this->addMessage('invalid_number', 'Invalid phone number.');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    $matches = array();

    foreach ($value as $key => $v)
    {
      $value[$key] = str_replace(' ', '', $v);
    }

    if (empty($value['country']) || empty($value['number']))
    {
      if ($this->getOption('required'))
      {
        throw new sfValidatorError($this, 'required');
      }
      else
      {
        $value = '';
      }
    }
    else
    {
      if (preg_match('/^(\+?)([0-9]{' . $this->getOption('country_minlength') . ',' . $this->getOption('country_maxlength') . '})$/', $value['country'], $matches))
      {
        if ($matches[1] != '+')
        {
          $value['country'] = '+' . $matches[2];
        }
      }
      else
      {
        throw new sfValidatorError($this, 'invalid_country', array('value' => $value['country']));
      }

      if (!preg_match('/^([0-9]{' . $this->getOption('number_minlength') . ',' . $this->getOption('number_maxlength') . '})$/', $value['number'], $matches))
      {
        throw new sfValidatorError($this, 'invalid_number', array('value' => $value['number']));
      }

      $value = implode($this->getOption('delimiter'), $value);
    }

    return $value;
  }

}
