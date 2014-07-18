<?php
/**
 * sfWidgetFormSchemaFormatterYaCss
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     pinhead
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterYaCss.class.php 711 2009-09-04 18:29:47Z pinhead $
 */
class sfWidgetFormSchemaFormatterYaCss extends yaWidgetFormSchemaFormatter
{
  protected
    $rowFormat                  = '<div%row_stylesheet%%row_attributes%>%label%%field%%error%%help%%hidden_fields%</div>',
    $helpFormat                 = '<span class="help">%help%</span>',
    $errorRowFormat             = '<div class="errors">%errors%</div>',
    $errorListFormatInARow      = '<ul class="error_list">%errors%</ul>',
    $namedErrorRowFormatInARow  = '<li>%name%: %error%</li>',
    $decoratorFormat            = '%content%';

  protected
    $labelSuffix      = ':',
    $requiredTemplate = '<span class="req_f">*</span>';
}
