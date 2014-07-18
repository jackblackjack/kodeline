<?php
/**
 * Helper for create urls.
 *
 * @package     yaCorePlugin
 * @subpackage  lib
 * @category    helper
 * @author      chugarev
 * @version     $Id$
 */

/**
 * @ignore
 */
function url_for2_escaped($routeName, sfOutputEscaperArrayDecorator $decorator, $absolute = false)
{
  $params = array();
  foreach($decorator as $key => $value) $params[$key] = $value;

  $params = array_merge(array('sf_route' => $routeName), is_object($params) ? array('sf_subject' => $params) : $params);

  return url_for1($params, $absolute);
}