<?php

/**
 * yaMultipageManager manages multipage sessions.
 *
 * @package    yaMultipagePlugin
 * @subpackage session
 * @author     pinhead
 * @version    SVN: $Id: yaMultipageManager.class.php 1459 2010-04-02 10:29:01Z pinhead $
 */
class yaMultipageManager
{
  /**
   * Текущий компонент системы.
   */
  protected $component;

  protected
    $context  = null,
    $user     = null,
    $storage  = null,
    $id       = null,
    $sessions = array(),
    $options  = array();

  /**
   * __construct
   * @see initialize()
   */
  public function __construct($options = array(), $component = null)
  {
    $this->initialize($options);

    $this->context     = sfContext::getInstance();
    $this->dispatcher  = $this->context->getEventDispatcher();
    $this->request     = $this->context->getRequest();
    $this->response    = $this->context->getResponse();
    $this->user        = $this->context->getUser();

    if (null != $component) $this->setParentComponent($component);

    // check storage parameters
    if (empty($this->options['storage']) || empty($this->options['storage']['class'])) {
      throw new InvalidArgumentException('yaMultipageManager requires storage option.');
    }

    // Fetch user multipage sessions.
    $this->sessions = $this->user->getAttribute($this->options['user_attribute'], array());
  }

  /**
   * Устанавливает текущий компонент (экшен) системы.
   * @param sfComponent $component Объект компонента.
   */
  public function setParentComponent(sfComponent $component)
  {
    $this->component = $component;
  }

  /**
   * Возвращает текущий компонент (экшен) системы.
   * @return sfComponent
   */
  public function getParentComponent()
  {
    return $this->component;
  }

  /**
   * Initializes this Manager instance.
   *
   * Available options:
   *   auto_shutdown:   Whether to automatically save the changes to the session (true by default)
   *   user_attribute:  User storage attribute to retrieve current sessions state
   *   timeout:         Session timeout
   *   limit:           Session limit
   * @param  array $options  An associative array of options
   */
  public function initialize($options = array())
  {
    $this->options = array_merge(array('user_attribute' => 'ya_multipage_sessions', 'timeout' => 86400, 'limit' => 3), $options);
  }

  /**
   * Retrieves manager options.
   *
   * @return array
   */
  public function getOptions()
  {
    return $this->options;
  }

  /**
   * Retrieves manager option value.
   *
   * @param $option
   *
   * @return mixed
   */
  public function getOption($name)
  {
    return isset($this->options[$name]) ? $this->options[$name] : null;
  }

  /**
   * Retrieves session id.
   *
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }

  /**
   * Creates and return new session storage instance.
   *
   * @param string  $id     Session id
   * @param string  $class  Storage class
   * @param array   $param  Storage parameters
   *
   * @return sfStorage
   */
  protected function getSessionStorage($id, $class, $param = array())
  {
    $param['id'] = $id;
    return new $class($param);
  }

  /**
   * Retrieves current session storage.
   *
   * @return sfStorage
   */
  public function getStorage()
  {
    return $this->storage;
  }

  /**
   * Starts session
   */
  public function startSession()
  {
    // check user multipage sessions timeout and limits
    $this->checkSessions();

    // get multipage session id
    $id = $this->request->getParameter($this->options['session_name'], null);

    if ($id == 'new') $id = null;

    /*
    if (! empty($id) && ! $this->sessionExists($id))
    {
      throw new yaMultipageUknownSessionException('Session not initialized.');
    }
    */

    // Normalize storage parameters.
    $this->options['storage']['param'] = is_array($this->options['storage']['param']) ? $this->options['storage']['param'] : array();

    // Create storage instance.
    $this->storage = $this->getSessionStorage($id, $this->options['storage']['class'], $this->options['storage']['param']);

    // Set current session id from session storage.
    $this->id = $this->storage->getId();

    // Update current session.
    $this->sessions[$this->id]['last_activity'] = time();
    //usort($this->sessions, create_function('$a, $b', 'if ($a == $b) { return 0; } return ($a < $b) ? -1 : 1;'));

    // Add current session to user's session holder.
    $this->user->setAttribute($this->options['user_attribute'], $this->sessions);
  }

  /**
   * Walk through user sessions, deletes expired sessions.
   */
  public function checkSessions()
  {
    $lfuId = null;

    // check expired sessions
    foreach ($this->sessions as $id => $session)
    {
      /*
      if ($this->options['timeout'] < (time() - $session['last_activity']))
      {
        $this->removeSession($id, false);
      }
*/
      if (null == $lfuId || (isset($this->sessions[$lfuId]) && $this->sessions[$lfuId]['last_activity'] > $session['last_activity']))
      {
        $lfuId = $id;
      }
    }

    // check session limit
    if (count($this->sessions) > $this->options['limit'] && null != $lfuId && isset($this->sessions[$lfuId]))
    {
      $this->removeSession($lfuId);
    }
  }

  /**
   * Remove user session and storage data.
   *
   * @param string  $id   Session ID
   */
  public function removeSession($id, $save = true)
  {
    // destroy session data
    $this->getSessionStorage($id, $this->options['storage']['class'], $this->options['storage']['param'])->erase();

    // remove session
    if ($this->sessionExists($id))
    {
      unset($this->sessions[$id]);
    }

    // save user sessions
    if ($save)
    {
      $this->user->setAttribute($this->options['user_attribute'], $this->sessions);
    }
  }

  /**
   * Check session exists.
   */
  public function sessionExists($id)
  {
    return ! empty($this->sessions[$id]);
  }
}
