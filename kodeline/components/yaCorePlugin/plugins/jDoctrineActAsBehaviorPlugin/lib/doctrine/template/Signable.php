<?php
/**
 * Signable behavior template.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  lib
 * @category    template
 * @author      Alexey G. Chugarev
 */
class Doctrine_Template_Signable extends Behavior_Template
{
  /**
   * Array of signable options
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
   * Add new sign item as jSignableItem.
   * 
   * @param jReweableItem $item
   * @return Doctrine_Record
   */
  public function addSignRecord(jSignableRecord $item)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();
    
    $item->set('component_id', $this->getComponentId($componentName));
    $item->set('record_id', $this->_invoker->get('id'));
    $item->save();
            
    return $this->getInvoker();
  }

  /**
   * Add new review item as text.
   * 
   * @param jReweableItem $item
   * @return Doctrine_Record
   */
  public function addSign($sBody, $iSenderId = null, $sTitle = '')
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();

    $review = new jSignableRecord();

    if (strlen($sTitle))
    {
      $review['title'] = $sTitle;
    }

    if (! is_null($iSenderId))
    {
      $review['sender_id'] = $iSenderId;
    }
    
    $review['body'] = $sBody;

    return $this->addSignRecord($review);
  }
  
  /**
   * Return count reviews of the record
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * @return integer
   */
  public function getNbSigns($bActiveOnly = true, $iSenderId = null)
  {
    $query = $this->getReviewQuery($bActiveOnly);

    if (! is_null($iSenderId))
    {
      $query->andWhere('ri.sender_id = ?', $iSenderId);
    }

    return $query->count();
  }

  /**
   * Return count publiched reviews of the record
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * @return integer
   */
  public function getNbPublishedSigns($bActiveOnly = true, $iSenderId = null)
  {
    $query = $this->getReviewQuery($bActiveOnly, true);

    if (! is_null($iSenderId))
    {
      $query->andWhere('ri.sender_id = ?', $iSenderId);
    }

    return $query->count();
  }
     
  /**
   * Check for available reviews for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * 
   * @return boolean
   */
  public function hasSigns($bActiveOnly = true, $iSenderId = null)
  {
    return (0 < $this->getNbReviews($bActiveOnly, $iSenderId));
  }

     
  /**
   * Returns reviews for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * 
   * @return Doctrine_Collection
   */
  public function getSigns($bActiveOnly = true, $iSenderId = null)
  {
    $query = $this->getReviewQuery($bActiveOnly);

    if (! is_null($iSenderId))
    {
      $query->andWhere('ri.sender_id = ?', $iSenderId);
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

         
  /**
   * Prepare query for fetch reviews.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param boolean $bPublished Flag for fetch published reviews.
   * 
   * @return Doctrine_Query
   */
  public function getSignsQuery($bActiveOnly = true, $bPublished = false)
  {    
    // Prepare query for fetch reviewable items.
    $query = Doctrine_Core::getTable('jSignableRecord')->createQuery('ri')->select('*')
              ->where('ri.component_id = ?', $this->getComponentId($this->getInvoker()->getTable()->getComponentName()))
              ->andWhere('ri.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly)
    {
      $query->andWhere('ri.is_active = ?', true);
    }

    if ($bPublished)
    {
      $query->andWhere('ri.is_published = ?', true);
    }

    return $query;
  }
}
