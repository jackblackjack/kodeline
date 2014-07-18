<?php
/**
 * klCmsBackendMenu class.
 * 
 * @package     kodeline-cms
 * @subpackage  klCmsBackendPlugin
 * @category    menu
 * @author      Kodeline
 * @version     $Id$
 */
class klCmsBackendMenu extends ArrayObject
{
  /**
   * Class constructor.
   *
   * @param array $data
   */
  public function __construct($data = array())
  {
    // Call parent constructor.
    parent::__construct($data, ArrayObject::ARRAY_AS_PROPS);
    
    // Create build menu event.
    yaContext::getInstance()->getEventDispatcher()->notify(
      new sfEvent($this, 'backend.menu.build', array(
        'culture' => yaContext::getInstance()->getUser()->getCulture(),
        'user'    => yaContext::getInstance()->getUser()
        )
      )
    );
  }
}

