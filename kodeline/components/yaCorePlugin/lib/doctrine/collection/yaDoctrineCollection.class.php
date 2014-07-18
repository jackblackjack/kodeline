<?php

abstract class yaDoctrineCollection extends Doctrine_Collection
{

  /**
   * Processes the difference of the last snapshot and the current data
   *
   * an example:
   * Snapshot with the objects 1, 2 and 4
   * Current data with objects 2, 3 and 5
   *
   * The process would remove object 4
   *
   * Diem alteration :
   * I never want translation records to be deleted.
   * It allows not to load all language translation
   * and to save a record without deleting all other translations
   *
   * @return Doctrine_Collection
   */
  public function processDiff()
  {
    if ($translationPos = strpos($this->_table->getComponentName(), 'Translation'))
    {
      $baseRecordClass = substr($this->_table->getComponentName(), 0, $translationPos);

      if ($baseTable = yaDb::table($baseRecordClass))
      {
        return $this;
      }
    }

    return parent::processDiff();
  }

  /**
   * Exports I18n-aware collection as array
   *
   * @param boolean $deep
   * @param boolean $prefixKey
   *
   * @return array
   */
  public function toArrayWithI18n($deep = true, $prefixKey = false)
  {
    $data = array();

    foreach ($this as $key => $record)
    {
      $key = $prefixKey ? get_class($record) . '_' .$key:$key;
      if ($record instanceof yaDoctrineRecord)
      {
        $data[$key] = $record->toArrayWithI18n($deep, $prefixKey);
      }
      else
      {
        $data[$key] = $record->toArray($deep, $prefixKey);
      }
    }

    return $data;
  }

  /**
   * Return array representation of this collection
   *
   * @return array An array representation of the collection
   */
  public function toDebug()
  {
    return array(
      'class' => $this->getTable()->getComponentName(),
      'data'  => $this->toArray()
    );
  }
}