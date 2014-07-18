<?php
/**
 * Controls the generation and parsing of URLs.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.routing
 * @author      chuga
 * @version     SVN: $Id$
 */
class klPatternRouting extends sfPatternRouting 
{
  /**
   * {@inheritDoc}
   */
  public function parse($url) 
  {
    if (isset($this->options['trimming']) && $this->options['trimming'])
    {
      $this->dispatcher->notify(new sfEvent($this, 'application.log', array(sprintf('Trimming url %s to %s', $url, rtrim($url, '/')))));
      $url = rtrim($url, '/'); # trim trailing slashes before actual routing
    }
   
    return parent::parse($url);
  }  
}