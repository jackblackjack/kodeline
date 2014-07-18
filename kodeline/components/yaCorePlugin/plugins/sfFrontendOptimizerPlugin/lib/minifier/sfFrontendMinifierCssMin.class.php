<?php

class sfFrontendMinifierCssMin extends sfFrontendMinifier
{
    protected function minify($content)
    {
        require_once dirname(__FILE__).'/../vendor/cssmin/cssmin.php';
        return cssmin::minify($content);
    }
}

