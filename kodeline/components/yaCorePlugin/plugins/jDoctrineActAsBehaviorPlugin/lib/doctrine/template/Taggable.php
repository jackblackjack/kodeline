<?php
/**
 * Template for the taggable behavior 
 * which allows add tags to any record.
 *
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  Commentable
 * @category    listener
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class Doctrine_Template_Taggable extends Doctrine_Template
{    
  /**
   * Array of taggable options
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
   * Set table definition for Taggable behavior
   * (borrowed and modified from Sluggable in Doctrine core)
   *
   * @return void
   */
  public function setTableDefinition()
  {
    $this->addListener(new Doctrine_Template_Listener_Taggable($this->_options));
  }
    
    /**
    * parameterHolder access methods
    */
    public static function getTagsHolder($object)
    {
        if ((!isset($object->_tags)) || ($object->_tags == null))
        {
            if (class_exists('sfNamespacedParameterHolder'))
            {
                // Symfony 1.1
                $parameter_holder = 'sfNamespacedParameterHolder';
            }
            else
            {
                // Symfony 1.0
                $parameter_holder = 'sfParameterHolder';
            }

            $object->mapValue('_tags', new $parameter_holder());
        }

        return $object->_tags;
    }

    public static function add_tag($object, $tag, $options = array())
    {
        $tag = TaggableToolkit::cleanTagName($tag, $options);

        if (strlen($tag) > 0)
        {
            self::getTagsHolder($object)->set($tag, $tag, 'tags');
        }
    }

    public static function clear_tags($object)
    {
        return self::getTagsHolder($object)->removeNamespace('tags');
    }

    public static function get_tags($object)
    {
        return self::getTagsHolder($object)->getAll('tags');
    }

    public static function set_tags($object, $tags)
    {
        self::clear_tags($object);
        self::getTagsHolder($object)->add($tags, 'tags');
    }

    public static function add_saved_tag($object, $tag)
    {
        self::getTagsHolder($object)->set($tag, $tag, 'saved_tags');
    }

    public static function clear_saved_tags($object)
    {
        return self::getTagsHolder($object)->removeNamespace('saved_tags');
    }

    public static function get_saved_tags($object)
    {
        return self::getTagsHolder($object)->getAll('saved_tags');
    }

    public static function set_saved_tags($object, $tags = array())
    {
        self::clear_saved_tags($object);
        self::getTagsHolder($object)->add($tags, 'saved_tags');
    }

    public static function add_removed_tag($object, $tag)
    {
        self::getTagsHolder($object)->set($tag, $tag, 'removed_tags');
    }

    public static function clear_removed_tags($object)
    {
        return self::getTagsHolder($object)->removeNamespace('removed_tags');
    }

    public static function get_removed_tags($object)
    {
        return self::getTagsHolder($object)->getAll('removed_tags');
    }

    public static function set_removed_tags($object, $tags)
    {
        self::clear_removed_tags($object);
        self::getTagsHolder($object)->add($tags, 'removed_tags');
    }
    
    /**
    * Adds a tag to the object. The "tagname" param can be a string or an array
    * of strings. These 3 code sequences produce an equivalent result :
    *
    * 1- $object->addTag('tag1,tag2,tag3');
    * 2- $object->addTag('tag1');
    *    $object->addTag('tag2');
    *    $object->addTag('tag3');
    * 3- $object->addTag(array('tag1','tag2','tag3'));
    *
    * @param      mixed       $tagname
    */
    public function addTag($tagname, $options = array())
    {
        $tagname = TaggableToolkit::explodeTagString($tagname);

        if (is_array($tagname))
        {
            foreach ($tagname as $tag)
            {
                $this->addTag($tag, $options);
            }
        }
        else
        {
            $removed_tags = $this->get_removed_tags($this->getInvoker()) ;

            if (isset($removed_tags[$tagname]))
            {
                unset($removed_tags[$tagname]);
                $this->set_removed_tags($this->getInvoker(), $removed_tags);
                $this->add_saved_tag($this->getInvoker(), $tagname);
            }
            else
            {
                $saved_tags = $this->getSavedTags();

                if (sfConfig::get('app_sfDoctrineActAsTaggablePlugin_triple_distinct', false))
                {
                    // the binome namespace:key must be unique
                    $triple = TaggableToolkit::extractTriple($tagname);
                    
                    if (!is_null($triple[1]) && !is_null($triple[2]))
                    {                       
                        $tags = $this->getTags(array('triple' => true, 'return' => 'tag'));
                        
                        $pattern = '/^'.$triple[1].':'.$triple[2].'=(.*)$/';
                        
                        $removed = array();
                    
                        foreach ($tags as $tag)
                        {
                            if (preg_match($pattern, $tag))
                            {
                              $removed[] = $tag;
                            }
                        }
                    
                        $this->removeTag($removed);
                    }
                }
                
                if (!isset($saved_tags[$tagname]))
                {
                    $this->add_tag($this->getInvoker(), $tagname, $options);
                }
            }
        }
    }

    /**
    * Retrieves from the database tags that have been atached to the object.
    * Once loaded, this saved tags list is cached and updated in memory.
    */
    public function getSavedTags()
    {
        $option = $this->getTagsHolder($this->getInvoker());
        
        if (!isset($option) || !$option->hasNamespace('saved_tags'))
        {
            // if record is new
            if ($this->getInvoker()->state() === Doctrine_Record::STATE_TCLEAN)
            {
                $this->set_saved_tags($this->getInvoker(), array());
                return array();
            }
            else
            {
                $q = Doctrine_Query::create()
                  ->select('t.name')
                  ->from('Tag t INDEXBY t.name, t.Tagging tg')
                  ->where('tg.taggable_id = ?', $this->getInvoker()->id)
                  ->addWhere('tg.taggable_model = ?', get_class($this->getInvoker()))
                ;

                $saved_tags = $q->execute(array(), Doctrine::HYDRATE_ARRAY);
                $tags = array();
                
                foreach ($saved_tags as $key => $infos)
                {
                    $tags[$key] = $key;
                }
                
                $this->set_saved_tags($this->getInvoker(), $tags);
                
                return (is_array($tags) ? $tags : array());
            }
        }
        else
        {
            $tags = $this->get_saved_tags($this->getInvoker()); 
            return (is_array($tags) ? $tags : array());
        }
        return array();
    }

    /**
    * Returns the list of the tags attached to the object, whatever they have
    * already been saved or not.
    *
    * @param       $object
    */
    public function getTags($options = array())
    {
        $tags = array_merge($this->get_tags($this->getInvoker()) , $this->getSavedTags());
        
        if (isset($options['is_triple']) && (true === $options['is_triple']))
        {
            $tags = array_map(array('TaggableToolkit', 'extractTriple'), $tags);
            $pattern = array('tag', 'namespace', 'key', 'value');
            
            foreach ($pattern as $key => $value)
            {
                if (isset($options[$value]))
                {
                    $tags_array = array();
                    
                    foreach ($tags as $tag)
                    {
                        if ($tag[$key] == $options[$value])
                        {
                            $tags_array[] = $tag;
                        }
                    }
                    
                    $tags = $tags_array;
                }
            }
            
            $return = (isset($options['return']) && in_array($options['return'], $pattern)) ? $options['return'] : 'all';
            
            if ('all' != $return)
            {
                $keys = array_flip($pattern);
                $tags_array = array();
                
                foreach ($tags as $tag)
                {
                    if (null != $tag[$keys[$return]])
                    {
                        $tags_array[] = $tag[$keys[$return]];
                    }
                }
                
                $tags = array_unique($tags_array);
            }
        }

        if (!isset($return) || ('all' != $return))
        {
            ksort($tags);
            
            if (isset($options['serialized']) && (true === $options['serialized']))
            {
                $tags = implode(', ', $tags);
            }
        }

        return $tags;
    }

    /**
    * Returns true if the object has a tag. If a tag ar an array of tags is
    * passed in second parameter, checks if these tags are attached to the object
    *
    * These 3 calls are equivalent :
    * 1- $object->hasTag('tag1')
    *    && $object->hasTag('tag2')
    *    && $object->hasTag('tag3');
    * 2- $object->hasTag('tag1,tag2,tag3');
    * 3- $object->hasTag(array('tag1', 'tag2', 'tag3'));
    *
    * @param      mixed       $tag
    */
    public function hasTag($tag = null)
    {
        $tag = TaggableToolkit::explodeTagString($tag);

        if (is_array($tag))
        {
            $result = true;
        
            foreach ($tag as $tagname)
            {
                $result = $result && $this->hasTag($tagname);
            }
        
            return $result;
        }
        else
        {
            $tags = $this->get_tags($this->getInvoker()) ;

            if ($tag === null)
            {
                return (count($tags) > 0) || (count($this->getSavedTags()) > 0);
            }
            elseif (is_string($tag))
            {
                $tag = TaggableToolkit::cleanTagName($tag);
                
                if (isset($tags[$tag]))
                {
                    return true;
                }
                else
                {
                    $saved_tags = $this->getSavedTags();
                    $removed_tags = $this->get_removed_tags($this->getInvoker()) ;
                    return isset($saved_tags[$tag]) && !isset($removed_tags[$tag]);
                }
            }
            else
            {
                $msg = sprintf('hasTag() does not support this type of argument : %s.', get_class($tag));
                throw new Exception($msg);
            }
        }
    }

    /**
    * Preload tags for a set of objects. It might be usefull in case you want to
    * display a long list of taggable objects with their associated tags: it
    * avoids to load tags per object, and gets all tags in a few requests.
    *
    * @param      array       $objects
    */
    public static function preloadTags(&$objects)
    {   
        // FIXME: usage of group_concat... mysql specific
        return array();
        // $searched = array();
        // 
        //         foreach ($objects as $object)
        //         {
        //             $class = get_class($object);
        //             
        //             if (!isset($searched[$class]))
        //             {
        //                 $searched[$class] = array();
        //             }
        //             
        //             $searched[$class][$object->getPrimaryKey()] = $object;
        //         }
        // 
        //         if (count($searched) > 0)
        //         {
        //             $con = Propel::getConnection();
        //             
        //             foreach ($searched as $model => $instances)
        //             {
        //                 Doctrine_Query::create()
        //                               ->select('t.taggable_id')
        //                               ->from('Tagging t')
        //                 array_map(array('sfDoctrineActAsTaggable', 'set_saved_tags'),
        //                           $instances,
        //                           array_fill(0, count($instances), array()));
        //                 $keys = array_keys($instances);
        //                 
        //                 $query = 'SELECT %s as id,
        //                                  GROUP_CONCAT(%s) as tags
        //                           FROM %s, %s
        //                           WHERE %s IN (%s)
        //                           AND %s=?
        //                           AND %s=%s
        //                           GROUP BY %s';
        //                 
        //                 $query = sprintf($query,
        //                                  TaggingPeer::TAGGABLE_ID,
        //                                  TagPeer::NAME,
        //                                  TaggingPeer::TABLE_NAME,
        //                                  TagPeer::TABLE_NAME,
        //                                  TaggingPeer::TAGGABLE_ID,
        //                                  implode($keys, ','),
        //                                  TaggingPeer::TAGGABLE_MODEL,
        //                                  TaggingPeer::TAG_ID,
        //                                  TagPeer::ID,
        //                                  TaggingPeer::TAGGABLE_ID);
        //                 $stmt = $con->prepareStatement($query);
        //                 $stmt->setString(1, $model);
        //                 $rs = $stmt->executeQuery();
        //                 
        //                 while ($rs->next())
        //                 {
        //                     $object = $instances[$rs->getInt('id')];
        //                     $object_tags = explode(',', $rs->getString('tags'));
        //                     $tags = array();
        //                     
        //                     foreach ($object_tags as $tag)
        //                     {
        //                         $tags[$tag] = $tag;
        //                     }
        //                     
        //                     self::set_saved_tags($this->getInvoker(), $object, $tags);
        //                 }
        //             }
        //         }
    }

    /**
    * Removes all the tags associated to the object.
    *
    * @param       $object
    */
    public function removeAllTags()
    {
        $saved_tags = $this->getSavedTags();
        
        $this->set_saved_tags($this->getInvoker(), array());
        $this->set_tags($this->getInvoker(), array());        
        $this->set_removed_tags($this->getInvoker(), array_merge($this->get_removed_tags($this->getInvoker()) , $saved_tags));
    }

    /**
    * Removes a tag or a set of tags from the object. The
    * parameter might be an array of tags or a comma-separated string.
    *
    * @param      mixed       $tagname
    */
    public function removeTag($tagname)
    {
        $tagname = TaggableToolkit::explodeTagString($tagname);
        
        if (is_array($tagname))
        {
            foreach ($tagname as $tag)
            {
                $this->removeTag($tag);
            }
        }
        else
        {
            $tagname = TaggableToolkit::cleanTagName($tagname);
            
            $tags = $this->get_tags($this->getInvoker()) ;
            $saved_tags = $this->getSavedTags();
        
            if (isset($tags[$tagname]))
            {
              unset($tags[$tagname]);
              $this->set_tags($this->getInvoker(), $tags);
            }
        
            if (isset($saved_tags[$tagname]))
            {
                unset($saved_tags[$tagname]);
                $this->set_saved_tags($this->getInvoker(), $saved_tags);
                $this->add_removed_tag($this->getInvoker(), $tagname);
            }
        }
    }

    /**
    * Replaces a tag with an other one. If the third optionnal parameter is not
    * passed, the second tag will simply be removed
    *
    * @param       $object
    * @param      String      $tagname
    * @param      String      $replacement
    */
    public function replaceTag($tagname, $replacement = null)
    {
        if (($replacement != $tagname) && ($tagname != null))
        {
            $this->removeTag($tagname);
            
            if ($replacement != null)
            {
                $this->addTag($replacement);
            }
        }
    }

    /**
    * Sets the tags of an object. As usual, the second parameter might be an
    * array of tags or a comma-separated string.
    *
    * @param       $object
    * @param      mixed       $tagname
    */
    public function setTags($tagname)
    {
        $this->removeAllTags();
        $this->addTag($tagname);
    }
}