<?php
/**
 * Listener for the Ignoreable behavior which allows to 
 * ignoring records by foreign components.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Ignoreable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Ignoreable extends Doctrine_Record_Listener
{
  /**
   * Array of ignoreable options
   *
   * @var string
   */
  protected $_options = array();

  /**
   * __construct
   *
   * @param string $options 
   * @return void
   */
  public function __construct(array $options)
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }
}