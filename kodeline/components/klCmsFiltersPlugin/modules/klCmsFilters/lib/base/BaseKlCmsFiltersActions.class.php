<?php
/**
 * Base KlCmsFilters controller.
 * 
 * @package     kodeline-cms
 * @subpackage  klCmsFiltersPlugin
 * @category    base controller
 * @author      Kodeline
 * @version     $Id$
 */
abstract class BaseKlCmsFiltersActions extends yaBaseActions
{
  /**
   * Index action.
   * @param sfWebRequest $request
   */
  public function executeIndex(sfWebRequest $request)
  {
    // Fetch system filters.
    $this->filters = sfFilterConfigHandler::getConfiguration(array(
      sfConfig::get('sf_symfony_lib_dir') . '/config/config/filters.yml',
      sfConfig::get('ya_core_dir') . '/config/filters.yml',
      dirname(sfConfig::get('sf_app_dir')) . '/frontend/config/filters.yml')
    );

    //file_put_contents(sfConfig::get('sf_app_dir') . '/config/filters.yml', sfYaml::dump($this->filters));
  }
}
