<?php

/**
 * yaMultipageTest3Page class.
 *
 */
class yaMultipageTest3Page extends yaMultiPage
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