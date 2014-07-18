<?php
/**
 * Cache class that sets HTTP cache headers to use a HTTP cache proxy like Squid or Varnish.
 * 
 * <code>
 * config/factories.yml
 * 
 * all:
 *  view_cache:
 *    class:    csHttpCacheHeaderCache
 *    param:
 *      headers:
 *        Expires:        "%EXPIRE_TIME%"
 *        Last-Modified:  "%LAST_MODIFIED%"
 *        Cache-Control:  "public"
 * </code>
 *
 * @package    symfony-snippets
 * @subpackage cache
 * @author     Christian Schaefer <caefer@ical.ly>
 * @version    SVN: $Id: $
 */
class csHttpCacheHeaderCache extends sfCache
{
  /**
   * @var headers HTTP headers to be set
   */
  private $headers = array();

 /**
  * Initializes this sfCache instance.
  *
  * Available options:
  *
  * * headers:  HTTP headers to be set (array)
  *
  * * see sfCache for options available for all drivers
  *
  * @see sfCache
  */
  public function initialize($options = array())
  {
    parent::initialize($options);

    if (!$this->getOption('headers'))
    {
      throw new sfInitializationException('You must pass a "headers" option to initialize a gujSquidCache object.');
    }

    $this->headers = $this->getOption('headers');
  }

  /**
   * @see sfCache
   */
  public function get($key, $default = null)
  {
    return $default;
  }

  /**
   * @see sfCache
   */
  public function has($key)
  {
    return false;
  }

  /**
   * @see sfCache
   */
  public function set($key, $data, $lifetime = null)
  {
    if(false === strpos($key, '/_partial/'))  // don't set cache headers for partials
    {
      if(is_object(unserialize($data)))       // don't set cache headers for pages without layout
      {
        $response = sfContext::getInstance()->getResponse();

        foreach($this->headers as $key => $value)
        {
          $value = str_replace('%EXPIRE_TIME%', gmdate("D, d M Y H:i:s", time() + $lifetime), $value);
          $value = str_replace('%LAST_MODIFIED%', gmdate("D, d M Y H:i:s", time()), $value);
          $value = str_replace('%LIFETIME%', $lifetime, $value);
          $response->setHttpHeader($key, $value, true);
        }
      }
    }
    return true;
  }

  /**
   * @see sfCache
   */
  public function remove($key)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function removePattern($pattern)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function clean($mode = sfCache::ALL)
  {
    return true;
  }

  /**
   * @see sfCache
   */
  public function getLastModified($key)
  {
    return 0;
  }

  /**
   * @see sfCache
   */
  public function getTimeout($key)
  {
    return 0;
  }
}