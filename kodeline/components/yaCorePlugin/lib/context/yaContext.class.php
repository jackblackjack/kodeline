<?php
/**
 * Extends symfony context.
 * @see sfContext
 *
 * @package    yaCorePlugin
 * @subpackage lib.context
 * @author     pinhead
 * @version    SVN: $Id: yaContext.class.php 2372 2010-09-30 22:23:55Z pinhead $
 */
class yaContext extends sfContext
{
  /**
   * Creates a new context instance.
   *
   * @param  sfApplicationConfiguration $configuration  An sfApplicationConfiguration instance
   * @param  string                     $name           A name for this context (application name by default)
   * @param  string                     $class          The context class to use (dmContext by default)
   *
   * @return yaContext                  A yaContext instance
   */
  public static function createInstance(sfApplicationConfiguration $configuration, $name = null, $class = __CLASS__)
  {
    return parent::createInstance($configuration, $name, $class);
  }

  /**
   * {@inheritDoc}
   */
  static public function getInstance($name = null, $class = __CLASS__)
  {
    if (null === $name)
    {
      $name = self::$current;
    }

    //var_dump(self::$instances); die;
    //sfContext::createInstance($this->configuration);

    if (! isset(self::$instances[$name]))
    {
      throw new sfException(sprintf('The "%s" context does not exist.', $name));
    }

    return self::$instances[$name];
  }

  /**
    * Retrieves the mailer.
    *
    * @return sfMailer The current sfMailer implementation instance.
    */
  public function getMailer()
  {
    if (! isset($this->factories['mailer']))
    {
      Swift::registerAutoload();
      sfMailer::initialize();
      $this->factories['mailer'] = new $this->mailerConfiguration['class']($this->dispatcher, $this->mailerConfiguration);
    }

    return $this->factories['mailer'];
  }

  /**
   * Retrives guest user record.
   */
  public function fetchGuestUser()
  {
    // Checks name of the guest user.
    if (null == ($name = sfConfig::get('app_ya_core_plugin_guest_username', 'guest')))
    {
      throw new sfConfigurationException('Guest name not configured.');
    }

    return Doctrine::getTable('sfGuardUser')->createQuery()->where('username = ?', $name)->fetchOne();
  }
}
