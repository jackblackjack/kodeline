<?php
/**
 * Контроллер работы с категориями элементов.
 * 
 * @package     frontend
 * @subpackage  frontendFShopFilter
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseFrontendFShopFilterActions extends yaBaseActions
{
  /**
   * Class name for the object
   * 
   * @var string
   */
  protected $objectClassName = 'FxShopFilter';

  /**
   * Class name of form for create new object
   * 
   * @var string
   */
  protected $formClassNew = 'FxShopFilterNewNodeForm';

  /**
   * Class name of form for edit object
   * 
   * @var string
   */
  protected $formClassEdit = 'FxShopFilterEditNodeForm';

  /**
   * Выборка по фильтру.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeFetch(sfWebRequest $request)
  {
    try {
      // Check id of the edited object.
      if (null == ($this->filter_id = $request->getParameter('filter_id', null))) {
        throw new sfException($this->getContext()->getI18N()->__('ID фильтра не был указан!', null, 'flexible-shop'));
      }

      // Fetch filter data with rules.
      $this->filter = Doctrine::getTable($this->objectClassName)->createQuery('fxf')
                        ->innerJoin('fxf.Rules as rules')
                        ->innerJoin('rules.Component as rc')
                        ->innerJoin('rules.Parameter as rcp WITH rcp.component_id = rc.id')
                        ->where('fxf.id = ?', $this->filter_id)
                        ->fetchOne();
      
      // Throw exception if filter has not found.
      if (! $this->filter)
      {
        throw new sfException(sprintf($this->getContext()
          ->getI18N()->__('Фильтр с ID #%d не найден!', null, 'flexible-shop'), $this->filter_id));
      }

      // Load filter query builder helper by context.
      $this->getContext()->getConfiguration()->loadHelpers(array('FilterBuilderQuery'));

      // Process query for fetch.
      $fetchQuery = Doctrine_Query::create();
      foreach($this->filter['Rules'] as $rule)
      {
        $fetchQuery = FilterBuilderQueryHelper::buildWhereQuery($fetchQuery, $rule['Parameter'], $rule, $rule['Parameter']['Component']);
      }

      $this->results = $fetchQuery->execute();
    }
    // Catch exceptions.
    catch(Exception $exception)
    {
      die($exception->getMessage());
      $this->getUser()->setFlash('error', $exception->getMessage());

      return sfView::ERROR;
    }

    return sfView::SUCCESS;
  }
}