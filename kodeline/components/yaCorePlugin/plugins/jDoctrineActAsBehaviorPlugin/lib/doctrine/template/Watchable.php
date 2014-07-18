<?php
/**
 * Template for the watchable behavior 
 * which allows add watchers to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Watchable extends Behavior_Template
{
  /**
   * Array of watchable options
   * 
   * @var array
   */
  protected $_options = array('watchable'  =>  array('name'         =>  'is_watchable',
                                                      'alias'         =>  null,
                                                      'disabled'      =>  false),
                              'watched'    =>  array('name'         =>  'has_watched',
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
   * {@inheritDoc}
   */
  public function setUp()
  {
    parent::setUp();

    //$this->hasMany($this->getTable()->getComponentName() . ' as Rates', array('local' => 'id', 'foreign' => $this->getTable()->getTableName() . '_id')); 
    //$this->_plugin->initialize($this->_table);
  }
  
  /**
   * {@inheritDoc}
   */
  public function setTableDefinition()
  {
    // Create watchable field.
    if ( ! $this->_options['watchable']['disabled']) {
      $name = $this->_options['watchable']['name'];

      if ($this->_options['watchable']['alias']) {
        $name .= ' as ' . $this->_options['watchable']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 1));
    }

    // Create watched field.
    if ( ! $this->_options['watched']['disabled']) {
      $name = $this->_options['watched']['name'];

      if ($this->_options['watched']['alias']) {
        $name .= ' as ' . $this->_options['watched']['alias'];
      }

      $this->hasColumn($name, 'integer', 8, array('notnull' => true, 'default' => 0, 'unsigned' => true));
    }
    
    $this->addListener(new Doctrine_Template_Listener_Watchable($this->_options));
  }
    
  /**
    * Add new comment.
    * @param jComment $comment
    */
  public function addWatcher($observed_id, $watcher_id)
  {
    $modelName = $this->getInvoker()->getTable()->getComponentName();
    
    $comment->set('model_id', $this->getWatchedModelId($modelName));
    $comment->set('record_id', $this->_invoker->get('id'));
    $comment->save();
            
    return $this->getInvoker();
  }
  
  /**
    * Return count of comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function getNbWatchers($bActiveOnly = true) {
    return $this->getWatchesQuery($bActiveOnly)->count();
  }
     
  /**
    * Check for available comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function hasWatchers($bActiveOnly = true) {
    return $this->getNbWatchers($bActiveOnly) > 0;
  }
     
  /**
    * Получить активные комментарии
    * @return Doctrine_Collection записи активных комментариев
    */
  public function getWatchers() {
    return $this->getWatchesQuery(true)->execute();
  }
     
  /**
    *
    * Получить все комментарии в том числе неактивные
    */
  public function getAllWatchers() {
      return $this->getWatchesQuery(false)->execute();
  }

  /**
   */
  public function getGeneralQuery()
  {
    // Definition for model of the type.
    $iComponent = $this->getWatchedModelId($this->getInvoker()->getTable()->getComponentName());
    
    // Prepare query.
    return Doctrine::getTable('jWatched')->createQuery('watchable')->where('watchable.model_id = ' . $iComponent);
  }
     
  /**
    * Собирает запрос на получение комментариев
    * @param boolean $bActiveOnly true брать только активные комментарии
    */
  public function getWatchesQuery($bActiveOnly = true)
  {    
    // Prepare query.
    $query = $this->getGeneralQuery()->andWhere('watchable.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly) {
      $query->andWhere('watchable.is_active = ?', true);
    }

    return $query;
  }
}
