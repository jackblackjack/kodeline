<?php
/**
 * Template for the Credentialable behavior which 
 * automatically sets the credentials of the user to record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Credentialable
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Credentialable extends Doctrine_Template
{
  /**
   * Array of credentialable options
   * 
   * @var array
   */
  protected $_options = array('alias' => '', 'name' => 'credentials');

  /**
   * __construct
   *
   * @param array $options 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  /**
   * Set table definition for Credentialable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    // Definition of the column.
    $columnName = $this->_options['name'];
    
    if (! empty($this->_options['alias'])) {
      $columnName .= ' as ' . $this->_options['alias'];
    }
    
    $this->hasColumn($columnName, 'clob', 16777215, array('nonull' => false, 'default' => null));
    $this->addListener(new Doctrine_Template_Listener_Credentialable($this->_options));
  }
}