<?php
abstract class BaseKlCmsLayoutManagerActions extends yaBaseActions
{
  /**
   * Index action.
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    // Fetch objects list.
    $this->layouts = Doctrine::getTable('klLayout')->createQuery('kll')->execute(array(), Doctrine_Core::HYDRATE_RECORD);

    return sfView::SUCCESS;
  }
}
