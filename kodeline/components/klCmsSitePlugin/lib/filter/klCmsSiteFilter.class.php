<?php
/**
 * Site filter.
 * Detect current hostname by request and fetch data about host.
 *
 * @version $Id$
 */
class klCmsSiteFilter extends sfFilter
{
  /**
   * {@inheritDoc}
   */
  public function execute($filterChain)
  {
    if ($this->isFirstCall())
    {
      // Parse and fetch hostname.
      $hostname = preg_replace('/^www\./i','', $this->getContext()->getRequest()->getHost());

      if (! strlen($hostname))
      {
        if ('default' != $context->getModuleName()) {
          $this->getContext()->getController()->redirect('@site_not_found');
        }
      }

      if (false !== stripos($hostname, 'xn--'))
      {
        require_once(dirname(__DIR__) . '/vendor/idna_convert.class.php');
        $converter = new idna_convert(array('idn_version'=>2008));
        $hostname = $converter->decode($hostname);
      }

      // Fetch value of the type for concrete model.
      $domain = Doctrine_Core::getTable('klSite')->createQuery('kld')
                ->where('kld.name = ?', $hostname)->fetchArray();

      if ($domain)
      {
        // Save enter domain for user.
        $this->getContext()->getUser()->setAttribute('domain', $domain);

        // Set request parameter.
        $this->getContext()->getRequest()->setParameter('domain', $domain);
      }
    }

    $filterChain->execute();
  }
}
