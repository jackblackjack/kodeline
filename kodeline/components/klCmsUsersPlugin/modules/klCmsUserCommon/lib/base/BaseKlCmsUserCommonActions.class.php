<?php
abstract class BaseKlCmsUserCommonActions extends yaBaseActions
{
  /**
   * Action for login user.
   * @param sfWebRequest $request sfWebRequest instance
   */
  public function executeLogin($request)
  {
    // Fetch current user.
    $user = $this->getUser();

    try {
      $class = sfConfig::get('app_klCmsUsersPlugin_login_form', 'klUserFormLogin');
      $this->form = new $class();
    }
    catch(sfException $exception) {
      $this->getUser()->setFlash('error', $exception->getMessage());
    }

    if ($request->isMethod(sfRequest::POST))
    {
      // Determination of the ability to call method getName.
      $method = new sfCallable(array($this->form, "getName"));
      if (is_callable($method->getCallable()))
      {
        $this->form->bind($request->getParameter($this->form->getName()));
      }
      else {
        $this->form->bind($request->getParameter('signin'));  
      }

      // Form validate.
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        // Sign in user.
        $this->getUser()->signin($values['user'], (array_key_exists('remember', $values) ? $values['remember'] : false));

        // Redirect to referer or success_signin_url from app.yml or homepage.
        $redirectUrl = '@homepage';
        if (null === sfConfig::get('app_klCmsUsersPlugin_success_signin_url', null))
        {
          $sReferer = $user->getReferer($request->getReferer());
          $sServerName = $request->getHost();

          if (preg_match("~(ht|f)tp(s?)://$sServerName(/|$|\s)~i", $sReferer))
          {
            $redirectUrl = $sReferer;
          }
        }
        else {
          return $this->redirect(sfConfig::get('app_klCmsUsersPlugin_success_signin_url'));  
        }

        return $this->redirect($redirectUrl);
      }
    }

    if ($request->isXmlHttpRequest())
    {
      $this->getResponse()->setHeaderOnly(true);
      $this->getResponse()->setStatusCode(401);
      return sfView::NONE;
    }

    // if we have been forwarded, then the referer is the current URL
    // if not, this is the referer of the current request
    $user->setReferer($this->getContext()->getActionStack()->getSize() > 1 ? $request->getUri() : $request->getReferer());

    $module = sfConfig::get('sf_login_module');
    if ($this->getModuleName() != $module)
    {
      return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
    }

    return sfView::SUCCESS;
  }

  public function executeLogout($request)
  {
    $this->getUser()->signOut();

    $signoutUrl = sfConfig::get('app_klCmsUsersPlugin_success_signout_url', $request->getReferer());

    $this->redirect('' != $signoutUrl ? $signoutUrl : '@homepage');
  }

  public function executeSecure($request)
  {
    $this->getResponse()->setStatusCode(403);
  }

  /*
  public function executePassword($request)
  {
    throw new sfException('This method is not yet implemented.');
  }
  */
}
