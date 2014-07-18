<?php
/**
 * Listener for the taggable behavior 
 * which allows add tags to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Listener_Taggable extends Doctrine_Record_Listener
{
  /**
   * Array of watchable options
   * @var array
   */
  protected $_options = array();

  /**
   * __construct
   *
   * @param string $options 
   * @return void
   */
  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
  }

  /**
  * Tags saving logic, runned after the object himself has been saved
  *
  * @param      Doctrine_Event  $event
  */
  public function postSave1(Doctrine_Event $event)
  {

      $object = $event->getInvoker();
      
      $added_tags = Taggable::get_tags($object);
      $removed_tags = array_keys(Taggable::get_removed_tags($object));
      
      // save new tags
      foreach ($added_tags as $tagname)
      {
          $tag = PluginTagTable::findOrCreateByTagName($tagname);
          $tag->save();
          $tagging = new Tagging();
          $tagging->tag_id = $tag->id;
          $tagging->taggable_id = $object->id;
          $tagging->taggable_model = get_class($object);
          $tagging->save();
      }
      
      if($removed_tags)
      {
          $q = Doctrine_Query::create()->select('t.id')
              ->from('Tag t INDEXBY t.id')
              ->whereIn('t.name', $removed_tags);
                              
          $removed_tag_ids = array_keys($q->execute(array(), Doctrine::HYDRATE_ARRAY));
          
          Doctrine::getTable('Tagging')->createQuery()
              ->delete()
              ->whereIn('tag_id', $removed_tag_ids)
              ->addWhere('taggable_id = ?', $object->id)
              ->addWhere('taggable_model = ?', get_class($object))
              ->execute();
      }

      //$tags = array_merge(Taggable::get_tags($object) , $object->getSavedTags());
      $tags = array_merge(Taggable::get_tags($object) , array());
      
      Taggable::set_saved_tags($object, $tags);
      Taggable::clear_tags($object);
      Taggable::clear_removed_tags($object);
  }

  /**
  * Delete related Taggings when this object is deleted
  *
  * @param      Doctrine_Event $event
  */
  public function preDelete1(Doctrine_Event $event)
  {
    
      $object = $event->getInvoker();
      
      Doctrine::getTable('Tagging')->createQuery()
        ->delete()
        ->addWhere('taggable_id = ?', $object->id)
        ->addWhere('taggable_model = ?', get_class($object))
        ->execute();
  }
}