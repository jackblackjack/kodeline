<?php

/**
 * yaMultipageTest2Page class.
 *
 */
class yaMultipageTest2Page extends yaMultiPage
{
  /**
   * save()
   *
   */
  public function save()
  {
    $this->handler->addValues($this->name, $this->form->getValues());
  }

}