<?php

/**
 * sfWidgetFormSchemaFormatterYaCss2 class.
 *
 * @package    yaWidgetsPlugin
 * @subpackage formatter
 * @author     pinhead
 * @version    SVN: $Id: sfWidgetFormSchemaFormatterYaCss2.class.php 1373 2010-03-14 21:09:51Z pinhead $
 */
class sfWidgetFormSchemaFormatterYaCss2 extends yaWidgetFormSchemaFormatter
{
  protected
    $rowFormat                  = '<p class="row"%row_stylesheet%%row_attributes%>%label%%field%%error%%help%</p>%hidden_fields%',
    $helpFormat                 = '<span class="help">%help%</span>',
    $errorRowFormat             = '%errors%',
    $errorRowFormatInARow       = '<li>%error%</li>',
    $errorListFormatInARow      = '<ul class="errors">%errors%</ul>',
    $namedErrorRowFormatInARow  = '<li>%name%: %error%</li>',
    $decoratorFormat            = '%content%';

  protected
    $labelSuffix      = ':',
    $requiredTemplate = '<sup>*</sup>';
}
