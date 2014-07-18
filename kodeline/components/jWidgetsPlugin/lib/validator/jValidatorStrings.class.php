<?php
/**
 * jValidatorStrings validates  a list of strings.
 *
 * @package     jWidgetsPlugin
 * @category    validator
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jValidatorStrings extends sfValidatorBase
{
  /**
   * Configures validator.
   *
   * @param array $options   An array of options
   * @param array $messages  An array of error messages
   *
   * @see sfValidatorBase
   */
  protected function configure($options = array(), $messages = array())
  {
    $this->addMessage('max_length', '"%value%" is too long (%max_length% characters max).');
    $this->addMessage('min_length', '"%value%" is too short (%min_length% characters min).');

    $this->addOption('max_length');
    $this->addOption('min_length');

    $this->setOption('empty_value', '');
    $this->setMessage('invalid', 'invalid strings list');
  }

  /**
   * @see sfValidatorBase
   */
  protected function doClean($value)
  {
    if (! is_array($value)) $value = array($value);

    // Define string validator.
    $stringValidator = new sfValidatorString($this->getOptions(), $this->getMessages());

    $clean = array();
    $arKeys = array_keys($value);
    $szKeys = count($arKeys);
    for ($i = 0; $i < $szKeys; $i++)
    {
      $clean[$arKeys[$i]] = $stringValidator->clean($value[$arKeys[$i]]);
    }

    return $clean;
  }
}
