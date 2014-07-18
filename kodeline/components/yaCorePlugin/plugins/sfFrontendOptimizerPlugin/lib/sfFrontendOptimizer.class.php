<?php

class sfFrontendOptimizer
{

    protected
    $configuration = array(
        'version' => '1',
        'javascript' => array(
            'enabled' => false,
            'destination' => '/build/all-:tag-:v.js',
            'driver' => 'yui',
            'driver_params' => array(),
        ),
        'stylesheet' => array(
            'enabled' => false,
            'destination' => '/build/all-:tag-:v.css',
            'driver' => 'yui',
            'driver_params' => array(),
        ),
    );

    public function __construct(array $configuration = array())
    {
        $this->configuration = sfToolkit::arrayDeepMerge($this->configuration, $configuration);
    }

    public function replaceJavascripts(sfWebResponse $response)
    {
        if (!$this->configuration['javascript']['enabled']) {
            return;
        }

        $files = array();

        $webDir = sfConfig::get('sf_web_js_dir_name', 'js');
        $ext = 'js';

        foreach ($response->getJavascripts() as $f => $options) {
            if (isset($options['condition'])) {
                continue;
            }

            $files[] = $this->computePublicPath($f, $webDir, $ext, isset($options['absolute']) ? true : false);
        }

        if (!count($files)) {
            return;
        }

        $key = md5(implode(':', $files));
        $v = $this->configuration['version'];

        $file = strtr($this->configuration['javascript']['destination'], array(':tag' => $key, ':v' => $v));

        $abloluteFile = sfConfig::get('sf_web_dir') . $file;

        if (!file_exists($abloluteFile)) {
            $driver = $this->configuration['javascript']['driver'];
            $class = 'sfFrontendMinifierJs' . ucfirst(strtr($driver, array('js' => '')));

            if (!class_exists($class)) {
                throw new sfConfigurationException(sprintf('Driver class "%s" not found', $class));
            }

            $driver = new $class($this->configuration['javascript']['driver_params']);

            if (!$driver instanceof sfFrontendMinifier) {
                throw new sfConfigurationException(sprintf('Driver "%s" must be instansof sfFrontendMinifier class', $class));
            }

            $driver->minifyAndSave($abloluteFile, $files);
        }

        foreach ($response->getJavascripts() as $f => $options) {
            if (isset($options['condition'])) {
                continue;
            }
            $response->removeJavascript($f);
        }

        $response->addJavascript($file);
    }

    public function replaceStylesheets(sfWebResponse $response)
    {
        if (!$this->configuration['stylesheet']['enabled']) {
            return;
        }

        $files = array();

        $webDir = sfConfig::get('sf_web_css_dir_name', 'css');
        $ext = 'css';

        foreach ($response->getStylesheets() as $f => $options) {
            if (isset($options['condition'])) {
                continue;
            }

            if (isset($options['rel']) && $options['rel'] != 'stylesheet') {
                continue;
            }

            $files[] = $this->computePublicPath($f, $webDir, $ext, isset($options['absolute']) ? true : false);
        }

        if (!count($files)) {
            return;
        }

        $key = md5(implode(':', $files));
        $v = $this->configuration['version'];

        $file = strtr($this->configuration['stylesheet']['destination'], array(':tag' => $key, ':v' => $v));

        $abloluteFile = sfConfig::get('sf_web_dir') . $file;

        if (!file_exists($abloluteFile)) {
            $driver = $this->configuration['stylesheet']['driver'];
            $class = 'sfFrontendMinifierCss' . ucfirst(strtr($driver, array('css' => '')));

            if (!class_exists($class)) {
                throw new sfConfigurationException(sprintf('Driver class "%s" not found', $class));
            }

            $driver = new $class($this->configuration['javascript']['driver_params']);

            if (!$driver instanceof sfFrontendMinifier) {
                throw new sfConfigurationException(sprintf('Driver "%s" must be instansof sfMAssetsMinifier class', $class));
            }

            $driver->minifyAndSave($abloluteFile, $files);
        }

        foreach ($response->getStylesheets() as $f => $options) {
            if (isset($options['condition'])) {
                continue;
            }
            $response->removeStylesheet($f);
        }

        $response->addStylesheet($file);
    }

    protected function computePublicPath($source, $dir, $ext, $absolute = false)
    {
        if (strpos($source, '://') || strpos($source, '//') === 0) {
            return $source;
        }

        $request = sfContext::getInstance()->getRequest();
        $sf_relative_url_root = $request->getRelativeUrlRoot();
        if (0 !== strpos($source, '/')) {
            $source = $sf_relative_url_root . '/' . $dir . '/' . $source;
        }

        $query_string = '';
        if (false !== $pos = strpos($source, '?')) {
            $query_string = substr($source, $pos);
            $source = substr($source, 0, $pos);
        }

        if (false === strpos(basename($source), '.')) {
            $source .= '.' . $ext;
        }

        if ($sf_relative_url_root && 0 !== strpos($source, $sf_relative_url_root)) {
            $source = $sf_relative_url_root . $source;
        }

        if ($absolute) {
            $source = 'http' . ($request->isSecure() ? 's' : '') . '://' . $request->getHost() . $source;
        }

        return $source . $query_string;
    }

    public function deleteOptimizedFiles()
    {
        foreach ($this->getAllOptimizedFiles() as $file) {
            unlink($file);
        }
    }

    public function getAllOptimizedFiles()
    {
        $stylesheetPatterm = strtr($this->configuration['stylesheet']['destination'], array(
            ':tag' => '*',
            ':v' => '*',
                ));

        $javascriptPatterm = strtr($this->configuration['javascript']['destination'], array(
            ':tag' => '*',
            ':v' => '*',
                ));

        $path = sfConfig::get('sf_web_dir');

        return $finder = sfFinder::type('file')
                ->name(basename($stylesheetPatterm))
                ->name(basename($javascriptPatterm))
                ->maxdepth(0)
                ->in(array(dirname($path . $stylesheetPatterm), dirname($path . $javascriptPatterm)));
    }

}