<?php
/**
 */
class bcValidatorCommentableProfileExists extends sfValidatorBase
{
  /**
   * {@inhertitDoc}
   */
  protected function doClean($profileId)
  {
    $profileId = intval($profileId);

    // Проверка существования профиля с указанным ID.
    $bExists = Doctrine_Query::create()->from('jProfileExtension')->where('id = ?', $profileId)->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    if (! $bExists)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $profileId));
    }

    return $profileId;
  }
}