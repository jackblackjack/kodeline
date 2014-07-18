<?php

/**
 * Extends symfony sfWebDebug.
 *
 * @package    yaCorePlugin
 * @subpackage lib.debug
 * @author     pinhead
 * @version    SVN: $Id: yaWebDebug.class.php 2391 2010-10-10 00:03:49Z pinhead $
 */
class yaWebDebug extends sfWebDebug
{

  /**
   * Injects the web debug toolbar into a given HTML string.
   *
   * @param string  $content The HTML content
   *
   * @return string The content with the web debug toolbar injected
   */
  public function injectToolbar($content)
  {
    // we don't want to show web debug panel on non-html response
    if (! strpos($content, '</html>'))
    {
      return $content;
    }

    return parent::injectToolbar($content);
  }

}