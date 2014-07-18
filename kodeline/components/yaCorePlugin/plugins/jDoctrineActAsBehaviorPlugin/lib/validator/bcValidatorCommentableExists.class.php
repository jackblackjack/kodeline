<?php
/**
 */
class bcValidatorCommentableExists extends sfValidatorBase
{
  /**
   * Имя модели используемой для комментирования.
   * @var string
   */
  const COMMENTABLE_MODEL_NAME = 'jCommentable';

  /**
   * {@inhertitDoc}
   */
  protected function doClean($commentId)
  {
    $commentId = intval($commentId);

    die(__METHOD__);

    // Проверка существования записи с указанным ID.
    $model = new $${self::COMMENTABLE_MODEL_NAME}();
    if (! $model)
    {
      die('aaa!');
    }

    die('bbb!');

    // Соединение с базой данных.
    $connection = Doctrine_Manager::getInstance()->getCurrentConnection();
    //$arTables = $connection->getTables();
    $arTables = Doctrine_Core::getLoadedModels();
    die(var_dump($arTables));

    if (strval($clean) != $value)
    {
      throw new sfValidatorError($this, 'invalid', array('value' => $value));
    }
    
    if (strval(round($clean, $this->getOption('precision'))) != strval($clean))
    {
      throw new sfValidatorError($this, 'precision', array('value' => $value));
    }
    
    if ($this->hasOption('max') && $clean > floatval($this->getOption('max')))
    {
      throw new sfValidatorError($this, 'max', array('value' => $value, 'max' => $this->getOption('max')));
    }

    if ($this->hasOption('min') && $clean < floatval($this->getOption('min')))
    {
      throw new sfValidatorError($this, 'min', array('value' => $value, 'min' => $this->getOption('min')));
    }
  }
}