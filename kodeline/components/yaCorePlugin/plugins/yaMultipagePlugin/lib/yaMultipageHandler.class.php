<?php
/**
 * yaMultipageHandler manages pages.
 *
 * @package    yaMultipagePlugin
 * @subpackage handler
 * @author     pinhead
 * @version    SVN: $Id: yaMultipageHandler.class.php 2382 2010-10-04 12:28:19Z pinhead $
 */
class yaMultipageHandler
{
  /**
   * Помещение страницы первой в списке.
   * @var string
   */
  const FIRST  = 'first';

  /**
   * Помещение страницы последней в списке.
   * @var string
   */
  const LAST  = 'last';

  /**
   * Помещение страницы перед какой-либо в списке.
   * @var string
   */
  const BEFORE  = 'before';

  /**
   * Помещение страницы после какой-либо в списке.
   * @var string
   */
  const AFTER  = 'before';

  /**
   * Default page name.
   * @var string
   */
  const DEFAULT_PAGE = 'default';


  const
    HANDLER_KEY     = '__mphndlr',
    PAGE_NAME_PARAM = 'ya_multipage_page';

  protected
    $context      = null,
    $dispatcher   = null,
    $request      = null,
    $user         = null,
    $options      = array(),
    $storage      = null,
    $pages        = array(),
    $currentPage  = null,
    $authPage     = null;


  /**
   * Class constructor.
   *
   * @param yaMultipageManager  $manager  Multipage manager instance
   * @param array               $options  An associative array of options
   *
   * @throws InvalidArgumentException
   */
  public function __construct(yaMultipageManager $manager, $options = array())
  {
    $this->initialize($options);

    $this->context    = sfContext::getInstance();
    $this->dispatcher = $this->context->getEventDispatcher();
    $this->request    = $this->context->getRequest();
    $this->user       = $this->context->getUser();
    $this->manager    = $manager;
    $this->storage    = $manager->getStorage();

    // Read pages from session storage
    $pages = $this->getValues(self::HANDLER_KEY, $this->options['pages'], 'pages');

    if (empty($pages))
    {
      throw new InvalidArgumentException('No pages configured.');
    }

    // setup pages
    $this->setupPages($pages);
  }

  /**
   * Возвращает имя текущей сессии мультистраниц.
   * 
   * @return string
   */
  public function getSessionId()
  {
    return $this->storage->getId();
  }


  /**
   * Возвращает имя последней сессии.
   */
  public function getLastSessionId(sfSecurityUser $user = null)
  {
    // Определение пользователя.
    $user = ((null == $user) ? $this->getUser() : $user);

    // Выборка всех сессий пользователя.
    $arOption = $this->getManager()->getOptions();
    $pageSessions = $user->getAttribute($arOption['user_attribute'], array());

    // Сортировка сессий.
    uasort($pageSessions, create_function('$a, $b', 'if ($a == $b) { return 0; } return ($a > $b) ? -1 : 1;'));

    return key(array_slice($pageSessions, 0, 1, true));
  }

  /**
   * Возвращает объект yaMultipageManager
   * 
   * @return yaMultipageManager
   */
  public function getManager()
  {
    return $this->manager;
  }

  /**
   * Initializes this Manager instance.
   *
   * Available options:
   *   page_name_param: Page name route (URL) parameter
   *   pages:           Pages confguration array
   *
   * @param array $options  An associative array of options
   */
  public function initialize($options = array())
  {
    $this->options = array_merge(array('page_name_param' => self::PAGE_NAME_PARAM, 'pages' => array()), $options);
  }

  /**
   * getContext()
   *
   * @return sfContext
   */
  public function getContext()
  {
    return $this->context;
  }

  /**
   * getUser()
   *
   * @return sfUser
   */
  public function getUser()
  {
    return $this->user;
  }

  /**
   * Get first page instance
   *
   * @return yaMultipageBase
   */
  public function getFirstPage()
  {
    return isset($this->pages[0]) ? $this->pages[0] : null;
  }

  /**
   * addPage()
   *
   * Available actions are:
   *
   *  * yaMultiPageHandler::BEFORE
   *  * yaMultiPageHandler::AFTER
   *  * yaMultiPageHandler::LAST
   *  * yaMultiPageHandler::FIRST
   *
   * @param yaMultipageBase|array $page The page instance or array of page parameters
   * @param constant $action        The action (see above for all possible actions)
   * @param string $pivot           The field name used for AFTER and BEFORE actions
   * @param bool $saveConfig
   *
   * @return yaMultipageBase
   *
   * @throws sfException
   */
  public function addPage($page, $action = self::LAST, $pivot = null, $saveConfig = true)
  {
    if (sfConfig::get('sf_logging_enabled')) {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Begin adding page: %s', $page['name']), 'priority' => sfLogger::INFO)));
    }

    if (! is_null($pivot))
    {
      if (! $pivotPage = $this->getPage((string) $pivot)) {
        throw new sfException(sprintf('Page "%s" does not exist.', $pivot));
      }

      $pivotPosition = $this->getPageIndex($pivotPage);
    }

    if (is_array($page) && isset($page['class']))
    {
      $class = $page['class'];
      unset($page['class']);

      $page = new $class($page, $this);
    }
    else if (! $page instanceof yaMultipageBase)
    {
      throw new sfException(sprintf('Wrong argument type "%s".', gettype($page)));
    }

    switch ($action)
    {
      case self::FIRST:
        if (isset($this->pages[0]))
        {
          $next = $this->pages[0];
          $next->setPrevious($page);
          $page->setNext($next);
        }

        array_unshift($this->pages, $page);

        break;

      case self::LAST:
        $pageCount = $this->getPageCount();
        if (($pageCount > 0) && isset($this->pages[$pageCount - 1]))
        {
          $previous = $this->pages[$pageCount - 1];
          $previous->setNext($page);

          $page->setPrevious($previous);
        }

        array_push($this->pages, $page);

        break;

      case self::BEFORE:
        if (is_null($pivot))
        {
          throw new sfException(sprintf('Unable to add page "%s" without a relative page.', $page));
        }

        if (isset($this->pages[$pivotPosition - 1]))
        {
          $previous = $this->pages[$pivotPosition - 1];
          $previous->setNext($page);
          $page->setPrevious($previous);
        }

        $next = $this->pages[$pivotPosition];
        $next->setPrevious($page);
        $page->setNext($next);

        $this->pages = array_merge(
          array_slice($this->pages, 0, $pivotPosition),
          array($page),
          array_slice($this->pages, $pivotPosition)
        );

        break;

      case self::AFTER:
        if (is_null($pivot))
        {
          throw new sfException(sprintf('Unable to add page "%s" without a relative page.', $page));
        }

        $previous = $this->pages[$pivotPosition];
        $previous->setNext($page);
        $page->setPrevious($previous);

        if (isset($this->pages[$pivotPosition + 1]))
        {
          $next = $this->pages[$pivotPosition + 1];
          $next->setPrevious($page);
          $page->setNext($next);
        }

        $this->pages = array_merge(
          array_slice($this->pages, 0, $pivotPosition + 1),
          array($page),
          array_slice($this->pages, $pivotPosition + 1)
        );

        break;

      default:
        throw new sfException(sprintf('Unknown operation "%s" for page "%s".', $action, $page));
    }

    if ($saveConfig)
    {
      $this->savePagesConfig();
    }

    return $page;
  }

  /**
   * addPages()
   *
   * @param array $pages
   *
   * @return yaMultiPageHandler
   */
  public function addPages(array $pages)
  {
    foreach ($pages as $page)
    {
      $this->addPage($page, self::LAST, null, false);
    }

    $this->savePagesConfig();

    return $this;
  }

  /**
   * removePage()
   *
   * @param string|yaMultipageBase $page
   * @param bool $saveConfig
   *
   * @return yaMultipageBase|bool
   */
  public function removePage($page, $saveConfig = true)
  {
    $found = false;
    for ($i = 0, $count = count($this->pages); $i < $count; $i++)
    {
      if ((string) $page === (string) $this->pages[$i])
      {
        $found  = $this->pages[$i];
        $prev   = $found->getPrevious();
        $next   = $found->getNext();

        $this->pages = array_merge(
          array_slice($this->pages, 0, $i),
          array_slice($this->pages, $i + 1)
        );

        if ($prev)
        {
          $prev->setNext($next);
        }

        if ($next)
        {
          $next->setPrevious($prev);
        }

        $this->removeValues($found->getName());

        break;
      }
    }

    if ($found && $saveConfig)
    {
      $this->savePagesConfig();
    }

    return $found;
  }

  /**
   * removePages()
   *
   * @param array $pages
   * @param bool $saveConfig
   *
   * @return array|bool
   */
  public function removePages(array $pages, $saveConfig = true)
  {
    $found = false;

    foreach ($pages as $page)
    {
      if (!is_array($found))
      {
        $found = array();
      }
      $found[] = $this->removePage($page, false);
    }

    if ($found && $saveConfig)
    {
      $this->savePagesConfig();
    }

    return $found;
  }

  /**
   * clearPages()
   *
   * @param bool $removeValues
   *
   * @return yaMultiPageHandler
   */
  public function clearPages($removeValues = false)
  {
    $this->pages = array();

    if ($removeValues)
    {
      $this->removeAllValues();
    }

    return $this;
  }

  /**
   * setupPages()
   *
   * @param array $pages
   * @return yaMultiPageHandler
   */
  public function setupPages(array $pages)
  {
    $this->clearPages(false)->addPages($pages);

    $defaultPage = array_slice($pages, 0, 1);
    $defaultPage = array_shift($defaultPage);

    foreach ($pages as $page) {
      if (array_key_exists('default', $page) && $page['default']) {
        $defaultPage = $page;
      }
    }

    $this->setPage($defaultPage['name']);

    if (sfConfig::get('sf_logging_enabled')) {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Current page is "%s"', $defaultPage['name']), 'priority' => sfLogger::INFO)));
    }

    return $this;
  }

  /**
   */
  public function getCurrentPage($requestParam = null)
  {
    $pageName = self::DEFAULT_PAGE;

    if (! is_null($requestParam)) {
      $pageName = $this->request->getParameter($requestParam, null);
    }

    if (is_null($pageName) && is_null($this->currentPage))
    {
      $pageName = array_slice($this->pages, 0, 1);
      $pageName = array_shift($pageName['name']);

      foreach ($pages as $page) {
        if (array_key_exists('default', $page) && $page['default']) {
          $pageName = $page['name'];
        }
      }
    }

    if (sfConfig::get('sf_logging_enabled')) {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Current page is "%s"', $pageName), 'priority' => sfLogger::INFO)));
    }

    return $this->getPage($pageName);
  }

  /**
   * Inserts and returns Authentication page instance
   *
   * @param string  $pivotPage  Pivot page name
   *
   * @return yaMultipageBase
   */
  public function insertAuthPage($pivotPage, $position = self::AFTER)
  {
    $pageOptions = $this->options['auth_page'];

    $page = $this->addPage($pageOptions, $position, $pivotPage, true);

    return $page;
  }

  /**
   * Removes authentication page
   */
  public function removeAuthPage()
  {
    // softly remove auth page to avoid wrong page order
    if ($this->getPage()->getName() == $this->options['auth_page']['name'])
    {
      $authPage = $this->getPage($this->options['auth_page']['name']);
      $this->_setPage($authPage->getNext());
    }

    $this->removePage($this->options['auth_page']['name'], true);
  }

  /**
   * Checks page is an authentication page
   *
   * @param yaMultipageBase $page Page instance
   *
   * @return bool
   */
  public function isAuthPage($page)
  {
    return ($page->getName() ==  $this->options['auth_page']['name']);
  }

  public function hasPage($pageName)
  {
    // Filter pages.
    $page = array_filter($this->pages, create_function('$page', 'return $page->getName() == "'.$name.'";'));

  }

  /**
   * getPage()
   *
   * @param string $name
   *
   * @return yaMultipageBase
   */
  public function getPage($name = null)
  {
    if (null === $name)
    {
      return $this->currentPage;
    }

    $found = array_filter($this->pages, create_function('$page', 'return $page->getName() == "'.$name.'";'));
    return reset($found);
  }

  /**
   * setPage()
   *
   * @param string $name
   *
   * @return bool
   */
  public function setPage($name)
  {
    if (! $page = $this->getPage($name))
    {
      throw new yaMultipageUknownPageException(sprintf('Page "%s" not configured.', $name));
    }

    // check page order
    if (! $page->isFirst())
    {
      $current = $this->getValues(self::HANDLER_KEY, false, 'current_page');
      $currentPage = $this->getPage($current);
      if (!$currentPage || (($this->getPageIndex($currentPage) + 1) < $this->getPageIndex($page)))
      {
        throw new yaMultipageWrongOrderException('Wrong page order.');
      }
    }

    return $this->_setPage($page);
  }

  public function _setPage($page)
  {
    $this->setValues(self::HANDLER_KEY, $page->getName(), 'current_page');
    $this->currentPage = $page;

    $result = $page->setup();

    return is_bool($result) ? $result : true;
  }

  /**
   * getNextPage()
   *
   * @return yaMultipageBase
   */
  public function getNextPage()
  {
    return $this->currentPage->isLast() ? false : $this->currentPage->getNext();
  }

  /**
   * getPreviousPage()
   *
   * @return yaMultipageBase
   */
  public function getPreviousPage()
  {
    return $this->currentPage->isFirst() ? false : $this->currentPage->getPrevious();
  }

  /**
   * Retrieve pages array.
   *
   * @return array
   */
  public function getPages(array $arSteps = array())
  {
    return $this->pages;
  }

  /**
   * Retrieves page index in the page list.
   *
   * @param string|yaMultipageBase $page
   *
   * @return integer|bool
   */
  public function getPageIndex($page)
  {
    return array_search($page, $this->pages);
  }

  /**
   * Returns the number of the pages.
   *
   * @return integer
   */
  public function getPageCount()
  {
    return count($this->pages);
  }

  /**
   * Set values for the page.
   *
   * If the values for the page already exists they will be overridden.
   *
   * @param string  $page   Page name
   * @param mixed   $values Page values
   * @param string  $key    Optional key
   */
  public function setValues($page, $values, $key = null)
  {
    $skey = (null == $key) ? $page : $page . '/' . $key;

    if (sfConfig::get('sf_logging_enabled'))
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Save key "%s" to storage', $skey), 'priority' => sfLogger::INFO)));
    }

    $this->storage->write($skey, $values);
  }

  /**
   * Retrieve page values.
   *
   * @param string  $page     Page name
   * @param mixed   $default  Default value
   * @param string  $key      Optional key
   *
   * @return mixed
   */
  public function getValues($page, $default = null, $key = null)
  {
    $skey = (null == $key) ? $page : $page . '/' . $key;

    $retval = $this->storage->read($skey);
    if (null == $retval)
    {
      $retval = $default;
    }

    return $retval;
  }

  /**
   * Retrieve merged values of a steps.
   *
   * @param array  $arSteps   List of the steps for fetch values.
   * @return array
   */
  public function getMergedValues(array $arSteps = array())
  {
    $pages = $this->getPages();

    $arRetValue = array(); $_ =& $pages;
    foreach ($pages as $page)
    {
      $arStepValues = $this->storage->read($page->getName());
      $arRetValue = array_merge($arRetValue, (is_array($arStepValues) ? $arStepValues : array($arStepValues)));
    }

    return $arRetValue;
  }

  public function getAllValues($page, $default = null, $key = null)
  {
    $skey = (null == $key) ? $page : $page . '/' . $key;

    $retval = $this->storage->read($skey);
    if (null == $retval)
    {
      $retval = $default;
    }

    return $retval;
  }



  public function getValue($page, $name, $default = null)
  {
    $pageValues = $this->storage->read($page);

    if (! array_key_exists($name, $pageValues)) return null;
    return $pageValues[$name];
  }

  /**
   * Indicates whether or not a page values exists.
   *
   * @param  string $page Page name
   * @param  string $key  Optional key
   *
   * @return bool
   */
  public function hasValue($page, $key = null)
  {
    return (null !== $this->getValue($name, null, $key));
  }

  /**
   * Remove values for the page.
   *
   * @param string $page    A page name
   * @param mixed $default  The default page values
   * @param string $key     The values' key
   *
   * @return mixed The page values, if the values was removed, otherwise null
   */
  public function removeValues($page, $default = null, $key = null)
  {
    $skey = (null == $key) ? $page : $page . '/' . $key;

    $retval = $this->storage->remove($skey);
    if (null == $retval)
    {
      $retval = $default;
    }

    return $retval;
  }

  /**
   * Remove values of all pages.
   *
   * @param string|yaMultipageBase $fromPage Optional. If defined, only values of the next pages will be deleted.
   */
  public function removeAllValues($fromPage = null)
  {
    if (is_null($fromPage) || (false === $offset = $this->getPageIndex($fromPage)))
    {
      $this->storage->erase();
    }
    else
    {
      for ($i = $offset, $count = count($this->pages); $i < $count; $i++)
      {
        $pageName = $this->pages[$i]->getName();
        $this->storage->remove($pageName);
      }
    }
  }

  /**
   * addValues()
   *
   * @param string $page  A page name
   * @param mixed $values The page values
   * @param string $key   The values' key
   *
   * @return array
   *
   * @throws sfException
   */
  public function addValues($page, $values, $key = null)
  {
    $storedValues = $this->getValues($page, array(), $key);

    if (! is_array($storedValues) || ! is_array($values))
    {
      throw new sfException($sprintf('Only array values can be merged, "%s" and "%s" given.', gettype($storedValues), gettype($values)));
    }

    $values = array_merge($storedValues, $values);
    $this->setValues($page, $values, $key);

    return $values;
  }

  /**
   * addValue()
   *
   * @param string $page  A page name
   * @param mixed $values The page values
   * @param string $key   The values' key
   *
   * @return array
   *
   * @throws sfException
   */
  public function addValue($page, $value, $key = null)
  {
    return $this->addValues($page, $value, $key);
  }

  /**
   * savePagesConfig()
   *
   * @return array Stored array of pages
   */
  protected function savePagesConfig()
  {
    $pages = array();
    foreach ($this->pages as $page)
    {
      $pages[] = $page->getConfig();
    }

    $this->setValues(self::HANDLER_KEY, $pages, 'pages');

    return $pages;
  }

  /**
   *
   */
  public function generatePageUrl($page, $parameters = array(), $absolute = false)
  {
    // Define parameters list.
    $parameters = array_merge(array(
      $this->options['page_name_param'] => $page->getName(),
      $this->manager->getOption('session_name') => $this->manager->getId()
    ),
    $parameters);

    return $this->context->getRouting()->generate($this->options['walk_route'], $parameters, $absolute);
  }

  /**
   * Redirects current request to next page.
   *
   * @param string $statusCode Status code (default to 302)
   */
  public function loadNext($parameters = array(), $statusCode = 302)
  {
    if ($this->getPage()->isLast())
    {
      throw new yaMultipageHandlerException('Could not redirect to next page. Current page is the last page.');
    }

    $this->redirect($this->generatePageUrl($this->getNextPage(), $parameters), $statusCode);
  }

  /**
   * Redirects current request to a new URL.
   *
   * 2 URL formats are accepted :
   *  - a full URL: http://www.google.com/
   *  - an internal URL (url_for() format): module/action
   *
   * This method stops the action. So, no code is executed after a call to this method.
   *
   * @param string $url        Url
   * @param string $statusCode Status code (default to 302)
   *
   * @throws sfStopException
   */
  public function redirect($url, $statusCode = 302)
  {
    $this->context->getController()->redirect($url, 0, $statusCode);
    throw new sfStopException();
  }
}