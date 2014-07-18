<?php
/**
 * Behaviors toolkit for managment usual methods.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviors
 * @category    toolkit
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class BehaviorTemplateToolkit
{
  /**
   * Array of the types for models.
   * @var array
   */
  protected static $arComponentsCache = array();

  /**
   * Set values for component's object.
   * 
   * @param string $componentName Name of the component.
   * @param array $arDataWithValues Array of the objects and it extended parameters values.
   */
  public static function getComponentIdByName($sComponentName)
  {
    // If value cached in the cache - return it.
    if (array_key_exists($sComponentName, self::$arComponentsCache))
    {
      return self::$arComponentsCache[$sComponentName];
    }
  
    // Fetch value of the type for concrete model.
    $iComponent = Doctrine_Core::getTable('jBehaviorComponent')
                        ->createQuery('bhc')
                        ->select('id')
                        ->where('bhc.name = ?', $sComponentName)
                        ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);
    
    // If model is not found - create new.
    if (! $iComponent)
    {
      $component = new jBehaviorComponent();
      $component['name'] = $sComponentName;
      $component->save();

      $iComponent = $component->getId();
    }
    
    self::$arComponentsCache[$sComponentName] = $iComponent;
    return $iComponent;
  }
}
