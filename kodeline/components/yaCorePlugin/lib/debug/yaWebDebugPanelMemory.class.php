<?php

/**
 * yaWebDebugPanelMemory extends sfWebDebugPanelMemory and adds extended system usage information.
 *
 * @package    yaCorePlugin
 * @subpackage lib.debug
 * @author     pinhead
 * @version    SVN: $Id: yaWebDebugPanelMemory.class.php 2391 2010-10-10 00:03:49Z pinhead $
 */
class yaWebDebugPanelMemory extends sfWebDebugPanelMemory
{
  public function getTitle()
  {
    return '<img src="'.$this->webDebug->getOption('image_root_path').'/memory.png" alt="Usage" />Usage';
  }

  public function getPanelTitle()
  {
    return 'Timers and Memory usage';
  }

  public function getPanelContent()
  {
    $usage = yaOs::getPerformanceInfo();
    $content = '<ul>';

    foreach ($usage as $key => $value)
    {
      $content .= sprintf('<li><strong>%s:</strong>&nbsp;%s</li>', ucfirst($key), $value);
    }

    $content .= '</ul>';

    return $content;
  }

  public static function listenToLoadDebugWebPanelEvent(sfEvent $event)
  {
    $debug = $event->getSubject();
    $debug->removePanel('memory');
    $debug->setPanel('memory', new self($event->getSubject()));
  }
}
