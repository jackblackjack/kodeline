<?php

class sfFrontendOptimizerPluginConfiguration extends sfPluginConfiguration
{

    public function initialize()
    {
        if (sfConfig::get('app_frontend_optimizer_plugin_enabled', false)) {
            $this->dispatcher->connect('context.load_factories', array($this, 'listenToContextLoadFactoriesEvent'));
        }

        sfConfig::set('sf_standard_helpers', array_merge(sfConfig::get('sf_standard_helpers', array()), array('FrontendOptimizer')));
    }

    public function listenToContextLoadFactoriesEvent(sfEvent $event)
    {
        $context = $event->getSubject();

        if (!class_exists($serviceClass = sfConfig::get('app_frontend_optimizer_plugin_class', 'sfFrontendOptimizer'))) {
            throw new sfConfigurationException(sprintf('The %s service class does not exist', $serviceClass));
        }

        $configuration = sfConfig::get('app_frontend_optimizer_plugin_configuration', array());
        $assetsOptimizer = new $serviceClass($configuration);

        $context->set('frontend_optimizer', $assetsOptimizer);
    }

}
