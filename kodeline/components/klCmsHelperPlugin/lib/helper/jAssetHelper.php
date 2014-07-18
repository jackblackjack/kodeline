<?php
/**
 * yaAssetHelper class.
 *
 * @package     yatutu
 * @subpackage  lib.helper
 * @author      gen
 * @version     SVN: $Id: yaAssetHelper.php 1529 2010-04-28 13:33:27Z pinhead $
 */
class jAssetHelper
{
  /**
   * Automatically adds a class name to the link if the link route matches
   * the current module or route.
   *
   * @see link_to
   * @see http://www.symfony-project.org/snippets/snippet/153
   *
   * @param string $text        Name of the link, i.e. string to appear between the <a> tags
   * @param string $internalUri String 'module/action' or '@rule' of the action
   * @param array $options      Additional options and HTML compliant <a> tag parameters
   *
   * @return string XHTML compliant <a href> tag
   */
  public function navLinkTo($text, $internalUri = '', $options = '')
  {
    static $context, $currentModuleName, $currentRouteName;

    $options = _parse_attributes($options);

    if (!isset($context))
    {
      $context = sfContext::getInstance();
    }

    if (isset($options['module_only']))
    {
      unset($options['module_only']);

      if (!isset($currentModuleName))
      {
        $currentModuleName = $context->getModuleName();
      }

      if ('@' == $internalUri[0])
      {
        // TODO: compare current module name with module name from given route
        $isSelected = false;
      }
      else
      {
        list($routeName, $params) = $context->getController()->convertUrlStringToParameters($internalUri);
        $isSelected = $params['module'] == $currentModuleName;
      }
    }
    else
    {
      if ('@' == $internalUri[0])
      {
        if (!isset($currentRouteName))
        {
          $currentRouteName = $context->getRouting()->getCurrentRouteName();
        }

        $isSelected = substr($internalUri, 1) == $currentRouteName;
      }
      else
      {
        $isSelected = $internalUri == $context->getRouting()->getCurrentInternalUri();
      }
    }

    // tag <a> options
    if (isset($options['selected_class']))
    {
      if ($isSelected)
      {
        $options['class']  = isset($options['class']) ?  $options['class'].' ' : '';
        $options['class'] .= $options['selected_class'];
      }
      unset($options['selected_class']);
    }

    // wrapped tag options
    if (isset($options['tag']))
    {
      $tag = $options['tag'];
      unset($options['tag']);

      $tagOptions = array();

      if (isset($options['tag_class']))
      {
        $tagOptions['class'] = $options['tag_class'];
        unset($options['tag_class']);
      }

      if (isset($options['tag_selected_class']))
      {
        if ($isSelected)
        {
          $tagOptions['class']  = isset($tagOptions['class']) ?  $tagOptions['class'].' ' : '';
          $tagOptions['class'] .= $options['tag_selected_class'];
        }
        unset($options['tag_selected_class']);
      }

      $result = content_tag($tag, link_to($text, $internalUri, $options), $tagOptions);
    }
    else
    {
      $result = link_to($text, $internalUri, $options);
    }

    return $result;
  }

  /**
   * Creates a <a> link tag with sortable parameters.
   *
   * @todo multiple 'class' atrributes
   * @see link_to
   *
   * @param string $name String name of the link, i.e. string to appear between the <a> tags
   * @param string $internalUri String 'module/action' or '@rule' of the action
   * @param string $sortName Sortable field name
   * @param array|string $options Additional HTML compliant <a> tag parameters
   *
   * @return string XHTML compliant <a href> tag
   */
  public function sortableLinkTo($name, $internalUri = '', $sortName = '', $options = array())
  {
    if ($internalUri)
    {
      $internalUri  = rtrim($internalUri, '?&');
      $internalUri .= strpos($internalUri, '?') ? '&' : '?';
    }
    $internalUri .= 'sort='.($sortName ? $sortName : $name);

    $context = sfContext::getInstance();
    $user = $context->getUser();
    $ns = $context->getModuleName().'/'.$context->getActionName().'/sort';

    if ($user->getAttribute('sort', null, $ns) == $sortName)
    {
      $type = $user->getAttribute('type', 'asc', $ns) == 'asc' ? 'desc' : 'asc';

      $options = _parse_attributes($options);
      if (!isset($options['class']))
      {
        $options['class'] = 'sortable-'.$type;
      }
      $internalUri .= '&type='.$type;
    }
    else
    {
      $internalUri .= '&type=asc';
    }

    return link_to($name, $internalUri, $options);
  }

  /**
   * Retrieve page navigator in format:
   *
   * <dl>
   *  <dt>Pages: <strong>1</strong> <a>2</a> <a>3</a> <a>...</a></dt>
   *  <dd><a>Previous</a> <a>Next</a></dd>
   * </dl>
   *
   * @param sfPager $pager
   * @param string $internalUri
   * @param int $linksCount
   *
   * @return string
   */
  public function pagerNavigation($pager, $internalUri, $linksCount = 5)
  {
    $html = '';

    if ($pager->haveToPaginate())
    {
      $linksCount = (int) $linksCount;
      $linksOffset = ceil($linksCount / 2);
      $linksOffsetLeft  = $linksCount % 2 ? $linksOffset : $linksOffset + 1;

      $internalUri  = rtrim($internalUri, '?&');
      $internalUri .= (strpos($internalUri, '?') ? '&' : '?').'page=';

      $html .= '<dl>';
      $html .= '<dt>';
      $html .= __('Pages:', null, 'navigation');

      // Pages one by one
      if ($pager->getPage() > $linksOffsetLeft)
      {
        $html .= link_to('...', $internalUri.($pager->getPage() - $linksOffsetLeft));
      }

      $links = $pager->getLinks($linksCount);
      foreach ($links as $page)
      {
        $html .= ($page == $pager->getPage())
               ? content_tag('strong', $page)
               : link_to($page, $internalUri.$page);
      }

      if ($pager->getLastPage() - $pager->getPage() >= $linksOffset)
      {
        $html .= link_to('...', $internalUri.($pager->getPage() + $linksOffset));
      }

      $html .= '</dt>';
      $html .= '<dd>';

      // First and previous page
      if ($pager->getPage() != 1)
      {
        $html .= '<span class="larr">';
        $html .= link_to(__('Previous', null, 'navigation'), $internalUri.$pager->getPreviousPage());
        $html .= '</span>';
      }

      if ($pager->getPage() != $pager->getCurrentMaxLink())
      {
        $html .= '<span class="rarr">';
        $html .= link_to(__('Next', null, 'navigation'), $internalUri.$pager->getNextPage());
        $html .= '</span>';
      }

      $html .= '</dd>';
      $html .= '</dl>';
    }

    return $html;
  }

  /**
   * Builds validator options array and encodes it to JSON object.
   *
   * @param sfForm $form - sfForm instance
   * @param boolean $asJSON - return result as PHP array or as JSON object (default)
   *
   * return mixed
   */
  public function formValidatorShemaToJson($form, $asJSON = true, $errorContainers = array())
  {
    $jsonResult = array();
    $validatorSchema = $form->getValidatorSchema();

    foreach ($validatorSchema->getFields() as $field => $validator)
    {
      $jsonData = array();
      if ($validator instanceof sfValidatorChoice)
      {
        $jsonData['type']     = 'choice';
        $jsonData['choices']  = $validator->getOption('choices');
        $jsonData['messages'] = $validator->getMessages();

        if (isset($errorContainers[$field]))
        {
          $jsonData['error_container'] = $errorContainers[$field];
        }
      }

      if (empty($jsonData))
      {
        continue;
      }

      $widgetSchema = $form->getWidgetSchema();
      $jsonResult[$form[$field]->renderId()] = $jsonData;
    }


    if ($asJSON)
      return json_encode($jsonResult);

    return $jsonResult;
  }

  /**
   *
   */
  public function include_metas()
  {
    $context = sfContext::getInstance();

    foreach ($context->getResponse()->getMetas() as $name => $content)
    {
      echo tag('meta', array('name' => $name, 'content' => $content))."\n";
    }
  }
}

