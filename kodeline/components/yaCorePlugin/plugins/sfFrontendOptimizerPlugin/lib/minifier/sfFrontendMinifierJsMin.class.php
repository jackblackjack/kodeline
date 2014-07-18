<?php

class sfFrontendMinifierJsMin extends sfFrontendMinifier
{
    protected function minify($content)
    {
        require_once dirname(__FILE__).'/../vendor/jsmin/jsmin-1.1.1.php';
        return JSMin::minify($content);
    }
}

