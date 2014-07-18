<?php
/**
 * BaseyaMultipageActions class.
 *
 * @package    yaMultipagePlugin
 * @subpackage yaMultipage
 * @author     pinhead
 * @version    $Id$
 */
class BaseyaMultipageActions extends yaBaseActions
{
  /**
   * Configuration name.
   * @var string
   */
  protected $configurationName = null;

  /**
   * {@inheritDoc}
   * 
   * Request types supported:
   *   txt:  text/plain
   *   js:   [application/javascript, application/x-javascript, text/javascript]
   *   css:  text/css
   *   json: [application/json, application/x-json]
   *   xml:  [text/xml, application/xml, application/x-xml]
   *   rdf:  application/rdf+xml
   *   atom: application/atom+xml
   */
  public function preExecute()
  {
    // Define action params for configuration.
    if (! strlen(trim($this->configurationName))) {
      throw new yaMultipageConfigException($this->getContext()->getI18N()->__('Конфигурация не указана!', null, 'multipage'));
    }

    // Define action params for configuration.
    if (! yaMultipageConfig::has($this->configurationName)) {
      throw new yaMultipageConfigException(sprintf($this->getContext()
        ->getI18N()->__('Не найдена конфигурация "%s"!', null, 'multipage'), $this->configurationName));
    }

    // Check exists param "action" for configuration.
    if (! yaMultipageConfig::has($this->configurationName . '/action')) {
      throw new yaMultipageConfigException(sprintf($this->getContext()
        ->getI18N()->__('Не найдена запись "%s" для конфигурации "%s"!', null, 'multipage'), 'action', $this->configurationName));
    }

    // Check exists param "session" for configuration.
    if (! yaMultipageConfig::has($this->configurationName . '/session')) {
      throw new yaMultipageConfigException(sprintf($this->getContext()
        ->getI18N()->__('Не найдена запись "%s" для конфигурации "%s"!', null, 'multipage'), 'session', $this->configurationName));
    }

    // Check exists param "handler" for configuration.
    if (! yaMultipageConfig::has($this->configurationName . '/handler')) {
      throw new yaMultipageConfigException(sprintf($this->getContext()
        ->getI18N()->__('Не найдена запись "%s" для конфигурации "%s"!', null, 'multipage'), 'handler', $this->configurationName));
    }

    // Define action options for multipage.
    $actionOptions = yaMultipageConfig::get($this->configurationName . '/action');

    // Define format.
    $format = strtolower($this->request->getParameter($actionOptions['format_parameter'], $this->request->getParameter('sf_format', 'html')));

    // Define output format.
    switch ($format)
    {
      case 'json': $this->getResponse()->setContentType('application/json'); break;
      case 'js': $this->getResponse()->setContentType('application/javascript'); break;
      case 'css': $this->getResponse()->setContentType('text/css'); break;
      case 'xml': $this->getResponse()->setContentType('text/xml'); break;
      case 'rdf': $this->getResponse()->setContentType('application/rdf+xml'); break;
      case 'atom': $this->getResponse()->setContentType('application/atom+xml'); break;
      default: $this->getResponse()->setContentType('text/html'); break;
    }
  }

  /**
   * executeIndex()
   *
   * @param sfRequest $request
   */
  public function executeIndex(sfWebRequest $request) { }

  /**
   * Process multipage request.
   * 
   * @param sfRequest $request
   */
  public function executeProcess(sfWebRequest $request)
  {
    try
    {
      // Fetch options.
      $options = yaMultipageConfig::get($this->configurationName);

      // Initiate multipage session manager.
      $manager = new yaMultipageManager(yaMultipageConfig::get($this->configurationName . '/session'), $this);

      // Logging about session.
      if (sfConfig::get('sf_logging_enabled')) {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Start miltipage session', 'priority' => sfLogger::INFO)));
      }
      
      // Start miltipage session.
      $manager->startSession();

      // If request is not 
      if (! $request->hasParameter($options['handler']['page_name_param'])) {
        //walk_route
        //die('ad');
      }       

      // Logging about page handler.
      if (sfConfig::get('sf_logging_enabled')) {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array('Create page handler', 'priority' => sfLogger::INFO)));
      }
      
      // Initiate page handler.
      $handler = new yaMultipageHandler($manager, $options['handler']);
      $page = $handler->getCurrentPage($options['handler']['page_name_param']);

      // Check page object of instance of yaMultipageBase class.
      if (! ($page instanceof yaMultipageBase)) {
        throw new yaMultipageUknownPageException(sprintf("Requested page is not instance of '%s' class.", 'yaMultipageBase'));
      }

      // Check callable secure method reference.
      $secureMethod = new sfCallable(array($page, 'isSecure'));
      if (! is_callable($secureMethod->getCallable())) {
        throw new yaMultipageUknownPageException(sprintf("Secure method cannot be call in the class '%s'", get_class($page)));
      }

      // insert/remove auth page for secure pages
      if (! $this->getUser()->isAuthenticated() && $page->isSecure())
      {
        $page = $handler->insertAuthPage($page, yaMultipageHandler::BEFORE);
        return $this->redirect($page->getUrl());
      }
    }
    // Catch exceptions of yaMultipageSessionException class.
    catch (yaMultipageSessionException $exception)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        $exception->getMessage(), 'priority' => sfLogger::WARNING)));

      return $this->redirect($actionOptions['start_route']);
    }
    // Catch exceptions of yaMultipageUknownPageException class.
    catch (yaMultipageUknownPageException $exception)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        $exception->getMessage(), 'priority' => sfLogger::WARNING)));

      return $this->forward404($exception->getMessage());
    }
    // Catch exceptions of yaMultipageWrongOrderException class.
    catch (yaMultipageWrongOrderException $exception)
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        $exception->getMessage(), 'priority' => sfLogger::WARNING)));

      return $this->redirect($actionOptions['start_route']);
    }

    if (sfConfig::get('sf_logging_enabled')) {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
        sprintf('Request "fetch" method for page "%s" (class "%s")', $page->getName(), get_class($page)), 'priority' => sfLogger::INFO)));
    }

    if (! method_exists($page, yaMultipageBase::FETCH_METHOD_NAME)) {
      throw new yaMultipageLoadPageException(sprintf('Method "%s" of page "%s" cannot be call!', yaMultipageBase::FETCH_METHOD_NAME, $page->getName()));
    }


    // fetch page
    try
    {
      // Define fetch result.
      $pageFetchResult = call_user_func(array($page, yaMultipageBase::FETCH_METHOD_NAME), $request);
    }
    // Catch exceptions of yaMultipageLoadPageException class.
    catch (yaMultipageLoadPageException $exception)
    {
      if (sfConfig::get('sf_logging_enabled')) {
        $this->dispatcher->notify(new sfEvent($this, 'application.log', array(
          sprintf('Load page error: %s', $exception->getMessage()), 'priority' => sfLogger::WARNING)));
      }

      $redirectUrl = $page->getUrl();
      $this->getUser()->setFlash('error', $exception->getMessage());

      if ($exception instanceof yaMultipagePrevPageException && ! $page->isFirst())
      {
        $redirectUrl = $page->getPrevious()->getUrl();
      }
      elseif ($exception instanceof yaMultipageNextPageException && ! $page->isLast())
      {
        $redirectUrl = $page->getNext()->getUrl();
      }

      return $this->redirect($redirectUrl);
    }

    // Fix current parameters of the route.
    $arCurrentParameters = $this->getRoute()->getParameters();
    unset($arCurrentParameters[$manager->getOption('session_name')]);
    if (! empty($options['handler']['page_name_param'])) {
      unset($arCurrentParameters[$options['handler']['page_name_param']]);
    }

    // Switch result.
    switch($pageFetchResult)
    {
      // 
      case yaMultipageBase::PAGE_NEXT_AND_SAVE:
        $page->save();

        if ($page->getNext()) {
          return $this->redirect($page->getNext()->getUrl($arCurrentParameters));
        }
      break;

      case yaMultipageBase::PAGE_NEXT:
        if ($page->getNext())
        {
          return $this->redirect($page->getNext()->getUrl($arCurrentParameters));
        }
      break;

      case yaMultipageBase::PAGE_PREVIOUS:
        if ($page->getPrevious()) {
          return $this->redirect($page->getPrevious()->getUrl($arCurrentParameters));
        }
      break;
    }

    // Set page variable for standart symfony page.
    $this->page = $page;

    // Clone variables of the processed page to standart page.
    $this->getVarHolder()->add($page->getVarHolder()->getAll());

    // Set current template name.
    $this->setTemplate($page->getTemplateName());

    return sfView::SUCCESS;
  }
}