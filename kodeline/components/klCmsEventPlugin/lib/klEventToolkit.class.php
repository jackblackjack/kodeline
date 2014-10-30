<?php
require_once(__DIR__ . DIRECTORY_SEPARATOR . 'klEventLayer.class.php');
/**
 * Kodeline cms events toolkit.
 * 
 * @package     klCmsEventPlugin
 * @category    toolkit
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class klEventToolkit
{
  /**
   * Tree of the events.
   * 
   * @var Object
   * @static
   */
  protected static $eventsInstance = null;

  /**
   * Get events tree object.
   * 
   * @return object
   */
  public static function &getEventsInstance()
  {
    if (null === self::$eventsInstance)
    {
      self::$eventsInstance = new self;
      
      // Iz-ia etoi herni kodeline ne inicializuruetsya
      //self::$eventsInstance = Doctrine_Core::getTable('klEvent');
    }

    return self::$eventsInstance;
  }

  /**
   * Return record of the event is it exists.
   * 
   * @param string $sName Name of the event.
   * @param array $arLayers Event's layers for fetch.
   * @return Doctrine_Collection
   */
  public static function eventFetch($sName, $arLayers = array())
  {
    // Prepare query for fetch event.
    $query = self::getEventsInstance()->createQuery('kle')
              ->leftJoin('kle.Subscribers')
              ->andWhere('kle.name = ?', $sName);

    // Activate filter for fetch events by layer.
    if (! is_array($arLayers)) $arLayers = array_filter(array($arLayers));
    if (count($arLayers)) $query->orWhereIn('kle.layer', $arLayers);

    // Return query result.
    return $query->execute(array(), Doctrine_Core::HYDRATE_RECORD);
  }

  /**
   */
  public static function eventSubscribersDiff(Doctrine_Record $event, $arSubscribers = array())
  {
    // Fetch subscribers list.
    $arEventSubscribers = $event->getSubscribers();

    // Prepare input subscribers list.
    $arSubs = array();
    foreach ($arSubscribers as $skey => $subscriber)
      $arSubs[sprintf('%s::%s', $subscriber['class'], $subscriber['method'])] = $skey;

    // Prepare current subscribers list.
    $arEventSubs = array();
    foreach($arEventSubscribers as $subscriber)
      $arEventSubs[sprintf('%s::%s', $subscriber['class'], $subscriber['method'])] = 1;

    // Intersect keys.
    $arDiffSubs = array_diff_key($arSubs, $arEventSubs);
    if (count($arDiffSubs))
    {
      // Create subscribers collection.
      $subscribers = new Doctrine_Collection('klEventSubscriber');

      foreach($arDiffSubs as $subscriber => $skey)
      {
        // Create a subscriber call info.
        $subscriber = new klEventSubscriber();
        $subscriber['method'] = $arSubscribers[$skey]['method'];
        $subscriber['class'] = (isset($arSubscribers[$skey]['class']) ? $arSubscribers[$skey]['class'] : null);
        $subscriber['path'] = (isset($arSubscribers[$skey]['path']) ? $arSubscribers[$skey]['path'] : null);
        if (! empty($arSubscribers[$skey]['params'])) $subscriber['path'] = serialize($arSubscribers[$skey]['params']);

        $subscribers->add($subscriber);
      }

      // Save subscribers collection.
      $subscribers->save();

      // Fetch primary keys of collection.
      $arSubscribersKeys = $subscribers->getPrimaryKeys();

      // Create event.
      $event->link('Subscribers', $arSubscribersKeys);
      $event->save();
    }
  }

  /**
   * Register event and event's subscribers.
   *
   * @param string $sName Name of event.
   * @param array $arEventConfiguration Configuration of event.
   * @return boolean True if event and subscribers have prepared successful and false if have not.
   */
  public static function eventRegister($sName, $arEventConfiguration = array())
  {
    // Define event's layer.
    $sLayer = (isset($arEventConfiguration['layer']) ? $arEventConfiguration['layer'] : 'plugin');

    // Define subscribers for event.
    $arSubscribers = (isset($arEventConfiguration['subscribers']) ? $arEventConfiguration['subscribers'] : null);

    // Fetch event data.
    $eventList = self::eventFetch($sName, $sLayer);

    // If events found.
    if ($eventList->count())
    {
      foreach($eventList as $event)
      {
        self::eventSubscribersDiff($event, $arSubscribers);
      }
    }
    // If the event is not found.
    else {
      // If subscribers is null - break process.
      if (null === $arSubscribers) return true;

      // Create subscribers collection.
      $subscribers = new Doctrine_Collection('klEventSubscriber');

      foreach($arSubscribers as $name => $arCallParams)
      {
        // Create a subscriber call info.
        $subscriber = new klEventSubscriber();
        $subscriber['method'] = $arCallParams['method'];
        $subscriber['class'] = (isset($arCallParams['class']) ? $arCallParams['class'] : null);
        $subscriber['path'] = (isset($arCallParams['path']) ? $arCallParams['path'] : null);
        if (! empty($arCallParams['params'])) $subscriber['path'] = serialize($arCallParams['params']);

        $subscribers->add($subscriber);
      }

      // Save subscribers collection.
      $subscribers->save();

      // Fetch primary keys of collection.
      $arSubscribersKeys = $subscribers->getPrimaryKeys();

      // Create event.
      $newEvent = new klEvent();
      $newEvent['name'] = $sName;
      $newEvent['layer'] = $sLayer;
      $newEvent->link('Subscribers', $arSubscribersKeys);
      self::getEventsInstance()->getTree()->createRoot($newEvent);
    }
  }
}