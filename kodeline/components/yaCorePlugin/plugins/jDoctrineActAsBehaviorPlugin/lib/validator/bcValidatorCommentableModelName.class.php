<?php
/**
 * Валидатор проверки существования модели в системе.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  plugin
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class bcValidatorCommentableModelName extends sfValidatorBase
{
  /**
   * {@inhertitDoc}
   */
  protected function doClean($modelName)
  {
    $modelName = trim($modelName);

    // Проверка существования модели.
    if (! Doctrine_Core::isValidModelClass($modelName))
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $modelName));
    }

    return $modelName;
  }
}