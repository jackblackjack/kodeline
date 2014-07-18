<?php
/**
 * Template for the watchdogable behavior which 
 * automatically sets the created by and updated by 
 * columns when a record is inserted and updated.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Watchdogable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Watchdogable extends Doctrine_Template
{
  /**
   * Array of watchdogable options
   *
   * @var string
   */
  protected $_options = array('revisions'   =>  false,
                              'creator'     =>  array('name'          =>  'created_by',
                                                      'alias'         =>  null,
                                                      'profile'       =>  true,
                                                      'type'          =>  'integer',
                                                      'disabled'      =>  false,
                                                      'options'       =>  array('notnull' => true)
                                                ),
                              'updater'     =>  array('name'          =>  'updated_by',
                                                      'alias'         =>  null,
                                                      'profile'       =>  true,
                                                      'type'          =>  'integer',
                                                      'disabled'      =>  false,
                                                      'onInsert'      =>  true,
                                                      'options'       =>  array('notnull' => true)
                                                )
                              );

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
   * Set table definition for Watchdogable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    // Check already exists and create creator field.
    if ( ! $this->_options['creator']['disabled']) {
      $name = $this->_options['creator']['name'];

      if ($this->_options['creator']['alias']) {
        $name .= ' as ' . $this->_options['creator']['alias'];
      }

      $this->hasColumn($name, $this->_options['creator']['type'], null, $this->_options['creator']['options']);
    }

    // Check already exists Create updater field.
    if ( ! $this->_options['updater']['disabled']) {
      $name = $this->_options['updater']['name'];

      if ($this->_options['updater']['alias']) {
        $name .= ' as ' . $this->_options['updater']['alias'];
      }

      $this->hasColumn($name, $this->_options['updater']['type'], null, $this->_options['updater']['options']);
    }

    $this->addListener(new Doctrine_Template_Listener_Watchdogable($this->_options));
  }
}