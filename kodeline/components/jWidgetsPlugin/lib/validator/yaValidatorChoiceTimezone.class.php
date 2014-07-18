<?php

/**
 * yaValidatorChoiceTimezone validates than the value is a valid timezone.
 *
 * @package     yaWidgetsPlugin
 * @subpackage  lib.validator
 * @author      pinhead
 * @version     SVN: $Id: yaValidatorChoiceTimezone.class.php 2775 2010-12-18 00:14:15Z pinhead $
 */
class yaValidatorChoiceTimezone extends sfValidatorChoice
{
  /**
   * Configures the current validator.
   * @see sfValidatorChoice
   */
  protected function configure($options = array(), $messages = array())
  {
    parent::configure($options, $messages);

    $this->setOption('choices', DateTimeZone::listIdentifiers());
  }
}
