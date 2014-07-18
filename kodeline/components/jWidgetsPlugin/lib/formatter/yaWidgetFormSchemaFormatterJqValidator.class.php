<?php
/**
 * Abstract class yaWidgetFormSchemaFormatter
 * support the allocation of the required fields.
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     chugarev
 * @version    $Id$
 */
abstract class yaWidgetFormSchemaFormatterJqValidator extends sfWidgetFormSchemaFormatter
{
  /**
   * List of validators of form fields.
   * @var array
   */
  protected $arFields = array();

  /**
   * String for prefix of label.
   * @var string
   */
  protected $labelPrefix = '';
  
  /**
   * String for suffix of label.
   * @var string
   */
  protected $labelSuffix = ':';
  
  /**
   * String for suffix of required fields.
   * @var string
   */
  protected $requiredTemplate = '<span class="req_f">*</span>';

  /**
   * Name of stylesheet class for dependent fields.
   * @var string
   */
  protected $dependencyCssClass = '';

  /**
   * Css class for row.
   * @var string.
   */
  protected $rowClass = '';

  /**
   * Copy form validation scheme.
   * @var sfValidatorSchema
   */
  protected $validatorSchema;

  /**
   * Extended row formats for customize form output.
   * @var array
   */
  protected $arExtendedFormats = array();

  /**
   * Return list of field having validator.
   * @return array
   */
  protected function getFormFields()
  {
    if (! $this->arFields)
    {
      $this->arFields = $this->getWidgetSchema()->getFields();
    }

    return $this->arFields;
  }

  /**
   * Exploding form field name from html-code and return.
   * 
   * @param string $sFormat Format of naming form fields.
   * @param string $sHtmlField Html code field.
   * @return string
   */
  protected function explodeFieldName($sFormat, $sHtmlField)
  {
    $sFieldFullName = '';
    $arMatch = array();
    if (preg_match('/.*name="([^"]+)".*/', $sHtmlField, $arMatch))
    {

      $sFieldFullName = $arMatch[1];

      if (preg_match_all('/\[([^\]]+)\]/is', $sFieldFullName, $arMatch))
      {
//      $sFieldFullName = array_shift($arMatch[1]);
        $sFieldFullName = count($arMatch[1]) ? $arMatch[1][count($arMatch[1])-1] : $arMatch[1];
//      return $sFieldFullName . (count($arMatch[1]) ? '[' . implode('][', $arMatch[1]) . ']' : '');
      }
    }
    return $sFieldFullName;
  }

  /**
   * @inheritDoc
   * @see sfWidgetFormSchemaFormatter::formatRow()
   */
  public function formatRow($label, $field, $errors = array(), $help = '', $hiddenFields = null)
  {
    $rowCssStyle = ($this->rowClass) ? 'class="'. $this->rowClass . '"' : '';
    $rowAttributes = array();

    if ($sFieldName = $this->explodeFieldName($this->getWidgetSchema()->getNameFormat(), $field))
    {
      $arFields = $this->getFormFields();

      // Define css style for row rendering.
      if (array_key_exists($sFieldName, $arFields))
      {

        if ($arFields[$sFieldName]->hasOption('dependency'))
        {
          $rowAttributes = array('id' => ($arFields[$sFieldName]->hasOption('dependency_id') ? $arFields[$sFieldName]->getOption('dependency_id') : $sFieldName . '_dependent'));
          $rowCssStyle =  (! $errors ? (! $this->dependencyCssClass) ? ' style="display: none"' : ' class="'. $this->rowClass . '  '. $this->dependencyStyle . '"' : '');
        }
        
      }
    }

    // Define current row format.
    $currentRowFormat = $this->getRowFormat();
    if (isset($this->arExtendedFormats[$sFieldName]))
      $currentRowFormat = $this->arExtendedFormats[$sFieldName];
      
    return strtr($currentRowFormat, array(
      '%row_stylesheet%' => $rowCssStyle,
      '%row_attributes%' => implode('', array_map(create_function('$k, $v', 'return sprintf(\' %s="%s"\', $k, $v);'), array_keys($rowAttributes), array_values($rowAttributes))),
      '%label%'         => $label,
      '%field%'         => $field,
      '%error%'         => $this->formatErrorsForRow($errors),
      '%help%'          => $this->formatHelp($help),
      '%hidden_fields%' => is_null($hiddenFields) ? '%hidden_fields%' : $hiddenFields,
    ));
  }

  /**
   * @inheritDoc
   * @see sfWidgetFormSchemaFormatter::generateLabelName()
   */
  public function generateLabelName($name)
  {
    // Define label text of field.
    $fieldLabel = parent::generateLabelName($name);
    $label = (empty($this->labelPrefix) ? '' : $this->labelPrefix) . parent::generateLabelName($name) .
             (empty($this->labelSuffix) || '?' == $fieldLabel{(strlen($fieldLabel) - 1)} ? '' : $this->labelSuffix);

    $arFields = (! is_null($this->validatorSchema) ? $this->validatorSchema->getFields() : $this->getFormFields());
    if (! $arFields) return $label;

    if (array_key_exists($name, $arFields))
    {
      $field = $arFields[$name];

      if ($field->hasOption('required') && $field->getOption('required'))
      {
        $label .= $this->requiredTemplate;
      }
    }

    return $label;
  }

  /**
   * Save validator scheme.
   * 
   * @param sfValidatorSchema $validatorSchema
   */
  public function setValidatorSchema(sfValidatorSchema $validatorSchema)
  {
    $this->validatorSchema = $validatorSchema;
  }
} 