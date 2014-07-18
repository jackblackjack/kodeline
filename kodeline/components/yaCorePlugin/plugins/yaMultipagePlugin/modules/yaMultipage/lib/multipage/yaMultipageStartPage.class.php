<?php

/**
 * yaMultipageStartPage class.
 *
 */
class yaMultipageStartPage extends yaMultiPage
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