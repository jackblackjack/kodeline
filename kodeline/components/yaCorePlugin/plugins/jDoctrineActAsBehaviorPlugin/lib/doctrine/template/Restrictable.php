<?php
/**
 * Doctrine_Template_Restrictable object for Restrictable behavior.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Restrictable
 * @category    template
 * @author      chugarev@gmail.com
 */
class Doctrine_Template_Restrictable extends Behavior_Template
{
  /**
    * Array of restrictable options
    * 
    * @var array
    */
  protected $_options = array('default' => false, 'alias' => '', 'name' => 'is_restricted');

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
    // Definition of the column.
    $columnName = $this->_options['name'];
    
    if (! empty($this->_options['alias'])) {
      $columnName .= ' as ' . $this->_options['alias'];
    }
    
    $this->hasColumn($columnName, 'integer', 1, array('nonull' => true, 'default' => (int) $this->_options['default']));
    $this->addListener(new Doctrine_Template_Listener_Restrictable($this->_options));
  }
   
  public function addRestrictRecord(jRestrictableRecord $restrict)
  {
    $componentName = $this->getInvoker()->getTable()->getComponentName();
    
    // Сохранение данных ограничения.
    $restrict->set('component_id', $this->getComponentId($componentName));
    $restrict->set('record_id', $this->getInvoker()->get('id'));
    $restrict->save();
            
    return $this->getInvoker();
  }

  public function addRestrict($restrictor_id = null, $iDuration = null, $reasons = null, $reason_text = null)
  {
    // Определение имени компонента для работы с расширением.
    $componentName = $this->getInvoker()->getTable()->getComponentName();
    
    // Создание объекта модели запрета.
    $restrict = new jRestrictableRecord();
    $restrict->set('component_id', $this->getComponentId($componentName));
    $restrict->set('record_id', $this->getInvoker()->get('id'));

    if (0 < (int) $restrictor_id)
    {
      $restrict->set('restrictor_id', $restrictor_id);
    }

    if (0 < strlen(trim($reason_text)))
    {
      $restrict->set('reason_text', $reason_text);
    }

    if (0 < (int) $iDuration)
    {
      $this->getTimestamp('updated', $event->getInvoker()->getTable()->getConnection());
      //$restrict->set('expire_at', $reason_text);
    }

    $restrict->save();

    // Установка предопределенных причин ограничения.
    if ((is_array($reasons) && 0 < count($reasons)) || 0 < (int) $reasons)
    {
      if (! is_array($reasons)) $reasons = array($reasons);

      foreach($reasons as $reason_id)
      {
        if (0 < (int) $reason_id)
        {
          $reason = new jRestrictableRecordReasons();
          $reason->set('item_id', $restrict['id']);
          $reason->set('reason_id', $reason_id);
          $reason->save();
        }
      }
    }
            
    return $this->getInvoker();
  }
  
  /**
    * Return count of comments for record.
    * @param boolean $bActiveOnly Flag for fetch only active comments
    */
  public function getNbRestricts($bActiveOnly = true, $iRestrictor = null, $afterTimeStamp = null)
  {
    $query = $this->getCommentsQuery($bActiveOnly);

    if ($iRestrictor)
    {
      $query->andWhere('rectricti.user_id = ?', $iRestrictor);
    }

    if ($afterTimeStamp)
    {
      $query->andWhere('rectricti.created_at = ?', date('Y-m-d H:i:s', $afterTimeStamp));
    }

    return $query->count();
  }
     
  public function hasRestricts($bActiveOnly = true)
  {
    return $this->getNbRestricts($bActiveOnly) > 0;
  }
     
  public function getRestricts()
  {
    return $this->getRestrictsQuery(true)->execute();
  }
     
  public function getAllRestricts()
  {
    return $this->getRestrictsQuery(false)->execute();
  }
     
  public function getRestrictsQuery($bActiveOnly = true)
  {
    // Definition for model of the type.
    $iComponent = $this->getComponentId($this->getInvoker()->getTable()->getComponentName());
    
    // Prepare query.
    $query = Doctrine_Core::getTable('jRestrictableRecord')->createQuery('rectricti')
                ->select('rectricti.*')
                ->addSelect('rectrictr.*')->leftJoin('rectricti.Reasons as rectrictr')
                ->where('rectricti.component_id = ?', $iComponent)
                ->andWhere('rectricti.record_id = ?', $this->getInvoker()->get('id'));

    if ($bActiveOnly)
    {
      $query->andWhere('rectricti.is_active = ?', true);
    }

    return $query;
  }
}
