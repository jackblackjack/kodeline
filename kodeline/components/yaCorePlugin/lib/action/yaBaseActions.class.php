<?php

/**
 * Extends symfony sfActions.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.action
 * @author      pinhead
 * @version     SVN: $Id: yaBaseActions.class.php 2404 2010-10-11 21:09:55Z pinhead $
 */
abstract class yaBaseActions extends sfActions
{
/*  
        if ($this->getContext()->getRequest()->isXmlHttpRequest())
        {
          $this->setLayout(false);
          sfConfig::set('sf_web_debug', false);

          $this->getRequest()->setParameter('sf_format','json');
          $this->getResponse()->setContentType('application/json; charset=utf-8');

          // Render text for ajax request.
          $this->renderText(json_encode(array('id' => $this->object['id'], )));
*/

/*
before action!:
        // if we have been forwarded, then the referer is the current URL
        // if not, this is the referer of the current request
        $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());
*/

    protected function setTitle($string)
    {
        $this->getResponse()->setTitle($string . sfConfig::get('app_title_separator') . sfConfig::get('app_title_default'));
    }
    
  /**
   * Conditionaly forwards request to security module action
   *
   * @param boolean $condition
   */
  protected function forwardSecureUnless($condition)
  {
    if (!$condition)
    {
      return $this->forwardSecure();
    }
  }

  /**
   * Forwards request to security module action
   */
  protected function forwardSecure()
  {
    return $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
  }

  /**
   * Appends the given json to the response content and bypasses the built-in view system.
   *
   * This method must be called as with a return:
   *
   * <code>return $this->renderJson(array('key'=>'value'))</code>
   *
   * Important : due to a limitation of the jquery form plugin (http://jquery.malsup.com/form/#file-upload)
   * when a file have been uploaded, the contentType is set to text/html
   * and the json response is wrapped into a textarea
   *
   * @param string $json Json to append to the response
   *
   * @return sfView::NONE
   */
  public function renderJson($json)
  {
    $this->setLayout(false);
    sfConfig::set('sf_web_debug', false);

    $encodedJson = json_encode($json);

    if ($this->request->isMethod(sfRequest::POST) && $this->request->isXmlHttpRequest() && !in_array('application/json', $this->request->getAcceptableContentTypes()))
    {
      $this->response->setContentType('text/html');
      $this->response->setContent('<textarea>'.$encodedJson.'</textarea>');
    }
    else
    {
      $this->response->setContentType('application/json; charset=utf-8');
      $this->response->setContent($encodedJson);
    }

    return sfView::NONE;
  }


  /**
   * Renders JSON result.
   * 
   * @param array $result
   * @param int   $code
   *
   * @return sfView::NONE
   */
  protected function renderJsonResult($result)
  {
    return $this->renderJson(array('result' => $result));
  }

  /**
   * Renders JSON error.
   * 
   * @param string  $message
   * @param int     $errorCode
   *
   * @return sfView::NONE
   */
  protected function renderJsonError($message, $code = 0)
  {
    return $this->renderJson(array('error' => array('code' => $code, 'message' => $message)));
  }

  /**
   *
   *
   * @param array   $parts
   * @param boolean $encodeAssets
   */
  protected function renderAsync(array $parts, $encodeAssets = false)
  {
    $parts = array_merge(array('html' => '', 'css' => array(), 'js' => array()), $parts);

    // translate asset aliases to web paths
    foreach($parts['css'] as $index => $asset)
    {
      $parts['css'][$index] = $this->getHelper()->getStylesheetWebPath($asset);
    }
    foreach($parts['js'] as $index => $asset)
    {
      $parts['js'][$index] = $this->getHelper()->getJavascriptWebPath($asset);
    }

    if(!empty($parts['css']) || !empty($parts['js']))
    {
      if ($encodeAssets)
      {
        $parts['html'] .= $this->getHelper()->tag('div.ya_encoded_assets.none', json_encode(array(
          'css' => $parts['css'],
          'js'  => $parts['js']
        )));
      }
      else
      {
        foreach($parts['css'] as $css)
        {
          $parts['html'] .= '<link rel="stylesheet" type="text/css" href="'.$css.'"/>';
        }

        foreach($parts['js'] as $js)
        {
          $parts['html'] .= '<script type="text/javascript" src="'.$js.'"></script>';
        }
      }
    }

    $this->response->setContentType('text/html');
    $this->response->setContent($parts['html']);

    return sfView::NONE;
  }

  protected function redirectBack()
  {
    return $this->redirect($this->getBackUrl());
  }

  protected function getBackUrl()
  {
    $backUrl = $this->request->getReferer();

    if (!$backUrl || ($backUrl == $this->request->getUri() && $this->request->isMethod('get')))
    {
      $backUrl = $this->getController()->genUrl('@homepage');
    }

    return $backUrl;
  }

  /**
   * @return sfEventDispatcher
   */
  public function getDispatcher()
  {
    return $this->context->getEventDispatcher();
  }

  /**
   * @return sfRouting
   */
  public function getRouting()
  {
    return $this->context->getRouting();
  }

  /**
   * @return sfI18N
   */
  public function getI18n()
  {
    return $this->context->getI18n();
  }

  /**
   * Calls methods defined via sfEventDispatcher.
   *
   * @param string $method The method name
   * @param array  $arguments The method arguments
   *
   * @return mixed The returned value of the called method
   *
   * @throws sfException If called method is undefined
   */
  public function __call($method, $arguments)
  {
    $event = $this->dispatcher->notifyUntil(new sfEvent($this, 'action.method_not_found', array('method' => $method, 'arguments' => $arguments)));

    if (! $event->isProcessed())
    {
      throw new sfException(sprintf('Call to undefined method %s::%s.', get_class($this), $method));
    }

    return $event->getReturnValue();
  }
}