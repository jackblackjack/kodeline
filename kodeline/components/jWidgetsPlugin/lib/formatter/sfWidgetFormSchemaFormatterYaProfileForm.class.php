<?php
/**
 * sfWidgetFormSchemaFormatterYaProfileForm
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     step
 * @version    $Id$
 */
class sfWidgetFormSchemaFormatterYaProfileForm extends yaWidgetFormSchemaFormatterJqValidator
{
  protected
    $rowFormat        = "%label%<div class='input'>%field%%help%%error%%hidden_fields%</div>",
    $errorRowFormat   = "<p>%errors%</p>",
    $helpFormat       = '<br /><span class="form_help">%help%</span>',
    $labelSuffix      = ':',
    $requiredTemplate = '',
    $decoratorFormat  = "<fieldset class='pad'>%content%</fieldset>",
    $validatorSchema  = null;
}
