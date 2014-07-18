<?php
/**
 * yaMultipageBase class.
 *
 * @package     yaMultipagePlugin
 * @subpackage  multipage
 * @author      Alexey Chugarev <chugarev@gmail.com>
 * @version     $Id$
 */
abstract class yaMultipageBase
{
  /**
   * Method name for fetch page.
   * @var string
   */
  const FETCH_METHOD_NAME = 'fetch';

  /**
   * Return flag for goto prev page
   * @var string
   */
  const PAGE_PREVIOUS = 'prev';

  /**
   * Return flag for goto next page
   * @var string
   */
  const PAGE_NEXT = 'next';

  /**
   * Return flag for goto next page,
   * before try save current page.
   * 
   * @var string
   */
  const PAGE_NEXT_AND_SAVE = 'save_and_next';

  /**
   * Return flag for goto last page
   * @var string
   */
  const PAGE_LAST = 'last';

  /**
   * Имя шаблона страницы.
   * @var string
   */
  public $template = null;

  protected
    $handler  = null,
    $next     = null,
    $previous = null,
    $isFlush  = false,
    $isSecure = false,
    $parameters = null,
    $varHolder = null;

  /**
   * System logger.
   * @var sfLogger
   */
  protected static $sfLogger = null;

  /**
   * Constructor.
   *
   * @param array $parameters
   */
  public function __construct($parameters = array(), yaMultipageHandler $handler = null)
  {
    // Установка значений параметрам страницы
    foreach ($parameters as $name => $value)
    {
      $this->setParameter($name, $value);
    }

    // Проверка установленного значения.
    if (null == $this->getParameter('name', null))
    {
      throw new sfException(sprintf('Parameter "%s" is not defined.', 'name'));
    }

    // Установка флага защищенной страницы.
    $this->isSecure = (bool) $this->getParameter('is_secure', false);

    // Установка флага флашевой страницы.
    $this->isFlush = (bool) $this->getParameter('is_flush', false);

    // Установка хендлера.
    $this->setHandler($handler);

    // Установка параметров.
    $this->varHolder = new sfParameterHolder();

    $this->configure();
  }

  /**
   * Конфигурирование 
   */
  public function configure(){ }

  /**
   * Возвращает нормализованное наименование параметра.
   * 
   * @param string $name Имя параметра.
   * @return string
   */
  protected function normalizeParamName($name)
  {
    return sfInflector::underscore($name);
  }

  /**
   * Установка значения параметра.
   * 
   * @param string $name Имя параметра.
   * @param mixed $value Значение параметра.
   */
  public function setParameter($name, $value)
  {
    $this->parameters[$this->normalizeParamName($name)] = $value;

    return $this;
  }

  /**
   * Возвращает значение параметра.
   * 
   * @param string name Значение параметра.
   * @param mixed default Возвращаемое значение, если параметр не найден.
   */
  public function getParameter($name, $default = null)
  {
    if (! array_key_exists($this->normalizeParamName($name), $this->parameters)) return $default;

    return $this->parameters[$this->normalizeParamName($name)];
  }

  /**
   * Return system logger object.
   * @return sfLogger
   */
  protected function getLogger()
  {
    if (null == self::$sfLogger)
    {
      self::$sfLogger = $this->getHandler()->getContext()->getLogger();
    }
    return self::$sfLogger;
  }

  /**
   * getName()
   * @return string
   */
  public function getName()
  {
    return $this->getParameter('name', null);
  }

  /**
   * getTitle()
   * @return string
   */
  public function getTitle()
  {
    return $this->getParameter('title', null);
  }

  /**
   * getBrief()
   * @return string
   */
  public function getBrief()
  {
    return $this->getParameter('brief', null);
  }

  /**
   * setUrl()
   * @param string  $url
   */
  public function setUrl($url)
  {
    return $this->getParameter('url', null);
  }

  /**
   * Возвращает url текущей страницы.
   * @return string
   */
  public function getUrl($parameters = array(), $absolute = false)
  {
    return $this->getHandler()->generatePageUrl($this, $parameters, $absolute);
  }

  /**
   * getPage()
   * @return yaMultiPage
   */
  public function getPage($pageName)
  {
    return $this->getHandler()->getPage($pageName);
  }

  /**
   * getNext()
   * @return yaMultiPage
   */
  public function getNext()
  {
    return $this->next;
  }

  /**
   * setNext()
   * @param yaMultiPage $next
   * @return yaMultiPage
   */
  public function setNext($next)
  {
    $this->next = $next;
    return $this;
  }

  /**
   * getPrevious()
   * @return yaMultiPage
   */
  public function getPrevious()
  {
    return $this->previous;
  }

  /**
   * setPrevios()
   * @param yaMultiPage $previous
   * @return yaMultiPage
   */
  public function setPrevious($previous)
  {
    $this->previous = $previous;
    return $this;
  }

  /**
   * Возвращает хендлер.
   * 
   * @return yaMultiPageHandler
   */
  public function getHandler()
  {
    return $this->handler;
  }

  /**
   * Устанавливает хендлер обработки запроса.
   * 
   * @param yaMultiPageHandler $handler
   * @return yaMultiPageHandler
   */
  public function setHandler(yaMultipageHandler $handler)
  {
    $this->handler = $handler;
    return $this;
  }

  /**
   * Retrieves handler values for specific page.
   * @param string|yaMultiPage $page
   * @return mixed
   */
  public function getHandlerValues($page = null)
  {
    if (is_null($page))
    {
      $page = $this->getName();
    }
    return $this->getHandler()->getValues(strval($page));
  }

  /**
   * isLast()
   * @return bool
   */
  public function isLast()
  {
    return (null == $this->next);
  }

  /**
   * isFirst()
   * @return bool
   */
  public function isFirst()
  {
    return (null == $this->previous);
  }

  /**
   * isSecure()
   * @return bool
   */
  public function isSecure()
  {
    return $this->isSecure;
  }

  /**
   * isFlush()
   * @return bool
   */
  public function isFlush()
  {
    return $this->isFlush;
  }

  /**
   * getTemplateName()
   * @param string $name Optional. Use custom name instead of current page name.
   * @return string
   */
  public function getTemplateName($name = null)
  {
    if (!is_null($this->template))
    {
      return $this->template;
    }

    $template     = sfInflector::camelize($name ? $name : $this->getName());
    $template[0]  = strtolower($template[0]);
    return $template;
  }

  /**
   */
  public function setForm($form)
  {
    try {
      if (! is_object($form))
      {
        if (! @strlen($form)) {
          throw new sfException('Form cannot be init!');
        }

        $this->form = $form;
      }
      elseif($form instanceof sfForm) {
        $this->form = $form;
      }
    }
    catch(sfException $e) {

    }
  }

  /**
   * getForm()
   * @return sfForm
   */
  public function getForm($mForm = null)
  {
    if (null != $mForm)
    {
      $this->form = $mForm;
    }

    if (! is_null($this->form) && ! $this->form instanceof sfForm)
    {
      $class = $this->form;
      $this->form = new $class($this->filterFormDefaults(), $this->filterFormOptions());      
    }

    return $this->form;
  }

  /**
   */
  public function hasForm()
  {
    return !is_null($this->form);
  }

  /**
   * filterFormDefaults()
   * @param array $defaults
   * @return array
   */
  public function filterFormDefaults(array $defaults = array())
  {
    return $defaults;
  }

  /**
   * filterFormOptions()
   * @param array $options
   * @return array
   */
  public function filterFormOptions(array $options = array())
  {
    return $options;
  }

  /**
   * Возвращает конфигурацию страницы.
   * 
   * @return array
   */
  public function getConfig()
  {
    return array_merge(array('class' => get_class($this)), $this->parameters);
  }

  /**
   * Processes the form with the current request.
   * @param sfRequest A sfRequest instance
   * @return mixed False if the form is not valid
   * @throws sfException If the name format is not recognized
   */
  public function processForm(sfRequest $request)
  {
    $form = $this->getForm();
    if (null == $form)
    {
      return true;
    }

    if ('%s' == $nameFormat = $form->getWidgetSchema()->getNameFormat())
    {
      $data = $request->isMethod('post') ? $request->getPostParameters() : $request->getGetParameters();
    }
    else if ('[%s]' == substr($nameFormat, -4))
    {
      $data = $request->getParameter(substr($nameFormat, 0, -4));
    }
    else
    {
      throw new sfException(sprintf('%s cannot understand the name format "%s"', __METHOD__, $nameFormat));
    }

    $form->bind($data, $request->getFiles());
    $result = $form->isValid() ? $this->save() : false;

    return is_null($result) ? true : $result;
  }

  /**
   * Magic method.
   * @return string
   */
  public function __toString()
  {
    return $this->getName();
  }

  /**
   * setup()
   * @return bool|void
   */
  public function setup()
  {
  }

  /**
   * fetch()
   */
  public function fetch(sfWebRequest $request = null)
  {
  }

  /**
   * save()
   */
  public function save()
  {
    return false;
  }

  /**
   * getUser()
   * @return myUser
   */
  protected function getUser()
  {
    return $this->getHandler()->getUser();
  }

  /**
   * goSsl()
   *
   */
  protected function goSsl()
  {
    // get the cool stuff
    $context = $this->getHandler()->getContext();
    $request = $context->getRequest();

    // only redirect HEAD and GET http(s) requests
    if (in_array($request->getMethod(), array(sfRequest::HEAD, sfRequest::GET)) && substr($request->getUri(), 0, 4) == 'http')
    {
      $controller = $context->getController();

      // get the current action instance
      $actionEntry    = $controller->getActionStack()->getLastEntry();
      $actionInstance = $actionEntry->getActionInstance();

      // request is SSL secured
      if ($request->isSecure())
      {
        // but SSL is not allowed
        if (!$actionInstance->sslAllowed())
        {
          $controller->redirect($actionInstance->getNonSslUrl());
          exit();
        }
      }
      // request is not SSL secured, but SSL is required
      elseif ($actionInstance->sslAllowed())
      {
        $controller->redirect($actionInstance->getSslUrl());
        exit();
      }
    }
  }

  /**
   * Gets the sfParameterHolder object that stores the template variables.
   *
   * @return sfParameterHolder The variable holder.
   */
  public function getVarHolder()
  {
    return $this->varHolder;
  }

  /**
   * Sets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->setVar('name', 'value')</code>
   *
   * @param string $key   The variable name
   * @param string $value The variable value
   *
   * @return boolean always true
   *
   * @see setVar()
   */
  public function __set($key, $value)
  {
    return $this->varHolder->setByRef($key, $value);
  }

  /**
   * Gets a variable for the template.
   *
   * This is a shortcut for:
   *
   * <code>$this->getVar('name')</code>
   *
   * @param string $key The variable name
   *
   * @return mixed The variable value
   *
   * @see getVar()
   */
  public function & __get($key)
  {
    return $this->varHolder->get($key);
  }
}