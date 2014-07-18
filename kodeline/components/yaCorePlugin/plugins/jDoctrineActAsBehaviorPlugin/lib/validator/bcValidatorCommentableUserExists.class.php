<?php
/**
 */
class bcValidatorCommentableUserExists extends sfValidatorBase
{
  /**
   * {@inhertitDoc}
   */
  protected function doClean($userId)
  {
    $userId = intval($userId);

    // Проверка существования пользователя с указанным ID.
    $bExists = Doctrine_Query::create()->from('sfGuardUser')->where('id = ?', $userId)->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    if (! $bExists)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $userId));
    }

    return $userId;
  }
}