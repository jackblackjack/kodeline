<?php
/**
 * jQueryValidateForm class.
 *
 * Before renedring form generate validation rules for yui-form-validator
 * @url http://code.google.com/p/yui-form-validator/source/checkout
 *
 * @package     jWidgetsPlugin
 * @subpackage  lib
 * @author      chugarev
 * @version     $Id$
 */
class jQueryValidateForm extends sfForm
{
  /**
   * List of fields hierarchy.
   * @var array
   */
  protected $arHierarchy = array();

  /**
   * List of validators of form fields.
   * @var array
   */
  protected $arValidatorFields = array();
  
  /**
   * List of supported options for convert to yui-form-validator options.
   *
   * @staticvar array
   */
  protected static $arSupportedOptions =
    array(
        'min_value'   => 'minValue',
        'max_value'   => 'maxValue',
        'max_length'  => 'maxLength',
        'min_length'  => 'minLength',
        'date_format' => 'mask',
        'pattern'     => 'mask',
    );

  /**
   * List of supported messages for convert to yui-form-validator options.
   * 
   * @staticvar array
   */
  protected static $arSupportedMessages =
    array(
        'required' => 'errorMessage'
    );
  
  /**
   * @inheritDoc
   */
  public function getJavaScripts()
  {
    return array_merge(
            //array('yui/validator/src/yui2.5.1/yui-pack-2.5.1.js'),
            parent::getJavaScripts(),
            array('yui/validator/src/st-validator'));
  }

  /**
   * Convert symfony options to yui-form-validator options.
   *
   * @param array $arValues All symfony options.
   * @param array $arSupported Supported options.
   * @return array
   */
  public function convert($arValues = array(), $arSupported = array())
  {
    $arResult = array();

    foreach($arValues as $key => $value)
    {
      if (array_key_exists($key, $arSupported))
      {
        $key = $arSupported[$key];
        if (null == $value) continue;
        $arResult[$key] = $value;
      }
    }

    return $arResult;
  }

  /**
   * Generate rules for javascript validator.
   * 
   * @return array
   */
  public function createValidatorRules()
  {
    $arRules = array();

    $arFields = $this->getValidatorSchema()->getFields();
    foreach($arFields as $fieldName => $Field)
    {
      if (! $Field->getOption('required') || $fieldName == $this->getCSRFFieldName()) continue;
      
      if ($arFieldRules = 
              ($this->convert($Field->getOptions(), self::$arSupportedOptions)
               + $this->convert($Field->getMessages(), self::$arSupportedMessages)))
      {
        $arRules += array($fieldName => $arFieldRules);
      }
    }
    
    return $arRules;
  }

  /**
   * Save fields hierarchy.
   *
   * @param array $arHierarchy List of hierarchy.
   */
  public function setFieldsHierarchy($arHierarchy)
  {
    $arFormFields = &$this->getWidgetSchema()->getFields();
    foreach($arHierarchy as $sDepField => $arDependency)
    {
      if (3 != count($arDependency)) throw new sfException('Illegal dependency description.');

      if (empty($arFormFields[$sDepField])) throw new sfException('Dependented field is not exists.');

      // Set dependent rule for field.
      $arFormFields[$sDepField]->addOption('dependency_rule', $arDependency);
      
      $sMethodName = array_shift($arDependency);
      $arValues = array_shift($arDependency);
      $sLeadFName = array_shift($arDependency);
      if (empty($arFormFields[$sLeadFName])) throw new sfException('Leading field is not exists.');

      // Set dependent option.
      $arFormFields[$sDepField]->addOption('dependency', $sLeadFName);
    }
    
    // Save hierarchy.
    $this->arHierarchy = $arHierarchy;   
  }

  /**
   * Return fields hierarchy.
   * 
   * @return array
   */
  public function getFieldsHierarchy()
  {
    return $this->arHierarchy;
  }

  /**
   * Prepare fields dependencies and generate
   * javascript-code for it.
   */
  public function prepareHierarchy()
  {
    $arJsMethods = array();
    $sJavascriptCode = '';

    $arFormFields = &$this->getWidgetSchema()->getFields();
    foreach($this->arHierarchy as $sDepField => $arDependency)
    {
      // Size of dependency.
      if (3 != count($arDependency)) throw new sfException('Illegal dependency description.');

      $sMethodName = array_shift($arDependency);
      $arValues = array_shift($arDependency);
      $sLeadFName = array_shift($arDependency);

      // Switch hierarchy method.
      switch($sMethodName)
      {
        // Method
        case 'method':
          // Define id attribute of lead field.
          if (! ($sLeadFId = $arFormFields[$sLeadFName]->getAttribute('id'))) {
            $sLeadFId = $this->getFieldId($sLeadFName);
          }

          if (isset($arJsMethods[$sLeadFId]) && $arJsMethods[$sLeadFId] == $arValues)
            continue;
          
          // Set javascript-code.
          $sJavascriptCode .= "
            YAHOO.util.Event.onContentReady('{$sLeadFId}', (function(){
              YAHOO.util.Event.on('{$sLeadFId}', 'change', function(event){ return $arValues(event); });
            }));";

          $arJsMethods[$sLeadFId] = $arValues;
        break;

        // Value
        case 'value':
          // Define condition for field.
          $sCondition = "'" . implode("' == target.value || '", $arValues) . "' == target.value";

          // Define id attribute of lead field.
          if (! ($sLeadFId = $arFormFields[$sLeadFName]->getAttribute('id'))) {
            $sLeadFId = $this->getFieldId($sLeadFName);
          }

          // Define id attribute of depended field.
          if (! ($sDepFieldId = $arFormFields[$sDepField]->getOption('dependency_id'))) {
            $sDepFieldId = $sDepField . '_dependent';
          }

          // Set javascript-code.
          $sJavascriptCode .= "
            YAHOO.util.Event.onContentReady('{$sLeadFId}', (function(){
              YAHOO.util.Event.on('{$sLeadFId}', 'change', function(event){
              	target = YAHOO.util.Event.getTarget(event);
                if ({$sCondition}) { YAHOO.util.Dom.setStyle('{$sDepFieldId}', 'display', ''); }
                else { YAHOO.util.Dom.setStyle('{$sDepFieldId}', 'display', 'none'); }
              });
            }));";
        break;
      }
    }
/*
    $arFormFields = &$this->getWidgetSchema()->getFields();

    foreach($this->arHierarchy as $sField => $arDependency)
    {
      foreach($arDependency as $sValue => $arFields)
      {
        foreach($arFields as $sFieldName)
        {
          // Set dependency field option.
          $arFormFields[$sFieldName]->addOption('dependency', $sField);

          // $sField _dependent.
          // Set yui callback for change value of field
          $sJavascriptCode .= '
            YAHOO.util.Event.on("change" '{$this->getName()}', (function(){
          var _oValidator = new YAHOO.extension.validator('" . $this->getName() . "', {
            notifyType:'tips', // default tips
            stopOnFirst : false,
            imageBase : '../src/img/',
            onSubmit : true, //default true, intervene the on submit event
            checkOnBlur : true, //default true, check an input when it losts focus
            hideSuccess : false // default false
          });
          _oValidator.addRules($sValidatorRules);
          _oValidator.validate();
          YAHOO.util.Event.on('clearAll', 'click', _oValidator.clearAll, _oValidator, true);
        }));");

        }
      }
    }
    */
    return $sJavascriptCode;
  }

  /**
   * Prepare list of form field to render.
   * Add 'required' option for required fields.
   *
   * @see yaWidgetFormSchemaFormatter
   */
  protected function prepareRequired()
  {
    $arFormFields = $this->getWidgetSchema()->getFields();
    foreach(array_keys($arFormFields) as $sFieldName)
    {
      if ($this->isRequired($sFieldName))
      {
        $arFormFields[$sFieldName]->addOption('required', true);
      }
    }
  }

  /**
   * Return final field name in html-code.
   *
   * @param string $sFieldName Name of form field.
   * @return string
   */
  protected function getFieldId($sFieldName)
  {
    return trim(str_replace(array('[', ']'), '_', str_replace('%s', $sFieldName, $this->widgetSchema->getNameFormat())), '_');
  }

  /**
   * Check if a field is required or not.
   * Retrun true if field is required and false if not.
   *
   * @return boolean
   */
  public function isRequired($field)
  {
    if (! $this->arValidatorFields) {
      $this->arValidatorFields = $this->getValidatorSchema()->getFields();
    }

    if (array_key_exists($field, $this->arValidatorFields))
    {
      return ($this->arValidatorFields[$field]->hasOption('required') && $this->arValidatorFields[$field]->getOption('required'));
    }

    return false;
  }

  /**
   * Generate validation rules for yui-form-validator and render the form.
   *
   * @param array $attributes attributes for render.
   */
  public function render($attributes = array())
  {
    // Set required fields.
    $this->prepareRequired();

    // Build javascript validation rules.
    $sValidatorRules = json_encode($this->createValidatorRules());

    // Activate JavascriptBase helper.
    sfProjectConfiguration::getActive()->loadHelpers(array('JavascriptBase'));

    return
      parent::render($attributes) .
      javascript_tag(
        $this->prepareHierarchy());

    return 
      parent::render($attributes) .
      javascript_tag(
        $this->prepareHierarchy() .
        "YAHOO.util.Event.onContentReady('{$this->getName()}', (function(){
          var _oValidator = new YAHOO.extension.validator('" . $this->getName() . "', {
            notifyType:'tips', // default tips
            stopOnFirst : false,
            imageBase : '../src/img/',
            onSubmit : true, //default true, intervene the on submit event
            checkOnBlur : true, //default true, check an input when it losts focus
            hideSuccess : false // default false
          });
          _oValidator.addRules($sValidatorRules);
          _oValidator.validate();
          YAHOO.util.Event.on('clearAll', 'click', _oValidator.clearAll, _oValidator, true);
        }));");
  }
}