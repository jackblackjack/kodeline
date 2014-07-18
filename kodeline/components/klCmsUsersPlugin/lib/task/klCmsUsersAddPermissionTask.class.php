<?php
class klCmsUsersAddPermissionTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    // Define task arguments
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
      new sfCommandArgument('permission', sfCommandArgument::REQUIRED, 'The permission name'),
    ));

    // Define task options
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'kl-user';
    $this->name = 'add-permission';
    $this->briefDescription = 'Adds a permission to a user';

    $this->detailedDescription = <<<EOF
The [kl-user:add-permission|INFO] task adds a permission to a user:

  [./symfony kl-user:add-permission fabien admin|INFO]

The user and the permission must exist in the database.
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    $databaseManager = new sfDatabaseManager($this->configuration);

    // Fetch user.
    $user = Doctrine_Core::getTable('klUser')->findOneByUsername($arguments['username']);
    if (!$user)
    {
      throw new sfCommandException(sprintf('User "%s" does not exist.', $arguments['username']));
    }

    $user->addPermissionByName($arguments['permission']);
    $this->logSection('kl-user', sprintf('Add permission %s to user %s', $arguments['permission'], $arguments['username']));
  }
}