<?php
/**
 * Template for the joinable behavior 
 * which allows make links between components.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Joinable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Joinable extends Behavior_Template
{
  /**
   * Array of joinable options
   * 
   * @var array
   */
  protected $_options = array(
    'joinable'  =>  array('name' => 'is_joinable', 'alias' =>  null, 'disabled' => false),
    'joined'    =>  array('name' => 'has_joined', 'alias' =>  null, 'disabled' =>  false)
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
   * {@inheritDoc}
   */
  public function setTableDefinition()
  {
    // Create watchable field.
    if ( ! $this->_options['joinable']['disabled']) {
      $name = $this->_options['joinable']['name'];

      if ($this->_options['joinable']['alias']) {
        $name .= ' as ' . $this->_options['joinable']['alias'];
      }

      $this->hasColumn($name, 'integer', 1, array('notnull' => true, 'default' => 1));
    }

    // Create joined field.
    if ( ! $this->_options['joined']['disabled']) {
      $name = $this->_options['joined']['name'];

      if ($this->_options['joined']['alias']) {
        $name .= ' as ' . $this->_options['joined']['alias'];
      }

      $this->hasColumn($name, 'integer', 8, array('notnull' => true, 'default' => 0, 'unsigned' => true));
    }
    
    $this->addListener(new Doctrine_Template_Listener_Joinable($this->_options));
  }
    
  /**
    * Добавление запроса на соединение.
    * 
    * @param jComment $comment
    */
  public function requestJoinBy(Doctrine_Record $requester, $bSuccess = false)
  {
    // Определение компонента, который запросил соединение.
    $requestComponent = $requester->getTable()->getComponentName();
    $responseComponent = $this->getInvoker()->getTable()->getComponentName();

    // Define database connection.
    //$connection = sfContext::getInstance()->getDatabaseManager()->getDatabase('doctrine' )->getDoctrineConnection();
    
    try 
    { 
      //$connection->beginTransaction();   

      $join = new jJoinedRecord();
      $join->set('response_component_id', $this->fetchComponentId($responseComponent));
      $join->set('response_record_id', $this->getInvoker()->getId());
      $join->set('response_success', (int) $bSuccess);     
      $join->set('request_component_id', $this->fetchComponentId($requestComponent));
      $join->set('request_record_id', $requester->getId());
      $join->save();

      if ($bSuccess) {
        $this->getInvoker()->set('has_joined', ($this->getInvoker()->get('has_joined') + 1))->save();
      }

      //$connection->commit();

      return true;
    }
    catch(Doctrine_Exception $exception) {
      //$connection->rollback(); 
      return false;
    }
  }
  
  /**
    * Return count of comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function getJoined($bActiveOnly = true) {
    return $this->getWatchesQuery($bActiveOnly)->count();
  }

  /**
   * Возвращает список объектов, присоединенных внешними.
   * 
   * @param boolean|null $bSuccess Фильтер по флагу response_success
   * @param string|null $componentName Имя компонента для фильтра.
   * @return array
   */
  public function fetchJoinableObjects($bSuccess = null, $componentName = null, $iRecordValue = null, $hydrateMode = Doctrine_Core::HYDRATE_RECORD, $queryOnly = false)
  {
    // Выборка ID присоединенных объектов.
    $arFetchedJoins = $this->fetchJoinableObjectsIds($bSuccess, $componentName, $iRecordValue);
    
    //$collection = new Doctrine_Collection();


    if (null !== $componentName)
    {
      /**
       * Если компонент был указан 
       * - обычная выборка объектов.
       */

      // Определение ID присоединенных объектов.
      $arids = array_map(create_function('$v', 'return @array_shift(array_intersect_key($v, array_flip(array("id"))));'), $arFetchedJoins);
      
      // Выборка присоединенных объектов.
      $query = Doctrine::getTable($this->getInvoker()->getTable()->getComponentName())
                  ->createQuery('joinable')
                  ->whereIn('joinable.id', $arids);

      if ($queryOnly) return $query;

      return $query->execute(array(), $hydrateMode);
    }
  }

  /**
   * Возвращает список ID присоединенных объектов.
   * 
   * @param boolean|null $bSuccess Фильтер по флагу response_success
   * @param string|null $componentName Имя компонента для фильтра.
   * @return array
   */
  public function fetchJoinableObjectsIds($bSuccess = null, $componentName = null, $iRecordValue = null)
  {
    // Define general query.
    $query = $this->getBehaviorJoinsGeneralQuery();

    // If component is exists - set filter.
    if (null !== $componentName) {
      $query->andWhere('joinable.request_component_id = ?', $this->fetchComponentId($componentName));
    }

    // Filter by successed.
    if (null !== $bSuccess) {
     $query->andWhere('joinable.response_success = ?', (int) $bSuccess); 
    }

    // Filter by request record;
    if (null !== $iRecordValue) {
     $query->andWhere('joinable.request_record_id = ?', (int) $iRecordValue); 
    }

    // Prepare result.
    $arResult = array();

    // Fetch result query.
    $fetchResult = $query->execute(array(), Doctrine_Core::HYDRATE_ARRAY);
    $arFetchKeys = array_keys($fetchResult);
    $szFetchKeys = count($arFetchKeys);

    for($i = 0; $i < $szFetchKeys; $i++)
    {
      $arData = array();

      if (null === $bSuccess) {
        $arData = array('id' => (int) $fetchResult[$i]['response_record_id'], 'success' => (int) $fetchResult[$i]['response_success']);
      }
      else {
        $arData = (int) $fetchResult[$i]['response_record_id'];
      }

      if (null === $componentName) {
        $arResult[$fetchResult[$i]['RequestComponent']['name']][] = $arData;
      }
      else {
        $arResult[] = $arData;
      }
    }

    return $arResult;
  }
     
  /**
    * Check for available comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function hasJoined($bActiveOnly = true) {
    return $this->getNbWatchers($bActiveOnly) > 0;
  }
         
  /**
   * Retrieve general query for behavior.
   * 
   * @return Dcotrine_Query
   */
  public function getBehaviorJoinsGeneralQuery()
  {   
    // Define general query for behavior.
    $query = Doctrine::getTable('jJoinedRecord')->createQuery('joinable')
            ->innerJoin('joinable.RequestComponent')
            ->where('joinable.response_component_id = ?', $this->fetchComponentId($this->getInvoker()->getTable()->getComponentName()));

    if ($this->getInvoker()->getId()) {
      $query->andWhere('joinable.response_record_id = ?', $this->getInvoker()->getId());
    }

    return $query;
  }
     
  /**
    * Собирает запрос на получение комментариев
    * @param boolean $bActiveOnly true брать только активные комментарии
    */
  public function getBehaviorJoinsQuery($bActiveOnly = true)
  {    
    // Prepare query.
    $query = $this->getGeneralQuery()->andWhere('watchable.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly) {
      $query->andWhere('watchable.is_active = ?', true);
    }

    return $query;
  }
}
