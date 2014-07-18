<?php
/**
 * sfImageThumbnailAlphaGeneric class
 *
 * generic thumbnail transform with support alpha channel
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Chugarev Alexey <chugarev@gmail.com>
 * @version SVN: $Id$
 */
class sfImageThumbnailAlphaGeneric extends sfImageThumbnailGeneric
{ 
  /**
   * Apply the transformation to the image and returns the image thumbnail
   */
  protected function transform(sfImage $image)
  {
    $resource_w = $image->getWidth();
    $resource_h = $image->getHeight();

    $scale_w    = $this->getWidth()/$resource_w;
    $scale_h    = $this->getHeight()/$resource_h;
    $method = $this->getMethod();
    switch ($method)
    {
      case 'deflate':
      case 'inflate':

        return $image->resize($this->getWidth(), $this->getHeight());

      case 'left':
      case 'right':
      case 'top':
      case 'bottom':
      case 'top-left':
      case 'top-right':
      case 'bottom-left':
      case 'bottom-right':
      case 'center':
        $image->scale(max($scale_w, $scale_h));

        if(false !== strstr($method, 'top'))
        {
          $top = 0;
        }
        else if(false !== strstr($method, 'bottom'))
        {
          $top = $image->getHeight() - $this->getHeight();
        }
        else
        {
          $top = (int)round(($image->getHeight() - $this->getHeight()) / 2);
        }

        if(false !== strstr($method, 'left'))
        {
          $left = 0;
        }
        else if(false !== strstr($method, 'right'))
        {
          $left = $image->getWidth() - $this->getWidth();
        }
        else
        {
          $left = (int)round(($image->getWidth() - $this->getWidth()) / 2);
        }

        return $image->crop($left, $top, $this->getWidth(), $this->getHeight());

      case 'scale':
        return $image->scale(min($scale_w, $scale_h));

      case 'fit':           
      default:
        $img = clone $image;

        $image->getAdapter()->setHolder($image->getAdapter()->getTransparentImage($this->getWidth(), $this->getHeight()));
        $img->scale(min($this->getWidth() / $img->getWidth(), $this->getHeight() / $img->getHeight()));
        $image->overlay($img, 'center');

        return $image;
    }
  }
}
