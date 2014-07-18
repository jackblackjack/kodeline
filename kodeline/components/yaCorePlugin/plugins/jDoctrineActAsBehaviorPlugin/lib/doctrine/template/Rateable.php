<?php
/**
 * Rateable behavior
 * @author Alexey G. Chugarev
 */
class Doctrine_Template_Rateable extends Behavior_Template
{
  /**
    * Array of rateable options
    * 
    * @var array
    */
  protected $_options = array('rateable'  =>  array('name' => 'is_rateable', 'alias' => null, 'disabled' => false),
                              'rated'     =>  array('name' => 'is_rated', 'alias' => null, 'disabled' => false),
                              'value'     =>  array('name' => 'rate_value', 'alias' => null, 'disabled' => false),
                              'method'    =>  'flat');
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
  public function setTableDefinition()
  {
    // Create rateable field.
    if ( ! $this->_options['rateable']['disabled']) {
      $name = $this->_options['rateable']['name'];

      if ($this->_options['rateable']['alias']) {
        $name .= ' as ' . $this->_options['rateable']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 1));
    }

    // Create rated field.
    if ( ! $this->_options['rated']['disabled']) {
      $name = $this->_options['rated']['name'];

      if ($this->_options['rated']['alias']) {
        $name .= ' as ' . $this->_options['rated']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 0));
    }

    // Create current rate value field.
    if ( ! $this->_options['value']['disabled']) {
      $name = $this->_options['value']['name'];

      if ($this->_options['value']['alias']) {
        $name .= ' as ' . $this->_options['value']['alias'];
      }

      $this->hasColumn($name, 'decimal', 10, array('scale' => 5, 'notnull' => true, 'default' => 0));
    }
    
    // Setup total rate field.
    //$this->hasColumn('rate_total', 'integer', '4', array('notnull' => true, 'default' => 0));

    // Setup average rate field.
    //$this->hasColumn('value', 'float', '4', array('notnull' => true, 'default' => 0));

    // Setup summary rate.
    //$this->hasColumn('rate_summary', 'integer', '4', array('notnull' => true, 'default' => 0));

    // Setup total views field.
    //$this->hasColumn('view_total', 'integer', '4', array('notnull' => true, 'default' => 0));

    // Setup listener.
    $this->addListener(new Doctrine_Template_Listener_Rateable($this->_options));
  }
   
  /**
    * Add new comment.
    * @param jComment $comment
    */
  public function setRateValue($iSender, $value = 1)
  {
    // Определение голосовал ли указанный голосующий ранее.
    $iRecord = $this->getRatesQuery()->select('rateable.id')
                  ->andWhere('rateable.sender_id = ?', $iSender)
                  ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    $record = new jRateable();

    if ($iRecord)
    {
      $record->assignIdentifier($iRecord);

    }
    else {
     $record['sender_id'] = $iSender;
     $record['record_id'] = $this->_invoker->get('id');
     $record['component_id'] = $this->getComponentId($this->getInvoker()->getTable()->getComponentName());
    }

    $record['value'] = $value;
    return $record->save();
  }

  /**
    * Add new comment.
    * @param jComment $comment
    */
  public function getRateValue($iSender)
  {
    $rating = $this->getRatesQuery()->select('rateable.value')
                  ->andWhere('rateable.sender_id = ?', $iSender)
                  ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    return (! $rating ? 0 : $rating);
  }

  /**
   */
  public function increaseRateView($value = 1)
  {
    $modelName = $this->getInvoker()->getTable()->getComponentName();
    /*
    $comment->set('model_id', $this->getRateabledModel($modelName));
    $comment->set('record_id', $this->_invoker->get('id'));
    $comment->save();
    */
  }

  /**
   * Возвращает true если пользователь уже голосовал за указанный ресурс.
   * 
   * @param integer $iSender Идентификатор голосующих.
   * @return boolean
   */
  public function hasVoted($iSender)
  {
    $record = $this->getRatesQuery()->select('rateable.id')
                  ->where('rateable.component_id = ?', $this->getComponentId($this->getInvoker()->getTable()->getComponentName()))
                  ->andWhere('rateable.record_id = ?', $this->getInvoker()->get('id'))
                  ->andWhere('rateable.sender_id = ?', $iSender)
                  ->execute(array(), Doctrine_Core::HYDRATE_SINGLE_SCALAR);

    return (! $record ? false : true);
  }

  /**
   */
  public function getRateViews()
  {

  }
  
  /**
    * Return count of comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function getNbRateValues($bActiveOnly = true)
  {
    return $this->getRatesQuery($bActiveOnly)->count();
  }
     
  /**
    * Check for available comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function hasRateValues($bActiveOnly = true)
  {
    return $this->getNbRateValues($bActiveOnly) > 0;
  }
     
  /**
    * Получить активные комментарии
    * @return Doctrine_Collection записи активных комментариев
    */
  public function getRateValues()
  {
    return $this->getRatesQuery(true)->execute();
  }
     
  /**
    *
    * Получить все комментарии в том числе неактивные
    */
  public function getAllRateValues()
  {
    return $this->getRatesQuery(false)->execute();
  }
     
  /**
    * Возвращает запрос по поиску рейтинга.
    * 
    * @param boolean $bActiveOnly Флаг выборки только активных комментариев.
    */
  public function getRatesQuery($bActiveOnly = true)
  {
    // Definition for model of the type.
    $iModelClass = $this->getComponentId($this->getInvoker()->getTable()->getComponentName());
    
    // Prepare query.
    $query = Doctrine_Core::getTable('jRateableRecord')->createQuery('rateable')->select('rateable.*')
              ->where('rateable.component_id = ?', $iModelClass)
              ->andWhere('rateable.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly)
    {
      $query->andWhere('rateable.is_active = ?', true);
    }

    return $query;
  }
}
