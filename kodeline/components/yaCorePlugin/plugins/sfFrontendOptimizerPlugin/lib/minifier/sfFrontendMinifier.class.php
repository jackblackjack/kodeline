<?php

abstract class sfFrontendMinifier
{

    protected $options;
    
    public function __construct($options)
    {
        $this->options = $options;
    }

    public function minifyAndSave($saveFile, $files)
    {
        $this->validateSaveDir($saveFile);
        $content = $this->minify($this->readContent($files));
        $this->saveContent($saveFile, $content);
    }

    protected function readContent($files)
    {
        $dir = sfConfig::get('sf_web_dir');

        $data = '';
        foreach ($files as $file) {

            if (false !== $pos = strpos($file, '?')) {
                $file = substr($file, 0, $pos);
            }

            if (!file_exists($dir . $file)) {
                throw new sfException(sprintf('File "%s" not found in "%s"', $file, $dir . $file));
            }
            $data .= file_get_contents($dir . $file);
        }

        return $data;
    }

    abstract protected function minify($content);

    protected function saveContent($saveFile, $content)
    {        
        $current_umask = umask(0000);
        file_put_contents($saveFile, $content);
        chmod($saveFile, 0666);
        umask($current_umask);
    }

    protected function validateSaveDir($saveFile)
    {
        $directory = dirname($saveFile);

        if (!is_writable($directory)) {
            throw new Exception(sprintf('Path "%s" is not writable.', $directory));
        }
    }

}

