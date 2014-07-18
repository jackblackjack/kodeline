<?php
/**
 * Base controller for behavior attachment files actions.
 *
 * @package     jDoctrineFilesPlugin
 * @subpackage  jDoctrineFile
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BasejFileAttachableActions extends yaBaseActions
{
  /**
   * Action for prepare new upload file.
   * 
   * @param sfWebRequest $request Web request.
   */
  public function executeByPost(sfWebRequest $request)
  {
    try {
      // Define uploaded files.
      $arContainer = $request->getFiles();
      $arFile =& $arContainer['file'];

      // Check uploaded files list.
      if (empty($arContainer))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Загружаемый файл не найден!', null, 'attachable'));
      }

      // Checks saved temporary name.
      if (! file_exists($arFile['tmp_name']))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Возникла ошибка при сохранении временного файла.', null, 'attachable'));
      }

      // Load helper for read disk store configurations.
      $this->getContext()->getConfiguration()->loadHelpers(array('yaContentStorage', 'jFileMime'));

      // Convert uploaded file to sfValidatedFile instance.
      $file = new sfValidatedFile($arFile['name'], $arFile['type'], $arFile['tmp_name'], $arFile['size'], sfConfig::get('sf_upload_dir'));
      if (! $file->isSaved()) { $file->save(); }

      // Определение имени сохраняемого файла.
      $sFileName = basename($file->getSavedName());

      // Checks saved name.
      if (! file_exists($file->getSavedName()))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Возникла ошибка при сохранении файла.', null, 'attachable'));
      }

      // Define mimetype.
      $sMimeType = jFileMime::getMimeType($arFile['tmp_name']);

      // Checks mimetype in the content table.
      $iFileTypeId = Doctrine::getTable('jFileMimeType')->createQuery('ct')
                      ->select('ct.id')
                      ->where('ct.name = ?', $sMimeType)
                      ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      if (! $iFileTypeId)
      {
        // Create jFileMimeType record.
        $fileType = new jFileMimeType();
        $fileType->name = $sMimeType;
        $fileType->save();
        $iFileTypeId = $fileType->id;
        unset($fileType);
      }

      // Create attachment record.
      $attachment = new jFile();
      if ($request->hasParameter('label')) $attachment['flabel'] = $request->getParameter('label');
      if ($request->hasParameter('key')) $attachment['fkey'] = $request->getParameter('key');
      $attachment['mime_type_id']     = $iFileTypeId;
      $attachment['fname']            = $sFileName;
      $attachment['original_name']    = $file->getOriginalName();
      $attachment['size']             = $file->getSize();
      $attachment['extension']        = $file->getOriginalExtension();
      $attachment['is_active']        = false;

      if (! $this->getUser()->isGuest())
      {
        $attachment['creator_id']  = $this->getUser()->getProfile()->getId();
      }

      $attachment->save();

      // Подготовка ответа по запросу.
      $arResult['path']     = rtrim(substr(sfConfig::get('sf_upload_dir'), strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
      $arResult['oname']     = $file->getOriginalName();
      $arResult['name']     = $sFileName;
      $arResult['msg']      = $this->getContext()->getI18N()->__('Файл загружен!', null, 'contest');

      return $this->renderJsonResult($arResult);
    }
    // Catch exceptions sfDoctrineException
    catch(sfDoctrineException $exception)
    {
      // Write error to log.
      $this->logMessage($exception->getMessage(), 'err');

      // Define error message txt.
      $errMessage = $this->getContext()->getI18N()->__('Ошибка при сохранении изображения', null, 'attachable');

      // Set error message for user.
      $this->getUser()->setFlash('error', $errMessage);

      // Return error message.
      return $this->renderJsonError($errMessage);
    }
    // Catch exceptions.
    catch(sfException $exception)
    {
      // Write error to log.
      $this->logMessage($exception->getMessage(), 'err');

      // Define error message txt.
      $errMessage = $this->getContext()->getI18N()->__('Ошибка при загрузке файла.', null, 'attachable');

      // Set error message for user.
      $this->getUser()->setFlash('error', $errMessage);

      // Return error message.
      return $this->renderJsonError($errMessage);
    }

    return $this->renderJsonError(array($this->getContext()->getI18N()->__('Неизвестная ошибка.', null, 'attachable')));
  }

  /**
   * Takes the web request and tries to save the uploaded file
   *
   * The web request is expected to have a form and a validator field name
   * passed as arguments. Once an upload is complete this action will initiate
   * the form class and get the validator with the name and pass an array of
   * data through the clean method.
   *
   * data for validator
   * array(
   *   name => $filename
   *   file => $pathToFile
   *   type => $mimeType
   * )
   *
   * Expected to return JSON. The 3 successful states it'll return is
   *
   * incomplete file:
   *    status: incomplate
   *
   * validation error:
   *    status: error
   *    message: $errorMessage
   *
   * comlete file:
   *    status: complete
   *    filename: $fileName (note not path)
   *
   * @see sfActions::execute
   */
  public function executeByForm(sfWebRequest $request)
  {
    // Define plupload class object.
    $plupload = new jDFUploadedFilePlupload(
      $request->getParameter('chunk', 0), 
      $request->getParameter('chunks', 0),
      $request->getParameter('name', 'uploadedfile.tmp'));

    // Initialize upload.
    $plupload->processUpload(
      $request->getFiles($request->getParameter('file-data-name', 'img')),
      $plupload->getContentType($request)
    );

    // Filter request parameters.
    $options = array_filter($request->getParameterHolder()->getAll(), function($value) { return (! is_array($value)); });
    $this->getVarHolder()->add(array_filter($request->getParameterHolder()->getAll(), function($value) { return (! is_array($value)); }));
    
    // Define result array.
    $this->returnData = array();

    // Check incomplete upload.
    if (! $plupload->isComplete())
    {
      $this->returnData = array('status' => 'incomplete');
      return sfView::ERROR;
    }

    // Fetch form class name.
    $formClass = $request->getParameter('form');

    // Fetch form field name.
    $fieldName = $request->getParameter('field');

    // Check form class exists.
    if (! class_exists($formClass))
    {
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Форма "%s" не найдена.', null, 'attachable'), $formClass));
    }

    // Define form class.
    $form = new $formClass();

    // Fetch list fields.
    $arFields = array_keys($form->getWidgetSchema()->getFields());

    // Check form field exists.
    if (! in_array($fieldName, $arFields))
    {
      throw new sfException(sprintf($this->getContext()
        ->getI18N()->__('Поле "%s" не найдено.', null, 'attachable'), $fieldName));
    }

    // Define validator.
    $validator = $form->getValidator($fieldName);

    try
    {
      // If file attach to non file field - use default file validator.
      if (! ($validator instanceof sfValidatorFile)) {
        $validator = new sfValidatorFile(array(
            'required'    => false,
            'mime_types'  =>'web_images',
            'path'        => sfConfig::get('sf_upload_dir')
          )
        );
      }

      // Define file object.
      $file = $validator->clean(array(
        'name'      => $plupload->getOriginalFilename(),
        'file'      => sfConfig::get('sf_upload_dir'),
        'type'      => $plupload->getMimeType(),
        'tmp_name'  => $plupload->getFilePath()
      ));

      // If file is not saved - save it.
      if (! $file->isSaved()) { $file->save(); }

      // Define saved filename.
      $sFileName = basename($file->getSavedName());
      $plupload->setFilename($sFileName);

      // Checks saved name.
      if (! file_exists($file->getSavedName()))
      {
        throw new sfException($this->getContext()
          ->getI18N()->__('Возникла ошибка при сохранении файла.', null, 'attachable'));
      }

      // Detect file mime type.
      $sMimeType = jFileMime::getMimeType($plupload->getFilePath());

      // Define mime type in the table of mime types.
      $iFileTypeId = Doctrine::getTable('jFileMimeType')->createQuery('ct')->select('ct.id')
                      ->where('ct.name = ?', $sMimeType)
                      ->fetchOne(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

      if (! $iFileTypeId)
      {
        // Create jFileMimeType record.
        $fileType = new jFileMimeType();
        $fileType->name = $sMimeType;
        $fileType->save();

        $iFileTypeId = $fileType->id;
        unset($fileType);
      }

      // Create attachment record.
      $attachment = new jFile();

      if ($request->hasParameter('label')) $attachment['flabel'] = $request->getParameter('label');

      $attachment['fkey'] = ($request->hasParameter('key') ? $request->getParameter('key') : md5(session_id()));
      $attachment['mime_type_id']     = $iFileTypeId;
      $attachment['fname']            = $sFileName;
      $attachment['original_name']    = $file->getOriginalName();
      $attachment['size']             = $file->getSize();
      $attachment['extension']        = $file->getOriginalExtension();
      $attachment['is_required']      = (int) false;
      $attachment['is_active']        = (int) false;

      // If not guest.
      if (! $this->getUser()->isGuest()) {
        $attachment['creator_id']  = $this->getUser()->getProfile()->getId();
      }

      // Save attachmant.
      $attachment->save();     

      // Define result array.
      $this->returnData = array(
        'path'    => rtrim(substr(sfConfig::get('sf_upload_dir'), strlen(sfConfig::get('sf_web_dir'))), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR,
        'oname'   => $file->getOriginalName(),
        'name'    => $sFileName,
        'msg'     => $this->getContext()->getI18N()->__('Файл загружен!', null, 'contest'),
        'status'  => 'complete'
      );

      // Save key of attachment.
      if (isset($attachment['fkey'])) { $this->returnData['key'] = $attachment['fkey']; }
    }
    // Catch errors by validator.
    catch (sfValidatorError $e)
    {
      $this->returnData = array('status' => 'error', 'message' => $e->getMessage());
      return sfView::ERROR;
    }
    // Catch any exceptions.
    catch (Exception $e)
    {
      $this->returnData = array('status' => 'error', 'message' => $e->getMessage());
      return sfView::ERROR;
    }

    return sfView::SUCCESS;
  }
}
