<?php
/**
 * Template for the Complaintable behavior which 
 * allow to adds complaints to tables records.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Credentialable
 * @category    template
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Complaintable extends Behavior_Template
{
  /**
   * Array of complaintable options
   * 
   * @var array
   */
  protected $_options = array();

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
   * Add new complaint item as jComplaint.
   * 
   * @param jComplaint $item
   * @return Doctrine_Record
   */
  public function addComplaintRecord(jComplaint $item)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();
    
    $item->set('component_id', $this->getComponentId($componentName));
    $item->set('record_id', $this->getInvoker()->get('id'));
    $item->save();
            
    return $this->getInvoker();
  }

  /**
   * Add new complaint item as text.
   * 
   * @return Doctrine_Record
   */
  public function addComplaint($sBody, $sTitle = '', $fHate = 0.0, $bPublished = false, $iSenderId = null)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();

    $complaint = new jComplaintRecord();

    if (strlen($sTitle))
    {
      $complaint['title'] = $sTitle;
    }

    if (! is_null($iSenderId))
    {
      $complaint['sender_id'] = $iSenderId;
    }

    $complaint['is_published'] = $bPublished;
    $complaint['hate_level'] = $fHate;
    $complaint['body'] = $sBody;

    return $this->addComplaintItem($complaint);
  }

  /**
   * Add new complaint item as text.
   * 
   * @return Doctrine_Record
   */
  public function addPublicComplaint($sBody, $sTitle = '', $fHate = 0.0, $iSenderId = null)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();

    $complaint = new jComplaintRecord();

    if (strlen($sTitle))
    {
      $complaint['title'] = $sTitle;
    }

    if (! is_null($iSenderId))
    {
      $complaint['sender_id'] = $iSenderId;
    }

    $complaint['is_published'] = true;
    $complaint['hate_level'] = $fHate;
    $complaint['body'] = $sBody;

    return $this->addComplaintItem($complaint);
  }
  
  /**
   * Return count complaints of the record
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * @return integer
   */
  public function getNbComplaints($bActiveOnly = true, $iSenderId = null)
  {
    $query = $this->getComplaintQuery($bActiveOnly);

    if (! is_null($iSenderId))
    {
      $query->andWhere('с.sender_id = ?', $iSenderId);
    }

    return $query->count();
  }

  /**
   * Return count publiched complaints of the record
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * @return integer
   */
  public function getNbPublicComplaints($bActiveOnly = true, $iSenderId = null)
  {
    $query = $this->getComplaintQuery($bActiveOnly, true);

    if (! is_null($iSenderId))
    {
      $query->andWhere('с.sender_id = ?', $iSenderId);
    }

    return $query->count();
  }
     
  /**
   * Check for available complaint for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * 
   * @return boolean
   */
  public function hasComplaints($bActiveOnly = true, $iSenderId = null)
  {
    return (0 < $this->getNbComplaints($bActiveOnly, $iSenderId));
  }

  /**
   * Check for available published complaints for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * 
   * @return boolean
   */
  public function hasPublicComplaints($iSenderId = null, $bActiveOnly = true)
  {
    return (0 < $this->getNbPublicComplaints($bActiveOnly, $iSenderId));
  }
     
  /**
   * Returns complaints for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * 
   * @return Doctrine_Collection
   */
  public function getComplaints($iSenderId = null, $bActiveOnly = true)
  {
    $query = $this->getComplaintQuery($bActiveOnly);

    if (! is_null($iSenderId))
    {
      $query->andWhere('с.sender_id = ?', $iSenderId);
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * Returns published complaints for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param integer $iSenderId ID of the sender
   * 
   * @return Doctrine_Collection
   */
  public function getPublicComplaint($iSenderId = null, $bActiveOnly = true, $iLimit = null, $iOffset = null)
  {
    $query = $this->getComplaintQuery($bActiveOnly, true);

    if (! is_null($iSenderId))
    {
      $query->andWhere('с.sender_id = ?', $iSenderId);
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }
         
  /**
   * Prepare query for fetch complaints.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active complaints.
   * @param boolean $bPublished Flag for fetch published complaints.
   * 
   * @return Doctrine_Query
   */
  public function getComplaintQuery($bActiveOnly = true, $bPublished = false)
  {    
    // Prepare query for fetch complaintable items.
    $query = Doctrine_Core::getTable('jComplaintRecord')->createQuery('с')->select('*')
              ->where('с.component_id = ?', $this->getComponentId($this->getInvoker()->getTable()->getComponentName()))
              ->andWhere('с.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly)
    {
      $query->andWhere('с.is_active = ?', true);
    }

    if ($bPublished)
    {
      $query->andWhere('с.is_published = ?', true);
    }

    return $query;
  }
}
