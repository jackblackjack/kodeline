<?php
/**
 * yaMultipageCacheSessionStorage manages multipage session storage via cache backend.
 *
 * This class stores the session data in via sfCache instance and with an id passed in the request.
 *
 * @package    yaMultipagePlugin
 * @subpackage storage
 * @author     pinhead
 */
class yaMultipageCacheSessionStorage extends yaMultipageSessionStorage
{
  protected
    $context     = null,
    $dispatcher  = null,
    $cache       = null,
    $data        = null,
    $dataChanged = false;

  /**
   * Separator, which replaced NULL values.
   * @var string
   */
  protected $sDbNullSeparator = '~~NULL_BYTE~~';

  /**
   * Initialize this Storage.
   *
   * @param string  $id       Session ID
   * @param array   $options  An associative array of initialization parameters.
   *                            session_name [required] name of session to use
   *
   * @return bool true, when initialization completes successfully, otherwise false.
   *
   * @throws <b>sfInitializationException</b> If an error occurs while initializing this Storage.
   */
  public function initialize($options = array())
  {
    // initialize parent
    parent::initialize(array_merge(array('id' => null, 'lazy_load' => true), $options));

    // create cache instance
    if (isset($this->options['cache']) && ! empty($this->options['cache']['class'])) {
      $this->cache = new $this->options['cache']['class'](is_array($this->options['cache']['param']) ? $this->options['cache']['param'] : array());
    }
    else {
      throw new InvalidArgumentException('yaMultipageCacheSessionStorage requires cache option.');
    }

    // get context and event dispatcher
    $this->context     = sfContext::getInstance();
    $this->dispatcher  = $this->context->getEventDispatcher();

    // Normalize session id.
    $id = $this->options['id'];

    if (empty($id) || ! preg_match("/\w{32}/i", $id))
    {
      // generate session id
      $this->id = $this->generateId();

     if(sfConfig::get('sf_logging_enabled'))
     {
       $this->dispatcher->notify(new sfEvent($this, 'application.log', array('New multipage session created')));
     }

     $this->data = array();
    }
    else
    {
      $this->id = $id;

      // load data from cache
      if (! $this->options['lazy_load'])
      {
        $this->data = $this->cache->get($this->id, array());
      }

      if(sfConfig::get('sf_logging_enabled'))
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', 
          array(sprintf('Restored previous multipage session: %s', $this->id), 'priority' => sfLogger::INFO)));
      }
    }

    return true;
  }

  /**
   * Generates session id.
   *
   * @return string
   */
  protected function generateId()
  {
    if (version_compare(PHP_VERSION, '5.4') >= 0) {
      $quick_rand = base_convert(mt_rand(0x1D39D3E06400000, min(0x41C21CB8E0FFFFFF, mt_getrandmax())), 10, 36);
    }
    else {
      $quick_rand = base_convert(mt_rand(0x19A100, 0x39AA3FF), 10, 36);
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : 'localhost';
    $ua = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : 'ua';

    // generate new id based on random # / ip / user agent / uniqid
    return md5($quick_rand . uniqid($ip . $ua, true));
  }

  /**
   * Get current session id.
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Reads session data from cache
   */
  protected function loadData()
  {
    $data = $this->cache->get($this->id, array());

    if (is_string($data))
    {
      if ($this->cache instanceof sfDatabaseCache)
      {
        $data = str_replace($this->sDbNullSeparator, "\0", $data);
      }

      if (sfConfig::get('sf_logging_enabled'))
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('From storage: "%s"', $data), 'priority' => sfLogger::INFO)));
      }

      $data = unserialize($data);
    }

    $this->data = $data;
  }

  /**
   * Write session data to cache
   */
  protected function saveData()
  {
    //var_dump($this->data); die;
    $serialized = serialize($this->data);

    if ($this->cache instanceof sfDatabaseCache)
    {
      $serialized = str_replace("\0", $this->sDbNullSeparator, $serialized);
    }

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('To storage: "%s"', $serialized), 'priority' => sfLogger::INFO)));
    }
  
    $this->cache->set($this->id, $serialized);
  }

  /**
   * Write data to this storage.
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string $key  A unique key identifying your data.
   * @param mixed  $data Data associated with your key.
   *
   * @return void
   */
  public function write($key, $data)
  {
    if (null == $this->data)
    {
      // lazy load data from cache
      $this->loadData();
    }

    $this->dataChanged = true;
    $this->data[$key] = $data;
  }

  /**
   * Read data from this storage.
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string $key A unique key identifying your data.
   * @return mixed Data associated with the key.
   */
  public function read($key)
  {
    if (null == $this->data)
    {
      // lazy load data from cache
      $this->loadData();
    }

    $retval = null;

    if (isset($this->data[$key]))
    {
      $retval = $this->data[$key];
    }

    return $retval;
  }

  /**
   * Remove data from this storage.
   *
   * The preferred format for a key is directory style so naming conflicts can be avoided.
   *
   * @param string $key A unique key identifying your data.
   *
   * @return mixed Data associated with the key.
   */
  public function remove($key)
  {
    $retval = null;

    if (isset($this->data[$key]))
    {
      $this->dataChanged = true;

      $retval = $this->data[$key];
      unset($this->data[$key]);
    }

    return $retval;
  }

  /**
   * Regenerates id that represents this storage.
   *
   * @param boolean $destroy Destroy session when regenerating?
   *
   * @return boolean True if session regenerated, false if error
   *
   * @throws <b>sfStorageException</b> If an error occurs while regenerating this storage
   */
  public function regenerate($destroy = false)
  {
    if($destroy)
    {
      $this->data = array();
      $this->cache->remove($this->id);
    }

    // generate session id
    $this->id = $this->generateId();

    // save data to cache
    $this->cache->set($this->id, $this->data);

    return true;
  }

  /**
   * Executes the shutdown procedure.
   *
   * @throws <b>sfStorageException</b> If an error occurs while shutting down this storage
   */
  public function shutdown()
  {
    // only update cache if session has changed
    if (true === $this->dataChanged)
    {
      $this->saveData();

      if(sfConfig::get('sf_logging_enabled'))
      {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Storing multipage session to cache.')));
      }
    }
  }

  /**
   * Deletes storage data from cache.
   */
  public function erase()
  {
    $this->data = array();
    $this->cache->remove($this->id);
  }

  /**
   * Expires the session storage instance.
   */
  public function expire()
  {
    // destroy data and regenerate id
    $this->regenerate(true);

    if(sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array('New multipage session created due to expiraton.')));
    }
  }
}
