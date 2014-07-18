<?php
/**
 * sfDoctrineFBAutocompleteJson actions.
 * 
 * @package    sfDoctrineFBAutocompletePlugin
 * @subpackage sfDoctrineFBAutocompleteJson
 * @author     chugarev
 * @version    $Id$
 */
class jDoctrineFBSuggestJsonActions extends BasejDoctrineFBSuggestJsonActions
{
  /**
   * Fetch street by filter.
   * 
   * @param $request sfWebRequest
   */
  public function executeGoods(sfWebRequest $request)
  {
    try {
      if (! strlen($request->getParameter('tag', null)))
      {
        throw new sfException($this->getContext()->getI18N()->__('Tag is not defined', null, 'suggest-local'));
      }

      $this->items = Doctrine_Core::getTable('Goods')->createQuery()
                      ->where("title LIKE ?", '%' . $request->getParameter('tag') . '%')
                      ->fetchArray();

      return sfView::SUCCESS;
    }
    // Catch unknown model.
    catch(Doctrine_Record_UnknownPropertyException $exception)
    {
      return $this->renderJsonError('Unknown model');
    }
    // Catch default exception.
    catch(sfException $exception)
    {
      return $this->renderJsonError($exception->getMessage());
    }
  }
}
