<?php
class klCmsUsersChangePasswordTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The user name'),
      new sfCommandArgument('password', sfCommandArgument::REQUIRED, 'The new password'),
    ));

    $this->addOptions(array(
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', null),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
    ));

    $this->namespace = 'kl-user';
    $this->name = 'change-password';
    $this->briefDescription = 'Changes the password of the user';

    $this->detailedDescription = <<<EOF
The [kl-user:change-password|INFO] task allows to change a user's password:

  [./symfony kl-user:change-password fabien changeme|INFO]
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
      throw new sfException(sprintf('User identified by "%s" username does not exist or is not active.', $arguments['username']));
    }

    $user->setPassword($arguments['password']);
    $user->save();

    $this->logSection('kl-user', sprintf('Password of user identified by "%s" has been changed', $arguments['username']));
  }
}