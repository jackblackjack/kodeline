<?php

abstract class sfFrontendMinifierAbstractYui extends sfFrontendMinifier
{

    public function __construct($options)
    {
        $options = array_merge(array(
            'jar_location' => dirname(__FILE__) . '/../vendor/yuicompressor/yuicompressor-2.4.2.jar',
            'charset' => 'utf-8',
            'line_break' => '5000',
            'tempPrefix' => 'm_asset_yui',
                ), $options);

        parent::__construct($options);
    }

    protected function minify($content)
    {
        $tempFile = $this->createTempFile($content);

        $command = 'java -jar '
                . $this->options['jar_location'] . ' '
                . '--type ' . $this->getType() . ' '
                . $this->buildCommandOptions() . ' '
                . escapeshellarg($tempFile)
        ;

        exec($command, $output, $return);

        $this->deleteTempFile($tempFile);
        
        if ($return !== 0) {
            throw new sfException('YUI Compressor returned error', $return);
        }

        return implode($output, "\n");
    }

    abstract protected function getType();

    protected function createTempFile($content)
    {
        $current_umask = umask(0000);
        
        $options = $this->options;

        $tempFile = tempnam(
                (isset($options['tempDir']) ? $options['tempDir'] : ''), (isset($options['tempPrefix']) ? $options['tempPrefix'] : '')
        );

        if (!$tempFile) {
            throw new sfException('Temporary file could not be created');
        }

        $result = file_put_contents($tempFile, $content);
        
        if ($result === false) {
            throw new sfException('Writing to temporary file failed');
        }
        
        chmod($tempFile, 0666);
        umask($current_umask);

        return $tempFile;
    }

    protected function deleteTempFile($tempFile)
    {
        if (is_file($tempFile)) {
            return unlink($tempFile);
        }

        return false;
    }

    protected function buildCommandOptions()
    {
        $options = $this->options;

        $commandOptions = '';

        if (isset($options['charset']) && $options['charset']) {
            $commandOptions .= ' --charset ' . $options['charset'];
        }

        if (isset($options['line_break']) && $options['line_break']) {
            $commandOptions .= ' --line-break ' . $options['line_break'];
        }

        return $commandOptions ? substr($commandOptions, 1) : '';
    }

}

