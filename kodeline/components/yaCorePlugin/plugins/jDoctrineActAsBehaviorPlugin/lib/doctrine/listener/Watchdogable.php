<?php
/**
 * Listener for the watchdogable behavior which 
 * automatically sets the created by and updated by 
 * columns when a record is inserted and updated.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Watchdogable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Watchdogable extends Doctrine_Record_Listener
{
  /**
   * Array of watchdogable options
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

  /**
   * Set the created and updated Watchdogable columns when a record is inserted
   *
   * @param Doctrine_Event $event
   * @return void
   */
  public function preInsert(Doctrine_Event $event)
  {
    // Set value for the creator field.
    if (! $this->_options['creator']['disabled'])
    {
      $creatorName = $event->getInvoker()->getTable()->getFieldName($this->_options['creator']['name']);
      $modified = $event->getInvoker()->getModified();

      if ( ! isset($modified[$creatorName]))
      {
        $event->getInvoker()->$creatorName = yaContext::getInstance()->getUser()->getId();
      }
    }

    // Set value for the updater field.
    if ( ! $this->_options['updater']['disabled'] && $this->_options['updater']['onInsert'])
    {
      $updaterName = $event->getInvoker()->getTable()->getFieldName($this->_options['updater']['name']);
      $modified = $event->getInvoker()->getModified();

      if ( ! isset($modified[$updaterName]))
      {
        $event->getInvoker()->$updaterName = yaContext::getInstance()->getUser()->getId();
      }
    }
  }

  /**
   * Process update record.
   *
   * @param Doctrine_Event $evet
   * @return void
   */
  public function preUpdate(Doctrine_Event $event)
  {
    // Set value for the updater field.
    if ( ! $this->_options['updater']['disabled'])
    {
      $updaterName = $event->getInvoker()->getTable()->getFieldName($this->_options['updater']['name']);
      $modified = $event->getInvoker()->getModified();

      if ( ! isset($modified[$updaterName]))
      {
        $event->getInvoker()->$updaterName = yaContext::getInstance()->getUser()->getId();
      }
    }

    // If model supports revisions - sets event for revision calculate.
    if ($this->_options['revisions'])
    {
      // Send event for calculate revision.
      yaContext::getInstance()->getEventDispatcher()->notify(
        new sfEvent(null, 'modules.background', array('module' => 'Watchdogable', 'record' => $event->getInvoker(), 'updater' => yaContext::getInstance()->getUser()->getId()))
      );
    }
  }

  /**
   * Process update record.
   * Set the updater field for dql update queries
   *
   * @param Doctrine_Event $evet
   * @return void
   */
  public function preDqlUpdate(Doctrine_Event $event)
  {
    if ( ! $this->_options['updater']['disabled'])
    {
      $params = $event->getParams();

      $updaterName = $event->getInvoker()->getTable()->getFieldName($this->_options['updater']['name']);
      $field = $params['alias'] . '.' . $updaterName;

      $query = $event->getQuery();
      if ( ! $query->contains($field))
      {
        $query->set($field, '?', yaContext::getInstance()->getUser()->getId());
      }
    }

    // If model supports revisions - sets event for revision calculate.
    if ($this->_options['revisions'])
    {
      // Send event for calculate revision.
      yaContext::getInstance()->getEventDispatcher()->notify(
        new sfEvent(null, 'modules.background', array('module' => 'Watchdogable', 'record' => $event->getInvoker(), 'updater' => yaContext::getInstance()->getUser()->getId()))
      );
    }
  }
}