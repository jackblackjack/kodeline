<?php
/**
 * Listener for the signable behavior 
 * which allows add signs to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Signable extends Doctrine_Record_Listener
{
  /**
   * Array of signable options
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