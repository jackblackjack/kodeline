<?php
/**
 * yaContentStorage
 * 
 * TODO:
 * Следует сделать из этого класса фабрику классов относительно сохраняемого файла (по mime-типу).
 * При инициализации выбирается класс относительно mime-типа и дальше работа производится относительно типа файла.
 * Хранилище должно поддерживать интерфейс CRUD.
 *
 * @package     yaCorePlugin
 * @subpackage  lib
 * @author      chugarev
 * @version     $Id$
 */
class yaContentStorage extends yaStorage
{
  /**
   */
  public function getPath($type)
  {
    if (! $this->has($type)) {
      throw new yaContentStorageException(sprintf('Type "%s" is not found (%s::%s)!', $type, __CLASS__, __METHOD__));
    }

    return $this->get($type)['path'];
  }

  public function getWebPath($type)
  {
    if (! $this->has($type)) {
      throw new yaContentStorageException(sprintf('Type "%s" is not found (%s::%s)!', $type, __CLASS__, __METHOD__));
    }

    return $this->get($type)['rel_path'];
  }

  public function getAvailableTypes()
  {
    if (! $this->has('available_types')) {
      throw new yaContentStorageException(sprintf('Type "%s" is not found (%s::%s)!', $type, __CLASS__, __METHOD__));
    }

    return $this->get('available_types');
  }

  
}
