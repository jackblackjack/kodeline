<?php
/**
 * Filter for spaceless page content.
 */
class sfFrontendSpacelessFilter extends sfFilter
{
  /**
   * {@inheritDoc}
   */
  public function execute($filterChain)
  {
    $filterChain->execute();
    
    if (sfConfig::get('app_frontend_optimizer_plugin_use_spaceless_filter', false))
    {
      $response = $this->context->getResponse();
      $response->setContent(trim(preg_replace('/>\s+</', '><', preg_replace(array('/\s{2,}/', '/[\t\r\f]/'), ' ', $response->getContent()))));
    }
  }
}