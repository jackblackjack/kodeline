<?php

/**
 * sfWidgetFormSchemaFormatterYaTable
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     pinhead
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterYaTable.class.php 2315 2010-09-22 03:46:47Z chugarev $
 */
class sfWidgetFormSchemaFormatterYaTable extends yaWidgetFormSchemaFormatter
{
  protected
    $rowFormat        = "<tr%row_stylesheet%%row_attributes%>\n  <th>%label%</th>\n  <td>%field%%help%%error%%hidden_fields%</td>\n</tr>\n",
    $errorRowFormat   = "<tr><td colspan=\"2\">\n%errors%</td></tr>\n",
    $helpFormat       = '<br /><span class="form_help">%help%</span>',
    $labelSuffix      = ':',
    $requiredTemplate = '<span class="req_f">*</span>',
    $decoratorFormat  = "<table>\n  %content%</table>",
    $validatorSchema  = null;
}
