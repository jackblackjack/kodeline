<?php
/**
 * sfWidgetFormSchemaFormatterYaFieldset
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     chugarev
 * @version    $Id$
 */
class sfWidgetFormSchemaFormatterYaFieldset extends yaWidgetFormSchemaFormatter
{
  protected
    $rowFormat        = "<div%row_stylesheet%%row_attributes%>%label%<div class=\"input\">%field%%help%%error%%hidden_fields%</div></div>",
    $errorRowFormat   = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
    $helpFormat       = '<span class="form_help">%help%</span>',
    $labelSuffix      = ':',
    $requiredTemplate = '<span class="req_f">*</span>',
    $decoratorFormat  = "%content%";
}
