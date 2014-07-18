<?php

/**
 * Doctrine form base class.
 *
 * @package    yaCorePlugin
 * @subpackage lib.form
 * @author     pinhead
 * @version    SVN: $Id: yaFormDoctrine.class.php 2245 2010-09-13 21:28:14Z pinhead $
 */
abstract class yaFormDoctrine extends sfFormDoctrine
{
  protected function disableField($parent_form, $key)
  {
    unset($this->widgetSchema[$parent_form][$key]);
    unset($this->validatorSchema[$parent_form][$key]);
    unset($this->defaults[$parent_form][$key]);
    unset($this->taintedValues[$parent_form][$key]);
    unset($this->values[$parent_form][$key]);
    unset($this->embeddedForms[$parent_form][$key]);
  }
}
