<?php
/**
 * jValidatorExtended
 *
 * @package     jWidgetsPlugin
 * @category    validator
 * 
 * @author      chugarev
 * @version     $Id$
 */
abstract class jValidatorExtended extends sfValidatorBase
{ 
  /**
   * List of extended options.
   * @staticvar array
   */
  protected $arOptionsExtended = array();
  
  /**
   * @inheritDoc
   */
  public function __construct($options = array(), $messages = array())
  {    
    $arDefaultOptions = array();
    if (! empty($this->arOptionsExtended))
    {
      $arExtendedKeys = array_keys($this->arOptionsExtended);
      $arExtendedOptions = array_combine($arExtendedKeys, $arExtendedKeys);
      
      $this->arOptionsExtended = array_merge($this->arOptionsExtended, $options);
      $arDefaultOptions = array_diff_key($options, $arExtendedOptions);
    }

    // Call constructor with standart options.
    parent::__construct($arDefaultOptions, $messages);
  }

  /**
   * @inheritDoc
   */
  protected function configure($options = array(), $messages = array())
  {
    // Set extended options for widget.
    foreach($this->arOptionsExtended as $key => $value)
    {
      $this->addOption($key, $value);
    }
    
    // Call constructor with standart options.
    parent::configure($options, $messages);
  }
}

