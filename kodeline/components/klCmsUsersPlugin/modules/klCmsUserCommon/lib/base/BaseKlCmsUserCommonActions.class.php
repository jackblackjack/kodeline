<?php
/**
 * Abstract class for common actions for user.
 * 
 * @version $version$
 */
abstract class BaseKlCmsUserCommonActions extends yaBaseActions
{
  /**
   * Log in action for users.
   * 
   * @param sfWebRequest $request sfWebRequest instance
   */
  public function executeLogin($request)
  {
    // Fetch current user.
    $user = $this->getUser();

    try {
      // Try to create login form class instance.
      $formClassName = sfConfig::get('app_klCmsUsersPlugin_login_form', 'klUserFormLogin');

      if (! class_exists($formClassName)) {
        throw new Exception(sprintf("Class %s is not exists!", $formClassName), 1);
      }

      // Create class instance.
      $this->form = new $formClassName();
    }
    // Catch exceptions.
    catch(sfException $exception) {
      $this->getUser()->setFlash('error', $exception->getMessage());
    }

    // If has been method POST.
    if ($request->isMethod(sfRequest::POST))
    {
      // Determination of the ability to call method getName.
      $method = new sfCallable(array($this->form, "getName"));

      if (is_callable($method->getCallable())) {
        $this->form->bind($request->getParameter($this->form->getName()));
      }

      else if ($request->hasParameter('signin')) {
        $this->form->bind($request->getParameter('signin'));  
      }

      // Validate form.
      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        // Do sign in current user.
        $this->getUser()->signin($values['user'], (array_key_exists('remember', $values) ? $values['remember'] : false));

        // Redirect to referer or app_klCmsUsersPlugin_redirect_url from app.yml or homepage.
        return $this->redirect($this->getUser()->getRedirectUrl());
      }
    }

    /*
    $module = sfConfig::get('sf_login_module');
    if ($this->getModuleName() != $module) {
      return $this->redirect($module.'/'.sfConfig::get('sf_login_action'));
    }
    */

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
