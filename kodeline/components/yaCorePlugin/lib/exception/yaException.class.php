<?php

class yaException extends sfException
{

  public function __construct($message = 'yaException')
  {
    return parent::__construct(strip_tags($message));
  }

  /**
   * Builds an exception
   *
   * @param mixed $something
   *
   * @return yaException  An yaException instance that wraps the given something
   */
  public static function build($something)
  {
    if ($something instanceof Exception)
    {
      $exception = new yaException(sprintf('Wrapped %s: %s', get_class($something), $something->getMessage()));
      $exception->setWrappedException($something);
    }
    elseif (is_array($something))
    {
      $exception = new yaException(self::formatArrayAsHtml($something));
    }
    else
    {
      $exception = new yaException($something);
    }

    return $exception;
  }

}