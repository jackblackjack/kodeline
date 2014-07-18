<?php
/**
 * Котроллер расширения комментирования объектов.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  controller
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BaseBehaviorCommentableActions extends yaBaseActions
{
  /**
   * {@inheritDoc}
   */
  public function preExecute()
  {
    if (sfContext::getInstance()->getRequest()->isXmlHttpRequest())
    {
      $this->setLayout(false);
      sfConfig::set('sf_web_debug', false);

      $this->getResponse()->setContentType('application/json; charset=utf-8');
    }
  }

  /**
   * Выводит список комментариев в виде JSON-списка.
   * @param sfWebRequest $request
   */
  public function executeXhrAdd(sfWebRequest $request)
  {
    if ($request->isMethod(sfRequest::POST))
    {
      //if(stripos($_SERVER["CONTENT_TYPE"], "application/json") === 0) {
  //$_POST = json_decode(file_get_contents("php://input"), true);
    //}
      $inVars = @json_decode(file_get_contents("php://input"), true);

      // Создание нового комментария.
      $comment = new jCommentable();
      $comment['user_id'] = $this->getUser()->getProfile()->getId();
      $comment['author_name'] = $this->getUser()->getProfile()->getFullName();
      $comment['body'] = $inVars['text'];

      // Выборка записи с указанным id.
      $record = Doctrine_Core::getTable($request->getParameter('model'))->createQuery()->where('id = ?', $request->getParameter('id'))->fetchOne();
      $record->addComment($comment);
      
      ya::loadHelpers(array('Partial'));
      $arCommentHtml = get_partial('behaviorCommentable/comment', array('comment' => $comment));
      return $this->renderJsonResult(array('model' => $request->getParameter('model'), 'resource' => $request->getParameter('id'), 'comment' => $arCommentHtml));
    }

    return sfView::SUCCESS;
  }

  /**
   * Выводит список комментариев в виде JSON-списка.
   * @param sfWebRequest $request
   */
  public function executeAdd(sfWebRequest $request)
  {
    if (sfContext::getInstance()->getRequest()->isXmlHttpRequest())
    {
      $methodName = 'xhrAdd';
      return $this->forward($this->getContext()->getModuleName(), $methodName);
    }

    // Определение формы добавления комментария.
    $this->form = new commentableForm();

    if ($request->isMethod(sfRequest::POST))
    {
      $this->form->bind($request->getParameter($this->form->getName()));
      if ($this->form->isValid())
      {
        $arValues = $this->form->getValues();

        // Создание нового комментария.
        $comment = new jCommentable();

        if (! empty($arValues['author_id']))
        {
          $comment['user_id'] = $arValues['author_id'];

          // Указание данных автора комментария.
          $author = $arValues['author'];
          $comment['author_name'] = $author->getFullName();
          if ($author->hasField('email')) $comment['author_email'] = $author['email'];
          if ($author->hasField('website')) $comment['author_website'] = $author['website'];
        }

        // Указание заголовка комментария, если он указан.
        if (! empty($arValues['title']))
        {
          $comment['title'] = $arValues['title'];
        }

        $comment['body'] = $arValues['body'];

        // @TODO: Добавить иерархию комментариев.

        // Выборка записи с указанным id.
        $record = Doctrine_Core::getTable($arValues['model'])->createQuery()->where('id = ?', $arValues['resource'])->fetchOne();
        $record->addComment($comment);
      }
      else {
        die('no valid!');
      }
    }

    //die(__METHOD__);

    return sfView::SUCCESS;
  }

  /**
   * Выводит список комментариев в виде JSON-списка.
   * @param sfWebRequest $request
   */
  public function executeGet(sfWebRequest $request)
  {
    try
    {
      // Определение имени модели для поиска комментариев.
      if (null == ($sModelName = $request->getParameter('model', null))) {
        throw new sfError404Exception('Model name is not found!');  
      }

      // Определение номера ресурса для комментариев.
      if (null == ($iResourceId = $request->getParameter('id', null))) {
        throw new sfError404Exception('Resource id is not found!');  
      }

      // Выборка данных модели.
      $model = Doctrine::getTable('jCommentModel')->createQuery()->addWhere('model = ?', $sModelName)->fetchOne();
      if (! $model)
      {
        return $this->renderJsonResult(array('model' => $sModelName, 'resource' => $iResourceId, 'comments' => array()));
      }

      // Выборка комментариев по указанному ресурсу.
      $arComments = Doctrine::getTable('jCommentable')->createQuery()
                      ->andWhere('model_id = ?', $model['id'])
                      ->andWhere('record_id = ?', $iResourceId)
                      ->andWhere('is_active = ?', true)
                      ->joinAll()
                      ->execute(array(), Doctrine_Core::HYDRATE_RECORD);

      ya::loadHelpers(array('Partial'));
      $arCommentsHtml = array();
      foreach($arComments as $comment)
      {
        $arCommentsHtml[] = get_partial('behaviorCommentable/comment', array('comment' => $comment));
      }

      return $this->renderJsonResult(array('model' => $sModelName, 'resource' => $iResourceId, 'comments' => $arCommentsHtml));
    }
    // Обработка исключений.
    catch(Exception $exception)
    {
      $this->renderJsonError($exception->getMessage());
    }

    return sfView::NONE;
  }
}