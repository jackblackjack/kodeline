<?php
class klCmsUsersPromoteSuperAdminTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'kl-user';
    $this->name = 'promote';
    $this->briefDescription = 'Promotes a user as a super administrator';

    $this->detailedDescription = <<<EOF
The [kl-user:promote|INFO] task promotes a user as a super administrator:

  [./symfony kl-user:promote fabien|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    // Fetch user.
    $user = Doctrine_Core::getTable('klUser')->retrieveByUsername($arguments['username']);

    if (!$user)
    {
      throw new sfException(sprintf('User identified by "%s" username does not exist or is not active.', $arguments['username']));
    }

    $user->setIsSuperAdmin(true);
    $user->save();

    $this->logSection('kl-user', sprintf('User identified by "%s" username has been promoted as super administrator', $arguments['username']));
  }
}