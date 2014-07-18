<?php
/**
 * Javascript helper.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.helper
 * @author      pinhead
 * @version     SVN: $Id: yaJavascriptHelper.class.php 2756 2010-12-15 22:50:26Z pinhead $
 */
class yaJavascriptHelper
{
  /**
   * Indicates that helper has inline capture in progress.
   * @var bool
   */
  protected $inlineCaptureInProgress = false;

  /**
   * Current inline capture position.
   * @var string
   */
  protected $inlineCapturePosition = null;

  protected $inlineCaptureLocation = null;

  /**
   * Begins capturing inline javascript code.
   * If $content is set and is not null then it will be used as captured content
   * and capturing will end. Content will be added to response.
   * @param string $position Position inside HTML document where content will be rendered
   * @param string $content  Javascript code including <script></script> tags
   * @throws sfConfigurationException
   * @throws sfCacheException
   */
  public function beginInlineJavascript($location = yaWebResponse::LOCATION_BODY, $position = yaWebResponse::LAST)
  {
    $response = sfContext::getInstance()->getResponse();
    if (! $response instanceof yaWebResponse)
    {
      throw new sfConfigurationException('Response object must be an instance of yaWebResponse.');
    }

    if ($this->inlineCaptureInProgress)
    {
      throw new sfCacheException('An inline javascript capture is already started.');
    }

    $this->inlineCaptureInProgress = true;
    $this->inlineCaptureLocation = $location;
    $this->inlineCapturePosition = $position;

    ob_start();
    ob_implicit_flush(0);
  }

  /**
   * Finishes inline javascript capture and adds captured content to response.
   * @throws sfCacheException
   * @throws sfConfigurationException
   */
  function endInlineJavascript()
  {
    if (! $this->inlineCaptureInProgress)
    {
      throw new sfCacheException('Inline javascript capture is not started.');
    }

    if (! $this->inlineCaptureLocation)
    {
      throw new sfCacheException('Inline javascript capture location is not set.');
    }

    $response = sfContext::getInstance()->getResponse();
    if (! $response instanceof yaWebResponse)
    {
      throw new sfConfigurationException('Response object must be an instance of yaWebResponse.');
    }

    $content = ob_get_clean();
    $response->addInlineScript($content, $this->inlineCaptureLocation, $this->inlineCapturePosition);

    $this->inlineCaptureInProgress = false;
    $this->inlineCaptureLocation = null;
    $this->inlineCapturePosition = null;
  }
}