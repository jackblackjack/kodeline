<?php
abstract class BaseklCmsUpdateActions extends yaBaseActions
{
  /**
   * Default action.
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    klUpdateToolkit::createDigest();
  }
}
