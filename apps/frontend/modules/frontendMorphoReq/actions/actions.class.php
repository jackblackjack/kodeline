<?php
/**
 * Контроллер работы с морфологией.
 * 
 * @package     frontend
 * @subpackage  frontendService
 * @category    module
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class frontendMorphoReqActions extends yaBaseActions
{
  /**
   * Главная страница раздела "Услуги".
   * 
   * @param sfRequest $request A request object
   */
  public function executeMlanal(sfWebRequest $request)
  {
    // ...
    $arFilesPath = array(
      sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'search.log',
      sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'sphinx.log'
    );

    // Create morpho object.
    //$morpho = new yaMorphyMorphologist('utf-8', 'ru_ru', 'ispell', false);

    foreach($arFilesPath as $file)
    {
      $hndFile = fopen($file, "r");

      if ($hndFile)
      {
        while (($line = fgets($hndFile)) !== false)
        {
          switch($file)
          {
            case sfConfig::get('sf_root_dir') . DIRECTORY_SEPARATOR . 'search.log':
              if (preg_match('%(?>search\?q=)(\S+)[^(?:HTTP/)*]%si', $line, $matches))
              {
                $matches[1] = preg_replace('%&page=\d%i', null, $matches[1]);
                $request = trim(urldecode($matches[1]));
                echo '<br />', $request, ';';

                /*
                $arBaseForms = $morpho->getBaseForm($request);
                if (1 < count($arBaseForms))
                {
                  foreach ($arBaseForms as $word => $forms)
                  {
                    echo join(', ', $forms), '|';
                  }
                }
                */
              }
            break;
          }
        // process the line read.
        }

        fclose($hndFile);
      }
    }





    return sfView::SUCCESS;
  }
}