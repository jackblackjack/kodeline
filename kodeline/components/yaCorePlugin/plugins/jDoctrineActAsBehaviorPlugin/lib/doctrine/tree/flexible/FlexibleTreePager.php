<?php
/**
 * Пагинатор для работой с FlexibleTree деревьями.
 *
 * @package    jDoctrineActAsBehaviorPlugin
 * @subpackage pager
 * @author     chugarev@gmail.com
 * @version    $Id$
 */
class FlexibleTreePager extends yaDoctrinePager
{
  protected
    $resultsCache;

  /**
   * Constructor.
   *
   * @param string  $class      The model class
   * @param integer $maxPerPage Number of records to display per page
   */
  public function __construct($class, $maxPerPage = 10)
  {
    $this->setClass($class);
    $this->setMaxPerPage($maxPerPage);
    $this->parameterHolder = new sfParameterHolder();
  }

  /**
   * @see sfPager
   */
  public function setMaxPerPage($maxPerPage)
  {
    parent::setMaxPerPage($maxPerPage);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function setQuery($query)
  {
    parent::setQuery($query);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function setPage($page)
  {
    parent::setPage($page);

    return $this;
  }

  /**
   * @see sfPager
   */
  public function init()
  {
    parent::init();

    return $this;
  }

  /**
   * Get all the results for the pager instance
   *
   * @param mixed $hydrationMode A hydration mode identifier
   *
   * @return Doctrine_Collection|array
   */
  public function getResults($hydrationMode = null)
  {
    return $this->getQuery()->execute(array(), Doctrine::HYDRATE_RECORD);
  }

  /**
   * Get all the results for the pager instance
   *
   * @param mixed $hydrationMode A hydration mode identifier
   *
   * @return Doctrine_Collection|array
   */
  public function getResultsWithoutCache($hydrationMode = null)
  {
    return parent::getResults($hydrationMode)->getData();
  }

  public function serialize()
  {
    $vars = get_object_vars($this);
    unset($vars['query'], $vars['resultsCache']);
    return serialize($vars);
  }

  public function getCountQuery()
  {
    $selectQuery = $this->getQuery();

    if (count($selectQuery->getDqlPart('where')))
    {
      $query = clone $selectQuery;
      return $query->offset(0)->limit(0);
    }
    else
    {
      return yaDb::table($this->getClass())->createQuery();
    }
  }
}