<?php
/**
 * Контроллер управления файлами.
 */
class BasejDoctrineFileActions extends sfActions
{
  /**
   * Скачивание файла.
   *
   * @param sfWebRequest $request
   */
  public function executeDownload(sfWebRequest $request)
  {
    //try {
      // Определение номера требуемого конкурса.
      if (null == $request->getParameter('id', null))
      {
        throw new sfException($this->getContext()->getI18N()->__('Файл для скачивания не указан!', null, 'contest'));
      }
      /*
    // Выборка списка последних постов блога.
    $file = Doctrine::getTable('jFile')->createQuery('f')
              ->innerJoin('bp.Blog as b')
              ->innerJoin('bp.User as u')
              ->addWhere('bp.is_active = ?', true)
              ->addWhere('b.is_active = ?', true)
              ->orderBy('bp.created_at');

return sfView::HEADERS_ONLY;
edit update due to extra comments.

Since you are trying to download a pdf, you're approach the problem incorrectly. Do not use sendContent(). See below (this is a snippet from a production site I've written and has proven to work across all major browsers):

$file = '/path/to/file.pdf';
$this->getResponse()->clearHttpHeaders();
$this->getResponse()->setStatusCode(200);
$this->getResponse()->setContentType('application/pdf');
$this->getResponse()->setHttpHeader('Pragma', 'public'); //optional cache header
$this->getResponse()->setHttpHeader('Expires', 0); //optional cache header
$this->getResponse()->setHttpHeader('Content-Disposition', "attachment; filename=myfile.pdf");
$this->getResponse()->setHttpHeader('Content-Transfer-Encoding', 'binary');
$this->getResponse()->setHttpHeader('Content-Length', filesize($file));

return $this->renderText(file_get_contents($file));
*/
    return sfView::SUCCESS;
  }
}
