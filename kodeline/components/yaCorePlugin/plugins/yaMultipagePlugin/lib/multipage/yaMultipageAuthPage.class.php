<?php

/**
 * yaMultipageAuthPage class.
 *
 * @package     yaMultipagePlugin
 * @subpackage  multipage
 * @author      pinhead
 * @version     SVN: $Id: yaMultipageAuthPage.class.php 1646 2010-06-18 16:09:07Z pinhead $
 */
class yaMultipageAuthPage extends yaMultipageBase
{
  protected $defaultName = 'auth';

  /**
   * Constructor.
   *
   * @param array $parameters
   *
   * @see yaMultiPage::__construct()
   */
  public function __construct($parameters = array())
  {
    $defaultParameters = array(
      'name'  => $this->defaultName,
      'form'  => sfConfig::get('app_sf_guard_plugin_signin_form', 'sfGuardFormSignin'),
      'title' => 'Login',
      'brief' => 'Login'
    );

    parent::__construct($parameters + $defaultParameters);
  }

  /**
   * getTemplateName()
   *
   * @return string
   */
  public function getTemplateName()
  {
    return $this->defaultName;
  }

  /**
   * fetch()
   *
   */
  public function fetch()
  {
    $user = sfContext::getInstance()->getUser();

    if ($user->isAuthenticated()) {
      $this->handler->removeAuthPage();
      throw new yaMultipageNextPageException();
    }
  }

  /**
   * save()
   *
   */
  public function save()
  {
    $user = sfContext::getInstance()->getUser();

    if (! $user->isAuthenticated())
    {
      $values   = $this->getForm()->getValues();
      $remember = isset($values['remember']) ? $values['remember'] : false;

      $user->signin($values['user'], $remember);
    }

    $this->handler->removeAuthPage();
  }
}