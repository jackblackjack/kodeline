<?php

function include_m_optimized_javascripts()
{
    $context = sfContext::getInstance();

    if ($context->has('frontend_optimizer')) {
        $context->get('frontend_optimizer')->replaceJavascripts($context->getResponse());
    }

    include_javascripts();
}

function include_m_optimized_stylesheets()
{
    $context = sfContext::getInstance();

    if ($context->has('frontend_optimizer')) {
        $context->get('frontend_optimizer')->replaceStylesheets($context->getResponse());
    }

    include_stylesheets();
}