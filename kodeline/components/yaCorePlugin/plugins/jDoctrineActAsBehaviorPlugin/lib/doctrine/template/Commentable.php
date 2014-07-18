<?php
/**
 * Template for the commentable behavior 
 * which allows add comments to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Commentable extends Behavior_Template
{
  /**
   * Array of commentable options
   * 
   * @var array
   */
  protected $_options = array('commentable'  =>  array('name'         =>  'is_commentable',
                                                      'alias'         =>  null,
                                                      'disabled'      =>  false),
                              'commented'    =>  array('name'         =>  'is_commented',
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
    //parent::__construct($this->_options);

    //$this->_plugin  = new CommentableGenerator();
  }

  /**
   * {@inheritDoc}
   */
  public function setUp()
  {
    parent::setUp();

    //$this->hasMany($this->getTable()->getComponentName() . ' as Rates', array('local' => 'id', 'foreign' => $this->getTable()->getTableName() . '_id')); 
    //$this->_plugin->initialize($this->_table);
  }
  
  /**
   * Set table definition for Commentable behavior
   *
   * @return void
   */
  public function setTableDefinition()
  {
    // Create commentable field.
    if ( ! $this->_options['commentable']['disabled']) {
      $name = $this->_options['commentable']['name'];

      if ($this->_options['commentable']['alias']) {
        $name .= ' as ' . $this->_options['commentable']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 1));
    }

    // Create commented field.
    if ( ! $this->_options['commented']['disabled']) {
      $name = $this->_options['commented']['name'];

      if ($this->_options['commented']['alias']) {
        $name .= ' as ' . $this->_options['commented']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 0));
    }

    $this->addListener(new Doctrine_Template_Listener_Commentable($this->_options));
  }
   
  /**
    * Add new comment.
    * @param jComment $comment
    */
  public function addCommentRecord(jCommentableRecord $comment)
  {
    $modelName = $this->getInvoker()->getTable()->getComponentName();
    
    $comment->set('component_id', $this->getComponentId($modelName));
    $comment->set('record_id', $this->_invoker->get('id'));
    $comment->save();
            
    return $this->getInvoker();
  }
  
  /**
    * Return count of comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function getNbComments($bActiveOnly = true, $iSender = null, $afterTimeStamp = null)
  {
    $query = $this->getCommentsQuery($bActiveOnly);

    if ($iSender) $query->andWhere('commentable.user_id = ?', $iSender);
    if ($afterTimeStamp) $query->andWhere('commentable.created_at = ?', date('Y-m-d H:i:s', $afterTimeStamp));

    return $query->count();
  }
     
  /**
    * Check for available comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function hasComments($bActiveOnly = true) {
    return $this->getNbComments($bActiveOnly) > 0;
  }
     
  /**
    * Получить активные комментарии
    * @return Doctrine_Collection записи активных комментариев
    */
  public function getComments() {
    return $this->getCommentsQuery(true)->execute();
  }
     
  /**
    *
    * Получить все комментарии в том числе неактивные
    */
  public function getAllComments() {
      return $this->getCommentsQuery(false)->execute();
  }
     
  /**
    * Собирает запрос на получение комментариев
    * @param boolean $bActiveOnly true брать только активные комментарии
    */
  public function getCommentsQuery($bActiveOnly = true)
  {
    // Definition for model of the type.
    $iModelType = $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName());
    
    // Prepare query.
    $query = Doctrine_Core::getTable('jCommentableRecord')->createQuery('commentable')->select('commentable.*')
                ->where('commentable.component_id = ?', $iModelType)
                ->andWhere('commentable.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly) {
      $query->andWhere('commentable.is_active = ?', 1);
    }

    return $query;
  }
}
