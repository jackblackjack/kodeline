<?php
/**
 * yaMultipageStorage is a base class for all multipage storage types.
 *
 * @package    yaMultipagePlugin
 * @subpackage lib.storage
 * @author     pinhead
 * @version    SVN: $Id: yaMultipageStorage.class.php 2052 2010-09-01 11:45:36Z pinhead $
 */
abstract class yaMultipageStorage
{
  protected
    $options = array();

  /**
   * Class constructor.
   *
   * @see initialize()
   */
  public function __construct($options = array())
  {
    $this->initialize($options);

    if ($this->options['auto_shutdown'])
    {
      register_shutdown_function(array($this, 'shutdown'));
    }
  }

  /**
   * Initializes this Storage instance.
   *
   * Available options:
   *    auto_shutdown: Whether to automatically save the changes to the session (true by default)
   *
   * @param  array $options  An associative array of options
   *
   * @return bool true, if initialization completes successfully, otherwise false
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this sfStorage
   */
  public function initialize($options = array())
  {
    $this->options = array_merge(array(
      'auto_shutdown' => true,
    ), $options);
  }

  /**
   * Returns the option array.
   *
   * @return array The array of options
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Reads data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key  A unique key identifying your data
   *
   * @return mixed Data associated with the key
   *
   * @throws <b>sfStorageException</b> If an error occurs while reading data from this storage
   */
  abstract public function read($key);

  /**
   * Regenerates id that represents this storage.
   *
   * @param  boolean $destroy Destroy session when regenerating?
   *
   * @return boolean True if session regenerated, false if error
   *
   * @throws <b>sfStorageException</b> If an error occurs while regenerating this storage
   */
  abstract public function regenerate($destroy = false);

  /**
   * Removes data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key  A unique key identifying your data
   *
   * @return mixed Data associated with the key
   *
   * @throws <b>sfStorageException</b> If an error occurs while removing data from this storage
   */
  abstract public function remove($key);

  /**
   * Removes all data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key  A unique key identifying your data
   *
   * @throws <b>sfStorageException</b> If an error occurs while removing data from this storage
   */
  abstract public function erase();

  /**
   * Executes the shutdown procedure.
   *
   * @throws <b>sfStorageException</b> If an error occurs while shutting down this storage
   */
  abstract public function shutdown();

  /**
   * Writes data to this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param  string $key   A unique key identifying your data
   * @param  mixed  $data  Data associated with your key
   *
   * @throws <b>sfStorageException</b> If an error occurs while writing to this storage
   */
  abstract public function write($key, $data);
}
