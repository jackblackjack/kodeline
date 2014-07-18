<?php
class sfMemcacheSingletonCache extends sfMemcacheCache
{
  static private $instance = null;

  public function __construct()
  {
    $this->initialize();
  }

  static public function getInstance()
  {
    if(!self::$instance)
    {
      self::$instance = new self();
    }
 
    return self::$instance;
  }

  /**
   *
   * @see sfMemcacheCache#initialize()
   */
  public function initialize($options = array())
  {
    $this->options = sfConfig::get('app_ya_core_plugin_view_cache', array());
 
    // START: taken from sfMemcacheCache::initialize
    if ($this->getOption('servers'))
    {
      foreach ($this->getOption('servers') as $server)
      {
        $port = isset($server['port']) ? $server['port'] : 11211;
        if (!$this->addServer($server['host'], $port, isset($server['persistent']) ? $server['persistent'] : true))
        {
          throw new sfInitializationException(sprintf('Unable to connect to the memcache server (%s:%s).', $server['host'], $port));
        }
      }
    }
    else
    {
      $method = $this->getOption('persistent', true) ? 'pconnect' : 'connect';
      if (!$this->$method($this->getOption('host', 'localhost'), $this->getOption('port', 11211), $this->getOption('timeout', 1)))
      {
        throw new sfInitializationException(sprintf('Unable to connect to the memcache server (%s:%s).', $this->getOption('host', 'localhost'), $this->getOption('port', 11211)));
      }
    }
    // END: taken from sfMemcacheCache::initialize
  }

  /**
   * Clearing the symfony cache will trigger flushing your complete memcache. 
   * If you do not want to allow this, you may overwrite the clean method in this child class.
   */
  public function clean($mode = sfCache::ALL)
  {
  }
}