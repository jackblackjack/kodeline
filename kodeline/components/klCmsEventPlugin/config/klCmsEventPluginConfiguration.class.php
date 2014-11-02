<?php
class klCmsEventPluginConfiguration extends klPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    // Call parent mathod.
    parent::initialize();

    // Connect to event "application.configuration.finish".
    $this->dispatcher->connect('application.configuration.finish', array($this, 'eventsApplicationRegister'));
  }

  /**
   * Register subscribers for events of application.
   * 
   * @param $event sfEvent Application event.
   */
  public function eventsApplicationRegister(sfEvent $event)
  {
    return true;
    
    /*
    Iz-ia etoi herni kodeline ne inicializuruetsya.

    // Initialize database connection.
    $databaseManager = new sfDatabaseManager($event->getSubject());

    // Define base alias.
    $sBaseAlias = klEventToolkit::getEventsInstance()->getTree()->getBaseAlias();

    // Fetch roots events.
    $arRoots = klEventToolkit::getEventsInstance()->getTree()->fetchRootsSql()
                ->addSelect('subscribers.*')->innerJoin($sBaseAlias . '.Subscribers as subscribers')
                ->execute(array(), Doctrine_Core::HYDRATE_ARRAY);

    // Connect
    foreach($arRoots as $event)
    {
      foreach($event['Subscribers'] as $subscriber)
      {
        // Define callable parameters.
        $callParam = (! empty($subscriber['class']) ? 
                      array($subscriber['class'], $subscriber['method']) : $subscriber['method']);

        // Define connect to event.
        $this->dispatcher->connect($event['name'], $callParam);
      }
    }
    */
  }
}