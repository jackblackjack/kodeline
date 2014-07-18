<?php
/**
 * Template for the reviewable behavior which add review to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Reviewable extends Behavior_Template
{
  /**
   * Array of reviewable options
   *
   * @var string
   */
  protected $_options = array('reviewable'  =>  array('name'          =>  'is_reviewable',
                                                      'alias'         =>  null,
                                                      'disabled'      =>  false),
                              'reviewed'    =>  array('name'          =>  'is_reviewed',
                                                      'alias'         =>  null,
                                                      'disabled'      =>  false));

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
   * Set table definition for Reviewable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    // Create reviewable field.
    if ( ! $this->_options['reviewable']['disabled']) {
      $name = $this->_options['reviewable']['name'];

      if ($this->_options['reviewable']['alias']) {
        $name .= ' as ' . $this->_options['reviewable']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 1));
    }

    // Create reviewed field.
    if ( ! $this->_options['reviewed']['disabled']) {
      $name = $this->_options['reviewed']['name'];

      if ($this->_options['reviewed']['alias']) {
        $name .= ' as ' . $this->_options['reviewed']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 0));
    }

    $this->addListener(new Doctrine_Template_Listener_Reviewable($this->_options));
  }

  /**
   * Add new review item as jReweableItem.
   * 
   * @param jReweableItem $item
   * @return Doctrine_Record
   */
  public function addReviewRecord(jReweableRecord $item)
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
  public function addReview($sBody, $sTitle = '', $iSenderId = null)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();

    $review = new jReweableRecord();

    if (strlen($sTitle))
    {
      $review['title'] = $sTitle;
    }

    if (! is_null($iSenderId))
    {
      $review['sender_id'] = $iSenderId;
    }
    
    $review['body'] = $sBody;

    return $this->addReviewRecord($review);
  }

  /**
   * Add new review item as text.
   * 
   * @param jReweableItem $item
   * @return Doctrine_Record
   */
  public function addPublishedReview($sBody, $sTitle = '', $iSenderId = null)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();

    $review = new jReweableRecord();

    if (strlen($sTitle))
    {
      $review['title'] = $sTitle;
    }

    if (! is_null($iSenderId))
    {
      $review['sender_id'] = $iSenderId;
    }
    
    $review['body'] = $sBody;
    $review['is_published'] = true;

    return $this->addReviewItem($review);
  }
  
  /**
   * Return count reviews of the record
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * @return integer
   */
  public function getNbReviews($bActiveOnly = true, $iSenderId = null)
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
  public function getNbPublishedReviews($bActiveOnly = true, $iSenderId = null)
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
  public function hasReviews($bActiveOnly = true, $iSenderId = null)
  {
    return (0 < $this->getNbReviews($bActiveOnly, $iSenderId));
  }

  /**
   * Check for available published reviews for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * 
   * @return boolean
   */
  public function hasPublishedReviews($iSenderId = null, $bActiveOnly = true)
  {
    return (0 < $this->getNbPublishedReviews($bActiveOnly, $iSenderId));
  }
     
  /**
   * Returns reviews for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * 
   * @return Doctrine_Collection
   */
  public function getReviews($iSenderId = null, $bActiveOnly = true)
  {
    $query = $this->getReviewQuery($bActiveOnly);

    if (! is_null($iSenderId))
    {
      $query->andWhere('ri.sender_id = ?', $iSenderId);
    }

    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   * Returns published reviews for record.
   * 
   * @param boolean $bActiveOnly Flag for fetch only active reviews.
   * @param integer $iSenderId ID of the sender
   * 
   * @return Doctrine_Collection
   */
  public function getPublishedReviews($iSenderId = null, $bActiveOnly = true, $iLimit = null, $iOffset = null)
  {
    $query = $this->getReviewQuery($bActiveOnly, true);

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
  public function getReviewQuery($bActiveOnly = true, $bPublished = false)
  {    
    // Prepare query for fetch reviewable items.
    $query = Doctrine_Core::getTable('jReweableRecord')->createQuery('ri')->select('*')
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
