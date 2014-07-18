<?php
/**
 * Extends symfony sfWebResponse.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.response
 * @author      pinhead
 * @author      chuga
 * @version     SVN: $Id: yaWebResponse.class.php 2824 2010-12-22 15:46:12Z pinhead $
 */
class yaWebResponse extends sfWebResponse
{
  /**
   * Include code locations.
   * @var string
   */
  const LOCATION_HEAD = 'HEAD', LOCATION_BODY = 'BODY';

  /**
   * Include methods of the load.
   * @var string
   */
  const METHOD_ASYNC = 'ASYNC';

  /**
   * Container for scripts to loads.
   * @var array
   */
  private $arScripts = array();

  /**
   * Probably locations for scripts.
   * @var array
   */
  private $arScriptLocations;

  /**
   * Container for scripts to include inline.
   * @var array
   */
  private $arInlineScripts = array();

  /**
   * Probably locations for inline scripts.
   * @var array
   */
  private $arInlineScriptLocations = array(self::LOCATION_HEAD => array(self::FIRST => null), self::LOCATION_BODY => array(self::FIRST => null, self::LAST => null));

  /**
   * Current culture
   * @var string
   */
  private $culture;

  /**
   * Indicates that request has been sent by a human (not bot/crawler)
   * and the application will send html for a browser.
   * @var bool
   */
  private $isHtmlForHuman = true;

  /**
   * Initializes this sfWebResponse.
   * @see sfWebResponse
   */
  public function initialize(sfEventDispatcher $dispatcher, $options = array())
  {
    parent::initialize($dispatcher, $options);

    // List of the probably locations.
    $this->arScriptLocations = array(self::LOCATION_HEAD, self::LOCATION_BODY, self::METHOD_ASYNC);
    
    // Prepare container for scripts by locations/position.
    $this->arScripts = array_combine($this->arScriptLocations, array_fill(0, count($this->arScriptLocations), $this->javascripts));

    // Prepare container for inline scripts by locations/position.
    $_ =& $this->arInlineScriptLocations;
    foreach($this->arInlineScriptLocations as $location => $positions) {
      $this->arInlineScripts[$location] = $positions;
    }
    
    // Subscribe for events.
    $this->dispatcher->connect('user.change_culture', array($this, 'listenToChangeCultureEvent'));
    $this->dispatcher->connect('user.remember_me', array($this, 'listenToRememberMeEvent'));
    $this->dispatcher->connect('user.sign_out', array($this, 'listenToSignOutEvent'));
  }

  /**
   * Adds javascript code to the current web response.
   * @param string $file      The JavaScript file
   * @param string $position  Position
   * @param string $options   Javascript options
   * @return yaWebResponse
   */
  public function addJavascript($file, $position = '', $options = array())
  {
    if (! $this->isHtmlForHuman) { return $this; }

    // Define location of the script.
    $arOptions = array_change_key_case($options, CASE_UPPER);
    $sLocation = (array_key_exists('LOCATION', $arOptions) ? $arOptions['LOCATION'] : self::LOCATION_HEAD);
    $sMethod = (array_key_exists('METHOD', $arOptions) ? $arOptions['METHOD'] : null);
    $sCallback = (array_key_exists('CALLBACK', $arOptions) ? $arOptions['CALLBACK'] : null);
    
    if (self::METHOD_ASYNC === strtoupper($sMethod))
    {
      $this->addScriptFileAsync($file, $position, $options); 
    }
    else {
      unset($options['callback']);
      if (self::LOCATION_BODY === strtoupper($sLocation))
      {
        $this->addScriptFile($file, $position, $options);
      }
      else {
        parent::addJavascript($file, $position, $options);  
      }
    }

    return $this;
  }

  /**
   * {@inheritDoc}
   */
  public function addStylesheet($file, $position = '', $options = array())
  {
    if (! $this->isHtmlForHuman) { return $this; }

    parent::addStylesheet($file, $position, $options);
    return $this;
  }

  /**
   * Returns the available document locations for javascripts.
   * @return array
   */
  public function getLocations()
  {
    return $this->arScriptLocations;
  }

  /**
   * Validate a content position name.
   *
   * @param  string $position
   * @throws InvalidArgumentException if the position is not available
   */
  protected function validateFilePosition($location, $position)
  {
    if (! array_key_exists($location, $this->arScripts))
    {
      throw new InvalidArgumentException(sprintf('The location "%s" does not exist (available locations: %s).', $position, implode(', ', array_keys($this->getLocations()))));
    }

    if (! array_key_exists($position, $this->arScripts[$location]))
    {
      throw new InvalidArgumentException(sprintf('The position "%s" does not exist (available positions: %s).',  $position, implode(', ', $this->positions)));
    }
  }

  /**
   * Add current script file to location of web response.
   * 
   * @param string $location    Document position
   * @param string $file        The file to add.
   * @param string $position    The position
   * @param string $options     Javascript options
   */
  public function addFileToLocation($location, $file, $position = self::MIDDLE, $options = array())
  {
    if (! $this->isHtmlForHuman) { return $this; }

    $this->validateFilePosition($location, $position);
    $this->arScripts[$location][$position][$file] = $options;
    return $this;
  }

  /**
   * Adds positioned script file to the end of the document.
   * 
   * @param string $file        The JavaScript file
   * @param string $position    The position
   * @param string $options     Javascript options
   */
  public function addScriptFile($file, $position = self::MIDDLE, $options = array())
  {
    $this->addFileToLocation(self::LOCATION_BODY, $file, $position, $options);
    return $this;
  }

  /**
   * Adds positioned script file to the end of the document and for load by async method.
   * 
   * @param string $file        The JavaScript file
   * @param string $position    The position
   * @param string $options     Javascript options
   */
  public function addScriptFileAsync($file, $position = self::MIDDLE, $options = array())
  {
    $this->addFileToLocation(self::METHOD_ASYNC, $file, $position, $options);
    return $this;
  }

  /**
   * Return true if location has content.
   * 
   * @return boolean
   */
  public function hasFileScrips($location, $position = null)
  {
    if (! array_key_exists($location, $this->arScripts)) return false;

    $_ =& $this->arScripts[$location];
    if (null === $position)
    {
      foreach($this->arScripts[$location] as $position => $srcipts)
      {
        if (count($srcipts)) return true;
      }
    }
    else {
      return (0 != count($this->arScripts[$location][$position]));
    }

    return false;
  }

  /**
   */
  public function getFilesByLocation($location, $position = self::ALL)
  {
    if (self::ALL === $position)
    {
      $javascripts = array();
      foreach($this->getPositions() as $position)
      {
        foreach($this->arScripts[$location][$position] as $file => $options)
        {
          $javascripts[$file] = $options;
        }
      }

      return $javascripts;
    }
    else if (self::RAW === $position)
    {
      return $this->arScripts[$location];
    }

    return $this->arScripts[$location][$position];
  }

  /**
   * Returns the available document locations for inline scripts.
   * @return array
   */
  public function getInlineLocations()
  {
    return array_keys($this->arInlineScriptLocations);
  }

  /**
   * Returns the available document positions for inline scripts.
   * @return array
   */
  public function getInlinePositions($location)
  {
    return array_keys($this->arInlineScriptLocations[$location]);
  }

  /**
   * Validate a inline position name.
   *
   * @param  string $position
   * @throws InvalidArgumentException if the position is not available
   */
  protected function validateInlinePosition($location, $position)
  {
    if (! array_key_exists($location, $this->arInlineScripts))
    {
      throw new InvalidArgumentException(sprintf('The location "%s" does not exist (available locations: %s).', $position, implode(', ', $this->getInlineLocations())));
    }

    $locationPositions = $this->arInlineScriptLocations[$location];
    if (! array_key_exists($position, $locationPositions))
    {
      throw new InvalidArgumentException(sprintf('The position "%s" does not exist (available positions: %s).', $position, implode(', ', $this->getInlinePositions($location))));
    }
  }

  /**
   * Adds inline javascript code to the current web response.
   * 
   * @param string $content     Javascript code content
   * @param string $docPosition Position inside HTML document where content will be rendered
   * @return yaWebResponse
   */
  public function addInlineScript($content, $location = self::LOCATION_BODY, $position = self::LAST)
  {
    if (! $this->isHtmlForHuman) { return $this; }

    if (strlen($content))
    {
      $this->validateInlinePosition($location, $position);
      $this->arInlineScripts[$location][$position] .= sprintf(PHP_EOL . '%s' . PHP_EOL, trim($content));
    }

    return $this;
  }

  /**
   * Retrieves inline javascript code from the current web response.
   * 
   * @param  string $docPosition  The position
   * @return array An associative array of javascript files as keys and options as values
   */
  public function getInlineScripts($location = self::LOCATION_BODY, $position)
  {
    if (self::RAW === $position)
    {
      return $this->arInlineScripts[$location];
    }

    return $this->arInlineScripts[$location][$position];
  }

  /**
   * Removes positioned javascript files from the current web response.
   * 
   * @param string $location Document position
   * @param string $position    The position
   * @return yaWebResponse
   */
  public function clearDocumentJavascripts($location = self::ALL, $position = self::ALL)
  {
    $empty = array_combine($this->positions, array_fill(0, count($this->positions), array()));
    if (self::ALL == $location)
    {
      $this->documentJavascripts = 
        array_combine($this->javascriptPositions, array_fill(0, count($this->javascriptPositions), $empty));
    }
    elseif (self::ALL == $position)
    {
      $this->documentJavascripts[$location] = $empty;
    }
    else
    {
      $this->documentJavascripts[$location][$position] = array();
    }

    return $this;
  }

 	/**
   * Removes all javascript files from the current web response.
   * 
   * @param string $position	The position
   * @return yaWebResponse
   */
  public function clearJavascripts($location = self::ALL, $position = self::ALL)
  {
    if (self::ALL == $position)
    {
      $this->javascripts = array_combine($this->positions, array_fill(0, count($this->positions), array()));
    }
    else
    {
      $this->validatePosition($position);
      $this->javascripts[$position] = array();
    }

    return $this;
  }

  /**
   * Listens to the user.change_culture event.
   * @param sfEvent An sfEvent instance
   */
  public function listenToChangeCultureEvent(sfEvent $event)
  {
    $this->culture = $event['culture'];
  }

  /**
   * Listens to the user.remember_me event.
   * @param sfEvent An sfEvent instance
   */
  public function listenToRememberMeEvent(sfEvent $event)
  {
    $this->setCookie($this->getRememberCookieName(), $event['remember_key'], time() + $event['expiration_age']);
  }

  /**
   * Listens to the user.sign_out event.
   * @param sfEvent An sfEvent instance
   */
  public function listenToSignOutEvent(sfEvent $event)
  {
    $this->setCookie($this->getRememberCookieName(), '', time() - $event['expiration_age']);
  }

  /**
   * Retrieves Remember Me cookie name.
   * @return string
   */
  public function getRememberCookieName()
  {
    return sfConfig::get('app_sf_guard_plugin_remember_cookie_name', 'ya_remember_' . yaProject::getHash());
  }

  /**
   * Check if it is a HTML response.
   */
  public function isHtml()
  {
    return strpos($this->getContentType(), 'text/html') === 0;
  }

  /**
   * Means that request has been sent by a human, and the application will send html for a browser.
   * CLI, ajax and flash are NOT human.
   * @return boolean $human
   */
  public function isHtmlForHuman()
  {
    return $this->isHtmlForHuman;
  }

  /**
   * @see isHtmlForHuman()
   */
  public function setIsHtmlForHuman($val)
  {
    $this->isHtmlForHuman = (bool) $val;
  }


  /**
   * Check exists meta value.
   *
   * @return string name
   */
  public function hasMeta($name)
  {
    return array_key_exists(strtolower($name), $this->metas);
  }

  /**
   * Retrieves meta header by name.
   *
   * @param string $name
   * @param string $default
   * @return array List of meta headers
   */
  public function getMeta($name, $default = null)
  {
    return ($this->hasMeta($name) ? $this->metas[strtolower($name)] : $default);
  }

  /**
   * {@inheritDoc}
   */
  public function send()
  {
    parent::send();

    // Flush all data response if its posiible.
    if (function_exists('fastcgi_finish_request')) {
      fastcgi_finish_request();
    }

    // Send event about end of content.
    ya::getEventDispatcher()->notify(new sfEvent(null, 'response.sent'));
  }

  /**
   * Tune output for ajax request.
   * 
   * @param string $contentType Content type for output.
   * @param string $requestFormat Set request format (use for select output template).
   */
  public function xmlHttpResponse($contentType = 'application/json; charset=utf-8', $requestFormat = 'json')
  {
    // Disable layout decoration for last (current) action.
    yaContext::getInstance()->getController()->getActionStack()->getLastEntry()->getActionInstance()->setLayout(false);

    // Disable debug panel.
    sfConfig::set('sf_web_debug', false);

    // Set response content-type.
    yaContext::getInstance()->getResponse()->setContentType($contentType);

    // Set request format.
    yaContext::getInstance()->getRequest()->setRequestFormat($requestFormat);
  }
}