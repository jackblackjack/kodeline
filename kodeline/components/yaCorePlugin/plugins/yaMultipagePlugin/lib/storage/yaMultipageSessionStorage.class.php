<?php

/**
 * yaMultipageSessionStorage represents multipage session storage.
 *
 * @package    yaMultipagePlugin
 * @subpackage storage
 * @author     pinhead
 */
abstract class yaMultipageSessionStorage extends yaMultipageStorage
{
  protected
    $id = null;

  /**
   * @see yaStorage
   */
  public function initialize($options = array())
  {
    $this->options = array_merge(array(
      'auto_shutdown' => true,
    ), $options);
  }

  /**
   * Get current session id.
   */
  public function getId()
  {
    return $this->id;
  }
}
