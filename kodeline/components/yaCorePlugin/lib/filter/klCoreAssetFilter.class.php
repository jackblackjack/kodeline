<?php
/**
 * This filter processes misc core related filtering.
 *
 * @package     yaCorePlugin
 * @subpackage  filter
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klCoreAssetFilter extends sfFilter
{
  /**
   * Code of async scripts loader.
   * 
   * @link http://headjs.com/
   * @var string
   */
  const ASYNC_SCRIPT_LOADER = '(function(n,t){"use strict";function w(){}function u(n,t){if(n){typeof n=="object"&&(n=[].slice.call(n));for(var i=0,r=n.length;i<r;i++)t.call(n,n[i],i)}}function it(n,i){var r=Object.prototype.toString.call(i).slice(8,-1);return i!==t&&i!==null&&r===n}function s(n){return it("Function",n)}function a(n){return it("Array",n)}function et(n){var i=n.split("/"),t=i[i.length-1],r=t.indexOf("?");return r!==-1?t.substring(0,r):t}function f(n){(n=n||w,n._done)||(n(),n._done=1)}function ot(n,t,r,u){var f=typeof n=="object"?n:{test:n,success:!t?!1:a(t)?t:[t],failure:!r?!1:a(r)?r:[r],callback:u||w},e=!!f.test;return e&&!!f.success?(f.success.push(f.callback),i.load.apply(null,f.success)):e||!f.failure?u():(f.failure.push(f.callback),i.load.apply(null,f.failure)),i}function v(n){var t={},i,r;if(typeof n=="object")for(i in n)!n[i]||(t={name:i,url:n[i]});else t={name:et(n),url:n};return(r=c[t.name],r&&r.url===t.url)?r:(c[t.name]=t,t)}function y(n){n=n||c;for(var t in n)if(n.hasOwnProperty(t)&&n[t].state!==l)return!1;return!0}function st(n){n.state=ft;u(n.onpreload,function(n){n.call()})}function ht(n){n.state===t&&(n.state=nt,n.onpreload=[],rt({url:n.url,type:"cache"},function(){st(n)}))}function ct(){var n=arguments,t=n[n.length-1],r=[].slice.call(n,1),f=r[0];return(s(t)||(t=null),a(n[0]))?(n[0].push(t),i.load.apply(null,n[0]),i):(f?(u(r,function(n){s(n)||!n||ht(v(n))}),b(v(n[0]),s(f)?f:function(){i.load.apply(null,r)})):b(v(n[0])),i)}function lt(){var n=arguments,t=n[n.length-1],r={};return(s(t)||(t=null),a(n[0]))?(n[0].push(t),i.load.apply(null,n[0]),i):(u(n,function(n){n!==t&&(n=v(n),r[n.name]=n)}),u(n,function(n){n!==t&&(n=v(n),b(n,function(){y(r)&&f(t)}))}),i)}function b(n,t){if(t=t||w,n.state===l){t();return}if(n.state===tt){i.ready(n.name,t);return}if(n.state===nt){n.onpreload.push(function(){b(n,t)});return}n.state=tt;rt(n,function(){n.state=l;t();u(h[n.name],function(n){f(n)});o&&y()&&u(h.ALL,function(n){f(n)})})}function at(n){n=n||"";var t=n.split("?")[0].split(".");return t[t.length-1].toLowerCase()}function rt(t,i){function e(t){t=t||n.event;u.onload=u.onreadystatechange=u.onerror=null;i()}function o(f){f=f||n.event;(f.type==="load"||/loaded|complete/.test(u.readyState)&&(!r.documentMode||r.documentMode<9))&&(n.clearTimeout(t.errorTimeout),n.clearTimeout(t.cssTimeout),u.onload=u.onreadystatechange=u.onerror=null,i())}function s(){if(t.state!==l&&t.cssRetries<=20){for(var i=0,f=r.styleSheets.length;i<f;i++)if(r.styleSheets[i].href===u.href){o({type:"load"});return}t.cssRetries++;t.cssTimeout=n.setTimeout(s,250)}}var u,h,f;i=i||w;h=at(t.url);h==="css"?(u=r.createElement("link"),u.type="text/"+(t.type||"css"),u.rel="stylesheet",u.href=t.url,t.cssRetries=0,t.cssTimeout=n.setTimeout(s,500)):(u=r.createElement("script"),u.type="text/"+(t.type||"javascript"),u.src=t.url);u.onload=u.onreadystatechange=o;u.onerror=e;u.async=!1;u.defer=!1;t.errorTimeout=n.setTimeout(function(){e({type:"timeout"})},7e3);f=r.head||r.getElementsByTagName("head")[0];f.insertBefore(u,f.lastChild)}function vt(){for(var t,u=r.getElementsByTagName("script"),n=0,f=u.length;n<f;n++)if(t=u[n].getAttribute("data-headjs-load"),!!t){i.load(t);return}}function yt(n,t){var v,p,e;return n===r?(o?f(t):d.push(t),i):(s(n)&&(t=n,n="ALL"),a(n))?(v={},u(n,function(n){v[n]=c[n];i.ready(n,function(){y(v)&&f(t)})}),i):typeof n!="string"||!s(t)?i:(p=c[n],p&&p.state===l||n==="ALL"&&y()&&o)?(f(t),i):(e=h[n],e?e.push(t):e=h[n]=[t],i)}function e(){if(!r.body){n.clearTimeout(i.readyTimeout);i.readyTimeout=n.setTimeout(e,50);return}o||(o=!0,vt(),u(d,function(n){f(n)}))}function k(){r.addEventListener?(r.removeEventListener("DOMContentLoaded",k,!1),e()):r.readyState==="complete"&&(r.detachEvent("onreadystatechange",k),e())}var r=n.document,d=[],h={},c={},ut="async"in r.createElement("script")||"MozAppearance"in r.documentElement.style||n.opera,o,g=n.head_conf&&n.head_conf.head||"head",i=n[g]=n[g]||function(){i.ready.apply(null,arguments)},nt=1,ft=2,tt=3,l=4,p;if(r.readyState==="complete")e();else if(r.addEventListener)r.addEventListener("DOMContentLoaded",k,!1),n.addEventListener("load",e,!1);else{r.attachEvent("onreadystatechange",k);n.attachEvent("onload",e);p=!1;try{p=!n.frameElement&&r.documentElement}catch(wt){}p&&p.doScroll&&function pt(){if(!o){try{p.doScroll("left")}catch(t){n.clearTimeout(i.readyTimeout);i.readyTimeout=n.setTimeout(pt,50);return}e()}}()}i.load=i.js=ut?lt:ct;i.test=ot;i.ready=yt;i.ready(r,function(){y()&&u(h.ALL,function(n){f(n)});i.feature&&i.feature("domloaded",!0)})})(window);';

  /**
   * Return version key
   * 
   * @return string
   */
  private function getVersionKey()
  {
    if (null === sfConfig::get('app_version', null))
    {
      if ('prod' !== sfConfig::get('sf_environment'))
      {
        mt_srand((double) microtime() * 100000);
        sfConfig::set('app_version', mt_rand());
      }
    }

    return sfConfig::get('app_version', null);
  }

  /**
   * @see sfFilter
   */
  public function initialize($context, $parameters = array())
  {
    $context->getConfiguration()->loadHelpers(array('Tag', 'Asset'));
    return parent::initialize($context, $parameters);
  }

  /**
   * Executes filter.
   * 
   * @param sfFilterChain $filterChain
   */
  public function execute(sfFilterChain $filterChain)
  {
    // Execute as last.
    $filterChain->execute();

    // Define response object.
    $response = $this->getContext()->getResponse();

    // If request content type is not equal 'html' - skip any includes.
    if (false === stripos($response->getContentType(), 'html')) {
      return true;
    }

    // Define current page's content.
    $content = $response->getContent();

    if (false !== ($pos = strpos($content, '</head>')))
    {
      $html = null;

      // Include page stylesheets.
      if (! sfConfig::get('symfony.asset.stylesheets_included', false))
      {
        $html .= get_stylesheets($response);
      }

      // Include page javascripts.
      if (! sfConfig::get('symfony.asset.javascripts_included', false))
      {
        $html .= get_javascripts($response);
      }

      if ($html)
      {
        $response->setContent(substr($content, 0, $pos) . $html . substr($content, $pos));
      }
      unset($html);

      // Include inline javascript code before tagname "head".
      $this->insertInlineJavascriptBeforeTag($response, yaWebResponse::LOCATION_HEAD, yaWebResponse::FIRST, '</head>');
    }

    //sfConfig::set('symfony.asset.javascripts_included', false);
    //sfConfig::set('symfony.asset.stylesheets_included', false);

    // Include javascript files before tagname "body".
    if ($response->hasFileScrips(yaWebResponse::LOCATION_BODY))
    {
      $this->insertDocumentJavascriptBeforeTag($response, yaWebResponse::LOCATION_BODY, '</body>');
    }

    // Include inline javascript code before tagname "body".
    $this->insertInlineJavascriptBeforeTag($response, yaWebResponse::LOCATION_BODY, yaWebResponse::FIRST, '<body>');
    $this->insertInlineJavascriptBeforeTag($response, yaWebResponse::LOCATION_BODY, yaWebResponse::LAST, '</body>');

    // Insert async loader.
    $this->insertAsyncLoader($response, '</body>');

    // Include async javascript files before tagname "body".
    if ($response->hasFileScrips(yaWebResponse::METHOD_ASYNC))
    {
      $this->insertAsyncJavascriptBeforeTag($response, array(yaWebResponse::FIRST, yaWebResponse::MIDDLE, yaWebResponse::LAST), '</body>');
    }
  }

  /**
   * Add javascript insert tag before tag on the page.
   * 
   * @param sfResponse $response
   * @param string $location
   * @param string $tag Tagname
   */
  protected function insertDocumentJavascriptBeforeTag(sfResponse $response, $location, $tag)
  {    
    $content = $response->getContent();
    $configKey = 'symfony.asset.document_javascripts_included_' . $location;

    if (false !== ($pos = strpos($content, $tag)) && !sfConfig::get($configKey, false))
    {
      sfConfig::set($configKey, true);

      if ($arScripts = $response->getFilesByLocation($location))
      {
        $html = '';
        foreach($arScripts as $file => $arOptions)
        {
          unset($arOptions['location']);
          if (! preg_match('#((ht|f)tp(s?)\:)?//\S+#is', $file))
          {
            $pathFile = javascript_path($file) . (null !== ($v = $this->getVersionKey()) ? ((false === strpos($file, '?')) ? '?' : '&') . "dstamp=" . $v : '');
            $html .= javascript_include_tag($pathFile, $arOptions);
          }
          else {
            $html .= javascript_include_tag($file, $arOptions); 
          }
        }

        $response->setContent(substr($content, 0, $pos) . $html . substr($content, $pos));
      }
    }

    return $content;
  }

  /**
   * Add inline javascript insert tag before tag on the page.
   * 
   * @param sfResponse $response
   * @param string $location
   * @param string $tag Tagname
   */
  protected function insertInlineJavascriptBeforeTag(sfResponse $response, $location, $position, $tag)
  {
    $content = $response->getContent();
    $configKey = sprintf('symfony.asset.inline_javascripts_included_%s_%s', $location, $position);

    if (! sfConfig::get($configKey, false) && false !== ($pos = strpos($content, $tag)))
    {
      sfConfig::set($configKey, true);

      if ($js = $response->getInlineScripts($location, $position))
      {
        $response->setContent(substr($content, 0, $pos) . '<script type="text/javascript">/*<![CDATA[*/' . $js . "/*]]>*/</script>" . substr($content, $pos));
      }
    }

    return $content;
  }

  /**
   * Add async loader scripts in the page content.
   * 
   * @param sfResponse $response System response.
   * @param string $tag Tagname, for insert content.
   */
  protected function insertAsyncLoader(sfResponse $response, $tag)
  {
    $content = $response->getContent();
    if (! sfConfig::get('symfony.asset.async_loader_included', false) && false !== ($pos = strpos($content, $tag)))
    {
      sfConfig::set('symfony.asset.async_loader_included', true);
      $response->setContent(substr($content, 0, $pos) . '<script type="text/javascript">/*<![CDATA[*/' . PHP_EOL . self::ASYNC_SCRIPT_LOADER . PHP_EOL . "/*]]>*/</script>" . substr($content, $pos));
    }
  }

  /**
   * Add async loads scripts insert tag before tag on the page.
   * 
   * @param sfResponse $response
   * @link  http://headjs.com/
   * @param array      $arPositions Positions for adds.
   * @param string     $tag Tagname.
   */
  protected function insertAsyncJavascriptBeforeTag(sfResponse $response, $arPositions, $tag)
  {
    $configKey = sprintf('symfony.asset.async_javascripts_included_%s', yaWebResponse::METHOD_ASYNC);
    $content = $response->getContent();

    if (! sfConfig::get($configKey, false) && false !== ($pos = strpos($content, $tag)))
    {
      sfConfig::set($configKey, true);

      $arScriptGroups = array();
      $arKeys = array_keys($arPositions);
      $szKeys = count($arKeys);
      $strAsyncLoadString = 'head';

      // Fetch scripts for loading.
      for($i = 0; $i < $szKeys; $i++)
      {
        // Fetch scripts of a position.
        $arPositionScripts = $response->getFilesByLocation(yaWebResponse::METHOD_ASYNC, $arPositions[$arKeys[$i]]);

        if (count($arPositionScripts))
        {
          $arAsyncScripts = array_keys($arPositionScripts);
          $arAsyncKeys = array_keys($arAsyncScripts);
          $szAsyncKeys = count($arAsyncKeys);

          for($j = 0; $j < $szAsyncKeys; $j++)
          {
            // Prepare grouped scripts.
            if (isset($arPositionScripts[$arAsyncScripts[$j]]['group']))
            { 
              // Save script in the group.
              $arScriptGroups[$arPositionScripts[$arAsyncScripts[$j]]['group']][$arAsyncScripts[$j]] =& $arPositionScripts[$arAsyncScripts[$j]];
            }
            else {
              // Define script path.
              $strScriptPath = javascript_path(trim($arAsyncScripts[$j], '"')) . ((! preg_match('#((ht|f)tp(s?)\:)?//\S+#is', $arAsyncScripts[$j])) ? (null !== ($v = $this->getVersionKey()) ? ((false === strpos($arAsyncScripts[$j], '?')) ? '?' : '&') . "dstamp=" . $v : '') : null);

              // Save script load call.
              if (isset($arPositionScripts[$arAsyncScripts[$j]]['callback']))
              {
                $strAsyncLoadString .= '.load("' . $strScriptPath . '", function(){ if ("function" === typeof(' .  $arPositionScripts[$arAsyncScripts[$j]]['callback'] . ')) ' . $arPositionScripts[$arAsyncScripts[$j]]['callback'] . '.apply(); })';
              }
              else {
                $strAsyncLoadString .= '.load("' . $strScriptPath . '")';
              }
            }
          }
        }
      }

      // Prepare scripts groups for loading.
      if (count($arScriptGroups))
      {
        $strScriptPaths = array();
        $arAsyncScriptGroups = array_keys($arScriptGroups);
        $arAsyncKeys = array_keys($arAsyncScriptGroups);
        $szAsyncKeys = count($arAsyncKeys);

        // Preparing groups.
        for($i = 0; $i < $szAsyncKeys; $i++)
        {
          // Fetch list of the scrips.
          $arAsyncScripts = array_keys($arScriptGroups[$arAsyncScriptGroups[$i]]);
          $szAsyncScriptsKeys = count($arAsyncScripts);
          $strCallbackGroup = '';

          // Preparing script of the groups.
          for($j = 0; $j < $szAsyncScriptsKeys; $j++)
          {
            // Define script path.
            $strScriptPaths[] = javascript_path(trim($arAsyncScripts[$j], '"')) . ((! preg_match('#((ht|f)tp(s?)\:)?//\S+#is', $arAsyncScripts[$j])) ? (null !== ($v = $this->getVersionKey()) ? ((false === strpos($arAsyncScripts[$j], '?')) ? '?' : '&') . "dstamp=" . $v : '') : null);

            // If set callback for script of the group - save for call it.
            if (isset($arScriptGroups[$arAsyncScriptGroups[$i]][$arAsyncScripts[$j]]['callback'])) {
              $strCallbackGroup .= 'if ("function" === typeof(' .  $arScriptGroups[$arAsyncScriptGroups[$i]][$arAsyncScripts[$j]]['callback'] . ')){ ' . $arScriptGroups[$arAsyncScriptGroups[$i]][$arAsyncScripts[$j]]['callback'] . '.apply(); } ';
            }
          }

          // Save scripts load call for group.
          $strAsyncLoadString .= '.load("' . implode('", "', $strScriptPaths) . '"' . (strlen($strCallbackGroup) ? ', function(){' . $strCallbackGroup . '}' : null) . '   )';
        }
      }

      $strAsyncLoadString .= ';';
      $response->setContent(substr($content, 0, $pos) . '<script type="text/javascript">/*<![CDATA[*/' . PHP_EOL . $strAsyncLoadString . PHP_EOL . "/*]]>*/</script>" . substr($content, $pos));
    }
  }
}
