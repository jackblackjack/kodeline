<?php
/**
 * jFileAttachableRouting class.
 *
 * @package     
 * @subpackage  lib
 * @category    routing
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class jFileAttachableRouting
{
  /**
   * Listens to the routing.load_configuration event.
   * @param sfEvent An sfEvent instance
   */
  public static function addRoutes(sfEvent $event)
  {
    $route = $event->getSubject();

    $route->prependRoute('j_file_attach_post', 
      new sfRoute('/upload/by/post/', 
                    array('module' => 'jFileAttachable', 'action' => 'byPost'),
                    array('sf_format' => 'html', 'sf_method' => array('get', 'post'))));

    $route->prependRoute('j_file_attach_post_ajax', 
      new sfRoute('/upload/by/form/:form/:field/', 
                    array('module' => 'jFileAttachable', 'action' => 'byForm'),
                    array('sf_format' => '(?:json)', 'sf_method' => '(?:post)', 'field' => '\w+')));
  }
}
