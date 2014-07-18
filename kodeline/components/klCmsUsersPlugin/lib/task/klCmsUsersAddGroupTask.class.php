<?php
class klCmsUsersAddGroupTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    // Define task arguments
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
      new sfCommandArgument('group', sfCommandArgument::REQUIRED, 'The group name'),
    ));

    // Define task options
    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'kl-user';
    $this->name = 'add-group';
    $this->briefDescription = 'Adds a group to a user';

    $this->detailedDescription = <<<EOF
The [kl-user:add-group|INFO] task adds a group to a user:

  [./symfony kl-user:add-group fabien admin|INFO]

The user and the group must exist in the database.
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

    $user->addGroupByName($arguments['group']);

    $this->logSection('kl-user', sprintf('Add group %s to user %s', $arguments['group'], $arguments['username']));
  }
}