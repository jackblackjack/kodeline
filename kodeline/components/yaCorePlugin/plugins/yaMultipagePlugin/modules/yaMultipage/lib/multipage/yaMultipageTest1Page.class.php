<?php

/**
 * yaMultipageTest1Page class.
 *
 */
class yaMultipageTest1Page extends yaMultiPage
{
  public function fetch()
  {
    return array('result' => array('total' => 10));
  }

  /**
   * save()
   *
   */
  public function save()
  {
    $this->handler->addValues($this->name, $this->form->getValues());
  }

}