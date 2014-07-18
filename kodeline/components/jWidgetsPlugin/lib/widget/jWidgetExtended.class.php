<?php
/**
 * jWidgetExtended
 *
 * @package     jWidgetsPlugin
 * @category    widget
 * 
 * @author      chugarev
 * @version     $Id$
 */
abstract class jWidgetExtended extends sfWidgetForm
{ 
  /**
   * List of extended options.
   * @staticvar array
   */
  protected $arOptionsExtended = array();
  
  /**
   * @inheritDoc
   */
  public function __construct($options = array(), $attributes = array())
  {    
    $arWidgetOptions = array();
    if (! empty($this->arOptionsExtended))
    {
      $arExtendedKeys = array_keys($this->arOptionsExtended);
      $arExtendedOptions = array_combine($arExtendedKeys, $arExtendedKeys);
      
      $this->arOptionsExtended = array_merge($this->arOptionsExtended, $options);
      $arWidgetOptions = array_diff_key($options, $arExtendedOptions);
    }

    // Call constructor with standart options.
    parent::__construct($arWidgetOptions, $attributes);
  }

  /**
   * @inheritDoc
   */
  protected function configure($options = array(), $attributes = array())
  {
    // Set extended options for widget.
    foreach($this->arOptionsExtended as $key => $value)
    {
      $this->addOption($key, $value);
    }
    
    // Call constructor with standart options.
    parent::configure($options, $attributes);
  }
  
  /**
   * Recursive method for adding options
   *
   * @param string $keyPrefix Option key name.
   * @param array $arValues Array values.
   */
  protected function addOptionsExt($keyPrefix, $arValues)
  {
    $szValues = count($arValues);
    for ($i = 0; $i < $szValues; $i++)
    {
      list($key, $value) = each($arValues);
      $curPrefix = $keyPrefix . '_' . $key;

      if (is_array($value))
      {
        $this->addOptionsExt($curPrefix, $value);
      }
      else {
        $this->addOption($curPrefix, $value);
      }
    }
  }
}

