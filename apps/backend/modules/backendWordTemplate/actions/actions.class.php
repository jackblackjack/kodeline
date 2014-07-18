<?php
/**
 * Контроллер работы с шаблонами слов.
 * 
 * @package     backend
 * @subpackage  backendWordTemplate
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class backendWordTemplateActions extends BaseBackendWordTemplateActions
{
  /**
   * Fetch list of the words.
   * 
   * @param $request sfWebRequest
   */
  public function executeList(sfWebRequest $request)
  {
    // Create query for fetch words.
    $query = Doctrine_Core::getTable($this->objectClassName)->createQuery('w');

    // Fetch goods.
    if (null !== $request->getParameter('good', null)) {
      $query->innerJoin('w.Goods as goods WITH goods.id = ?', $request->getParameter('good'));
    }

    // Fetch words.
    $this->items = $query->fetchArray();

    return sfView::SUCCESS;
  }

  /**
   * Fetch list of the words.
   * 
   * @param $request sfWebRequest
   */
  public function executeNew(sfWebRequest $request)
  {
    // Fetch goods.
    if (null === $request->getParameter('word', null)) {
      die('no word');
    }

    // Create.
    $newWord = new $this->objectClassName();
    $newWord['title'] = $request->getParameter('word');
    $newWord->save();

    // Fetch words.
    $this->items = array(array('title' => $newWord['title'], 'id' => $newWord->getId()));

    return sfView::SUCCESS;
  }

  /**
   * Fetch list of the words.
   * 
   * @param $request sfWebRequest
   */
  public function executeLink(sfWebRequest $request)
  {
    // Fetch goods.
    if (null === $request->getParameter('good', null)) {
      die('no good');
    }

    if (null === $request->getParameter('words', null)) {
      die('no words');
    }

    foreach ($request->getParameter('words') as $word_id)
    {
      $newWord = new GoodsWordsRef();
      $newWord['good_id'] = $request->getParameter('good');
      $newWord['word_id'] = $word_id;
      $newWord->save();
    }

    return sfView::SUCCESS;
  }
}