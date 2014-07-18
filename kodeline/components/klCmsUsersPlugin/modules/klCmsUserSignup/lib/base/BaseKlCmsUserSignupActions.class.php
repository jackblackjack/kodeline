<?php
abstract class BaseKlCmsUserSignupActions extends yaBaseActions
{
  /**
   * Default action.
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    // Define doctrine query.
    $query = Doctrine::getTable('sfGuardUser')->createQuery('sfjpu')->leftJoin('sfjpu.Profile');

    // Define configuration for pager.
    $arPagerConfig = sfConfig::get('app_klCmsUsersPlugin_klCmsUsers_per_page', array('per_page' => 10));

    // Define pager.
    $this->items_pager = new sfDoctrinePager('sfGuardUser', $arPagerConfig['per_page']);
    $this->items_pager->setQuery($query);
    $this->items_pager->setPage($request->getParameter('upage', 1));
    $this->items_pager->init();

    return sfView::SUCCESS;
  }
}
