<?php
/**
 * Указание пользователей online.
 *
 * @package     frontend
 * @subpackage  onlineFilter
 * @version     $Id$
 */
class onlineFilter extends jSessionUpdateFilter
{
  /**
   * Name of the cookie.
   */
  const COOKIE_NAME = 'fonline';

  /**
   * @inheritDoc
   */
  public function execute(sfFilterChain $filterChain)
  {
    // Definition flag of the user anonymous.
    $bIsAnonymous = $this->getContext()->getUser()->isAnonymous();

    if ($this->isFirstCall() && ! $bIsAnonymous)
    {  
      // Update user status.
      Doctrine_Query::create()->update('userSessions')
        ->set('user_id', '?', $this->getContext()->getUser()->getId())
        ->where('sess_id = ?', session_id())->execute();
    }

    // Set cookie value.
    // getResponse()->setCookie not working :(
    setcookie(self::COOKIE_NAME, (int) (false == $bIsAnonymous), (time() + 604800), '/', $this->getContext()->getRequest()->getHost(), $this->getContext()->getRequest()->isSecure(), false);

    $filterChain->execute();
  }
}