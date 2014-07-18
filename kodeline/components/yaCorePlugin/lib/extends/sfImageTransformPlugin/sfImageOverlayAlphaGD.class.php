<?php
/**
 *
 * sfImageOverlaysGD class.
 *
 * Overlays GD image with trasparent on top of another GD image with transparent.
 * Overlays an image at a set point on the image.
 *
 * @package sfImageTransform
 * @subpackage transforms
 * @author Chugarev Alexey <chugarev@gmail.com>
 * @version SVN: $Id$
 */
class sfImageOverlayAlphaGD extends sfImageOverlayGD
{ 
  /**
   * Apply the transform to the sfImage object.
   *
   * @param integer
   * @return sfImage
   */
  protected function transform(sfImage $image)
  {
    // compute the named coordinates
    $this->computeCoordinates($image);

    $resource = $image->getAdapter()->getHolder();
    //imagealphablending($resource, false);

    // create true color canvas image:
    $canvas_w = $image->getWidth();
    $canvas_h = $image->getHeight();

    $canvas_img = $image->getAdapter()->getTransparentImage($canvas_w, $canvas_h);
    imagealphablending($canvas_img, true);
    imagecopy($canvas_img, $resource, 0, 0, 0, 0, $canvas_w, $canvas_h);

    // Check we have a valid image resource
    if (false === $this->overlay->getAdapter()->getHolder())
    {
      throw new sfImageTransformException(sprintf('Cannot perform transform: %s',get_class($this)));
    }

    // create true color overlay image:
    $overlay_w   = $this->overlay->getWidth();
    $overlay_h   = $this->overlay->getHeight();
    $overlay_img = $this->overlay->getAdapter()->getHolder();
    
    // copy and merge the overlay image and the canvas image:
    imagecopy($canvas_img, $overlay_img, $this->left, $this->top, 0, 0, $overlay_w, $overlay_h);
    imagesavealpha($canvas_img, true);

    // tidy up
    imagedestroy($resource);

    $image->getAdapter()->setHolder($canvas_img);

    return $image;
  }
}
