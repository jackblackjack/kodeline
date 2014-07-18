<?php
/**
 * Form for item enum list.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    form
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableParamEnumItemForm extends yaForm
{
  /**
   * {@inheritDoc}
   */
  public function configure()
  {
    // Redefine title field.
    $this->setWidget('title', new sfWidgetFormInputText());
    $this->setValidator('title', new sfValidatorString(array('required' => true)));
  }
}