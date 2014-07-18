<?php

class sfFrontendWarmCacheTask extends sfBaseTask
{
    protected function configure()
    {
        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'frontend'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'task')
        ));

        $this->namespace = 'frontend';
        $this->name = 'warm-cache';
        $this->aliases = array('wc');
        $this->briefDescription = 'Warm up frontend cache';
        $this->detailedDescription = '';
    }

    protected function execute($arguments = array(), $options = array())
    {
        sfContext::createInstance($this->configuration);
        
        $configuration = sfConfig::get('app_frontend_optimizer_plugin_warm_cache', array());

        $links = isset($configuration['links']) ? $configuration['links'] : array();
        $username = isset($configuration['auth']['username']) ? $configuration['auth']['username'] : '';
        $password = isset($configuration['auth']['password']) ? $configuration['auth']['password'] : '';
        
        $className = sfConfig::get('app_frontend_optimizer_plugin_class', 'sfFrontendOptimizer');
        $optimizer = new $className(sfConfig::get('app_frontend_optimizer_plugin_configuration', array()));

        $this->logSection('cache', sprintf('Clearing optimized files'));
        $this->getFilesystem()->remove($optimizer->getAllOptimizedFiles());

        foreach ($links as $link) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $link);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            if ($username && $password) {
                curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
            }
            curl_setopt($ch, CURLOPT_TIMEOUT, 20);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
            curl_exec($ch);
            curl_close($ch);
            $this->logSection('get', $link);
        }
    }

}
