<?php
class klCmsUsersCreateTask extends sfBaseTask
{
  /**
   * @see sfTask
   */
  protected function configure()
  {
    // Define task arguments
    $this->addArguments(array(
      new sfCommandArgument('username', sfCommandArgument::REQUIRED, 'The username'),
      new sfCommandArgument('password', sfCommandArgument::REQUIRED, 'The password')
    ));

    // Define task options
    $this->addOptions(array(
      new sfCommandOption('is-super-admin', null, sfCommandOption::PARAMETER_OPTIONAL, 'Whether the user is a super admin', false),
      new sfCommandOption('application', null, sfCommandOption::PARAMETER_OPTIONAL, 'The application name', 'frontend'),
      new sfCommandOption('env', null, sfCommandOption::PARAMETER_OPTIONAL, 'The environment', 'dev')
    ));

    $this->namespace = 'kl-user';
    $this->name = 'create';
    $this->briefDescription = 'Creates a user';

    $this->detailedDescription = <<<EOF
The [kl-user:create|INFO] task creates a user:

  [./symfony kl-user:create mail@example.com password|INFO]
EOF;
  }

  /**
   * @see sfTask
   */
  protected function execute($arguments = array(), $options = array())
  {
    // Create context.
    sfContext::createInstance(yaProjectConfiguration::getApplicationConfiguration($options['application'], $options['env'], true));

    // Initialize database connection.
    $databaseManager = new sfDatabaseManager($this->configuration);

    $user = new klUser();
    $user->setUsername($arguments['username']);
    $user->setPassword($arguments['password']);
    $user->setIsActive((int) true);
    $user->setIsSuperAdmin((int) $options['is-super-admin']);
    $user->trySave();

    $this->logSection('kl-user', sprintf('Create user "%s"', $arguments['username']));
  }
}