<?php
/**
 * Контроллер по-умолчанию.
 *
 * @package     frontend
 * @subpackage  default
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class defaultActions extends BaseDefaultActions
{

  /**
   * {@inheritDoc}
   * 
   * @param sfRequest $request A request object
   */
  public function executeIndex(sfWebRequest $request)
  {
    /*
    // Выборка списка фильтров системы.
    $this->filters = Doctrine::getTable('FxShopFilter')->createQuery('filters')
                      ->innerJoin('filters.Rules as rules')
                      ->innerJoin('rules.Component as component')
                      ->innerJoin('rules.Parameter as parameter')
                      ->innerJoin('parameter.Translation as psctr WITH psctr.lang = ?', sfContext::getInstance()->getUser()->getCulture())
                      ->fetchArray();
    */
    return sfView::SUCCESS;
  }

  /**
   * Страница контактов компании.
   * 
   * @param sfRequest $request A request object
   */
  public function executeContacts(sfWebRequest $request)
  {
    
    return sfView::SUCCESS;
  }

  /**
   * Страница обратной связи "Задать вопрос".
   * 
   * @param sfWebRequest $request Web request
   */
  public function executeFeedback(sfWebRequest $request)
  {
    try {
      // Define form instance.
      $this->form = new feedbackForm();

      if ($request->isMethod(sfRequest::POST) && $request->hasParameter($this->form->getName()))
      {
        // Bind parameters.
        $this->form->bind($request->getParameter($this->form->getName()));

        if ($this->form->isValid())
        {
          // Fetch tained values.
          $arFormValues = $this->form->getValues();

          // Define parameters list.
          $arMailerParams = sfConfig::get('app_jDoctrineFeedbackPlugin_params', array());

          // Define feedback mailer email types.
          $arMailerTypes = sfConfig::get('app_jDoctrineFeedbackPlugin_types', array());

          // Check exists mail types.
          if (! count($arMailerTypes))
          {
            throw new sfException($this->getContext()->getI18N()->__('Параметры отправки сообщения не найдены', null, 'feedback'));
          }

          // Mail by each type.
          $arSended = array();

          // Init swift mailer.
          // create the mail transport using the 'newInstance()' method
          //$transport = Swift_SendmailTransport::newInstance('/usr/sbin/exim -bs');
          // create the mailer using the 'newInstance()' method
          //$mailer = Swift_Mailer::newInstance($transport);
          //$mailer = yaProjectConfiguration::getMailer();
          $mailer = $this->getMailer();

          // Load partial helper.
          $this->getContext()->getConfiguration()->loadHelpers('Partial');

          // Foreach by email type.
          foreach ($arMailerTypes as $emailType => $arParameters)
          {
            // Define from email address.
            $fromEmail = (empty($arMailerParams['from_email']) ? (empty($arParameters['from_email']) ? null : $arParameters['from_email']) : $arMailerParams['from_email']);

            if (null === $fromEmail)
            {
              throw new sfException($this->getContext()->getI18N()->__('Исходящий адрес сообщения не найден', null, 'feedback'));
            }

            // Define from name address.
            $fromName = (empty($arMailerParams['from_name']) ? (empty($arParameters['from_name']) ? null : $arParameters['from_name']) : $arMailerParams['from_name']);

            if (null === $fromName)
            {
              throw new sfException($this->getContext()->getI18N()->__('Исходящий адресат сообщения не найден', null, 'feedback'));
            }

            // Define destination address.
            $toEmail = (empty($arMailerParams['to_email_use_field']) ? (empty($arParameters['to_email_use_field']) ? null : 
              (! empty($arFormValues[$arParameters['to_email_use_field']]) ? $arFormValues[$arParameters['to_email_use_field']] : null)) : 
              (! empty($arFormValues[$arMailerParams['to_email_use_field']]) ? $arFormValues[$arMailerParams['to_email_use_field']] : null));

            if (null === $toEmail)
            {
              $toEmail = (empty($arMailerParams['to_email']) ? (empty($arParameters['to_email']) ? null : $arParameters['to_email']) : $arMailerParams['to_email']);

              if (null === $toEmail)
              {
                throw new sfException($this->getContext()->getI18N()->__('Адресат сообщения не найден', null, 'feedback'));
              }
            }

            // Define from name address.
            $fromSubject = (empty($arMailerParams['from_subject']) ? (empty($arParameters['from_subject']) ? 'Feedback' : $arParameters['from_subject']) : $arMailerParams['from_subject']);

            // Define email template.
            $emailTemplate = (empty($arMailerParams['template']) ? (empty($arParameters['template']) ? null : $arParameters['template']) : $arMailerParams['template']);

            // Define BCC name addresses.
            $bccEmails = (empty($arMailerParams['bcc_email']) ? (empty($arParameters['bcc_email']) ? null : $arParameters['bcc_email']) : $arMailerParams['bcc_email']);

            // Define message for email.
            if (null === $emailTemplate)
            {
              throw new sfException($this->getContext()->getI18N()->__('Шаблон сообщения не найден!', null, 'feedback'));
            }

            // Prepare message.
            $mailBody = $this->getPartial($emailTemplate, array('values' => $arFormValues));

            $message = $mailer->compose(
              array($fromEmail => $fromName), 
              $toEmail,
              $this->getContext()->getI18N()->__($fromSubject, null, 'feedback'),
              $mailBody
            )
            ->setContentType('text/html')
            ->setSender(array($fromEmail => $fromName))
            ->setFrom(array($fromEmail => $fromName))
            ->setReplyTo(array($fromEmail => $fromName));

            // Define BCC header for email.
            if (null !== $bccEmails)
            {
              if (! is_array($bccEmails)) $bccEmails = array($bccEmails);
              foreach($bccEmails as $bccEmail) $message->addBcc($bccEmail);
            }

            file_put_contents(sfConfig::get('sf_web_dir') . '/feedback.log', PHP_EOL . $mailBody);

            // Send message
            $arSended[$emailType] = $mailer->send($message);
            gc_enable();
            gc_enabled();
            gc_collect_cycles();
            gc_disable();
          }
          $arSended  = array(1);

          //var_dump($arSended); die;

          // Define success message.
          $messageSuccess = (empty($arMailerParams['success_message']) ? (empty($arParameters['success_message']) ? 'Success' : $arParameters['success_message']) : $arMailerParams['success_message']);

          // Define failed message.
          $messageFailed = (empty($arMailerParams['fail_message']) ? (empty($arParameters['fail_message']) ? 'Failed' : $arParameters['fail_message']) : $arMailerParams['fail_message']);

          // Define success url.
          $successUrl = (empty($arMailerParams['success_url']) ? (empty($arParameters['success_url']) ? null : $arParameters['success_url']) : $arMailerParams['success_url']);

          // Define result.
          $arResult = array();

          if (null !== $successUrl) {
            $arResult['url'] = $this->getController()->genUrl($successUrl);
          }

          if (count($arSended))
          {
            $arFormValues = array();
            $this->getUser()->setFlash('success', $this->getContext()->getI18N()->__($messageSuccess, null, 'feedback'));
            $this->renderJsonResult($arResult + array('msg' => $messageSuccess, 'to' => $toEmail));
          }
          else {
            $this->getUser()->setFlash('error', $this->getContext()->getI18N()->__($messageFailed, null, 'feedback'));
            $this->renderJsonError($arResult + array('msg' => $messageFailed, 'to' => $toEmail));
          }
        }
        else {
          $arResult = array();

          if ( $this->form->hasErrors() )
          {
            $arErrors = $this->form->getErrorSchema()->getErrors();
            foreach($arErrors as $field => $error)
            {
              $arResult['fields'][$field] = (string) $error;
            }
          }

          $this->renderJsonError($arResult);
        }

        return sfView::NONE;
      }
    }
    // Catch sfDatabaseException exceptions.
    catch(sfDatabaseException $Exception)
    {
      $this->renderJsonError(array('msg' => $Exception->getMessage()));
    }
    // Обработка исключений класса sfException
    catch(sfException $Exception)
    {
      $this->renderJsonError(array('msg' => $Exception->getMessage()));
    }
    // Обработка исключений класса sfException
    catch(Exception $Exception)
    {
      $this->renderJsonError(array('msg' => $Exception->getMessage()));
    }

    return sfView::SUCCESS;
  }
}