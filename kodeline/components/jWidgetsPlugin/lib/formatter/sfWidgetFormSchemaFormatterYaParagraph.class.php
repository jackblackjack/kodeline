<?php
/**
 * sfWidgetFormSchemaFormatterYaParagraph
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     chugarev
 * @version    $Id$
 */
class sfWidgetFormSchemaFormatterYaParagraph extends yaWidgetFormSchemaFormatter
{
  protected
    $rowFormat        = "<p%row_stylesheet%%row_attributes%>%label%%field%%help%%error%%hidden_fields%</p>",
    $errorRowFormat   = "<p>%errors%</p>",
    $helpFormat       = '<br /><span class="form_help">%help%</span>',
    $labelSuffix      = ':',
    $requiredTemplate = '<span class="req_f">*</span>',
    $decoratorFormat  = "%content%",
    $validatorSchema  = null;
}
