<?php
/**
 */
class apiFormatter
{
  private static $format = '';

  /**
   */
  public static function setFormat(sfEvent $event)
  {
    self::$format = $event['format'];
  }

  /**
   */
  public static function setContent(sfEvent $event, $content)
  {
    $response = $event->getSubject();

    if ('xml' == self::$format)
    {
      $response->setContentType('application/xhtml+xml');
      header('Content-type: application/xhtml+xml');     
    }

    return $content;
  }
}