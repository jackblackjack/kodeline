<?php
/**
 * Extends symfony Factory configuration handler.
 *
 * @package    yaCorePlugin
 * @subpackage lib.config
 * @author     pinhead
 * @version    SVN: $Id: yaFactoryConfigHandler.class.php 2372 2010-09-30 22:23:55Z pinhead $
 */
class yaFactoryConfigHandler extends sfFactoryConfigHandler
{
  /**
   * @see sfFactoryConfigHandler
   */
  public function execute($configFiles)
  {
    $code = parent::execute($configFiles);

    if (! sfConfig::get('app_ya_core_plugin_enable_mailer', false))
    {
      $code = str_replace('Swift::registerAutoload();', '// Swift::registerAutoload();', $code);
      $code = str_replace('sfMailer::initialize();', '// sfMailer::initialize();', $code);
    }

    return $code;
  }

  /**
   * @see sfFactoryConfigHandler
   * @todo use for debug.
   */
  public function execute_for_debug($configFiles)
  {
    // parse the yaml
    $config = self::getConfiguration($configFiles);

    // init our data and includes arrays
    $includes  = array();
    $instances = array();

    // available list of factories
    $factories = array('view_cache_manager', 'logger', 'i18n', 'controller', 'request', 'response', 'routing', 'storage', 'user', 'view_cache', 'mailer');

    // let's do our fancy work
    foreach ($factories as $factory)
    {
      // see if the factory exists for this controller
      $keys = $config[$factory];

      if (!isset($keys['class']))
      {
        // missing class key
        throw new sfParseException(sprintf('Configuration file "%s" specifies category "%s" with missing class key.', $configFiles[0], $factory));
      }

      $class = $keys['class'];

      if (isset($keys['file']))
      {
        // we have a file to include
        if (!is_readable($keys['file']))
        {
          // factory file doesn't exist
          throw new sfParseException(sprintf('Configuration file "%s" specifies class "%s" with nonexistent or unreadable file "%s".', $configFiles[0], $class, $keys['file']));
        }

        // append our data
        $includes[] = sprintf("require_once('%s');", $keys['file']);
      }

      // parse parameters
      $parameters = array();
      if (isset($keys['param']))
      {
        if (!is_array($keys['param']))
        {
          throw new InvalidArgumentException(sprintf('The "param" key for the "%s" factory must be an array (in %s).', $class, $configFiles[0]));
        }

        $parameters = $keys['param'];
      }

      // append new data
      switch ($factory)
      {
        case 'controller':
          $instances['controller'][] = sprintf("  \$class = sfConfig::get('sf_factory_controller', '%s');\n   \$this->factories['controller'] = new \$class(\$this);", $class);
          break;

        case 'request':
          $parameters['no_script_name'] = sfConfig::get('sf_no_script_name');
          $instances['request'][] = sprintf("  \$class = sfConfig::get('sf_factory_request', '%s');\n   \$this->factories['request'] = new \$class(\$this->dispatcher, array(), array(), sfConfig::get('sf_factory_request_parameters', %s), sfConfig::get('sf_factory_request_attributes', array()));", $class, var_export($parameters, true));
          break;

        case 'response':
          $instances['response'][] = sprintf("  \$class = sfConfig::get('sf_factory_response', '%s');\n  \$this->factories['response'] = new \$class(\$this->dispatcher, sfConfig::get('sf_factory_response_parameters', array_merge(array('http_protocol' => isset(\$_SERVER['SERVER_PROTOCOL']) ? \$_SERVER['SERVER_PROTOCOL'] : null), %s)));", $class, var_export($parameters, true));
          // TODO: this is a bit ugly, as it only works for sfWebRequest & sfWebResponse combination. see #3397
          $instances['response'][] = sprintf("  if (\$this->factories['request'] instanceof sfWebRequest \n      && \$this->factories['response'] instanceof sfWebResponse \n      && 'HEAD' == \$this->factories['request']->getMethod())\n  {  \n    \$this->factories['response']->setHeaderOnly(true);\n  }\n");
          break;

        case 'storage':
          $defaultParameters = array();
          $defaultParameters[] = sprintf("'auto_shutdown' => false, 'session_id' => \$this->getRequest()->getParameter('%s'),", $parameters['session_name']);
          if (is_subclass_of($class, 'sfDatabaseSessionStorage'))
          {
            $defaultParameters[] = sprintf("'database' => \$this->getDatabaseManager()->getDatabase('%s'),", isset($parameters['database']) ? $parameters['database'] : 'default');
            unset($parameters['database']);
          }

          $instances['storage'][] = sprintf("  \$class = sfConfig::get('sf_factory_storage', '%s');\n  \$this->factories['storage'] = new \$class(array_merge(array(\n%s\n), sfConfig::get('sf_factory_storage_parameters', %s)));", $class, implode("\n", $defaultParameters), var_export($parameters, true));
          break;

        case 'user':
          $instances['user'][] = sprintf("  \$class = sfConfig::get('sf_factory_user', '%s');\n  \$this->factories['user'] = new \$class(\$this->dispatcher, \$this->factories['storage'], array_merge(array('auto_shutdown' => false, 'culture' => \$this->factories['request']->getParameter('sf_culture')), sfConfig::get('sf_factory_user_parameters', %s)));", $class, var_export($parameters, true));
          break;

        case 'view_cache':
          $instances['view_cache'][] = sprintf("\n  if (sfConfig::get('sf_cache'))\n  {\n".
                             "    \$class = sfConfig::get('sf_factory_view_cache', '%s');\n".
                             "    \$cache = new \$class(sfConfig::get('sf_factory_view_cache_parameters', %s));\n".
                             "    \$this->factories['viewCacheManager'] = new %s(\$this, \$cache, %s);\n".
                             "  }\n".
                             "  else\n".
                             "  {\n".
                             "    \$this->factories['viewCacheManager'] = null;\n".
                             "  }\n",
                             $class, var_export($parameters, true), $config['view_cache_manager']['class'], var_export($config['view_cache_manager']['param'], true));
          break;

        case 'i18n':
          if (isset($parameters['cache']))
          {
            $cache = sprintf("    \$cache = new %s(%s);\n", $parameters['cache']['class'], var_export($parameters['cache']['param'], true));
            unset($parameters['cache']);
          }
          else
          {
            $cache = "    \$cache = null;\n";
          }

          $instances['i18n'][] = sprintf("\n  if (sfConfig::get('sf_i18n'))\n  {\n".
                     "    \$class = sfConfig::get('sf_factory_i18n', '%s');\n".
                     "%s".
                     "    \$this->factories['i18n'] = new \$class(\$this->configuration, \$cache, %s);\n".
                     "    sfWidgetFormSchemaFormatter::setTranslationCallable(array(\$this->factories['i18n'], '__'));\n".
                     "  }\n"
                     , $class, $cache, var_export($parameters, true)
                     );
          break;

        case 'routing':
          if (isset($parameters['cache']))
          {
            $cache = sprintf("    \$cache = new %s(%s);\n", $parameters['cache']['class'], var_export($parameters['cache']['param'], true));
            unset($parameters['cache']);
          }
          else
          {
            $cache = "    \$cache = null;\n";
          }

          $instances['routing'][] = sprintf("  \$class = sfConfig::get('sf_factory_routing', '%s');\n".
                           "  %s\n".
                           "\$this->factories['routing'] = new \$class(\$this->dispatcher, \$cache, array_merge(array('auto_shutdown' => false, 'context' => \$this->factories['request']->getRequestContext()), sfConfig::get('sf_factory_routing_parameters', %s)));\n".
                           "if (\$parameters = \$this->factories['routing']->parse(\$this->factories['request']->getPathInfo()))\n".
                           "{\n".
                           "  \$this->factories['request']->addRequestParameters(\$parameters);\n".
                           "}\n",
                           $class, $cache, var_export($parameters, true)
                         );
          break;

        case 'logger':
          $loggers = '';
          if (isset($parameters['loggers']))
          {
            foreach ($parameters['loggers'] as $name => $keys)
            {
              if (isset($keys['enabled']) && !$keys['enabled'])
              {
                continue;
              }

              if (!isset($keys['class']))
              {
                // missing class key
                throw new sfParseException(sprintf('Configuration file "%s" specifies logger "%s" with missing class key.', $configFiles[0], $name));
              }

              $condition = true;
              if (isset($keys['param']['condition']))
              {
                $condition = $keys['param']['condition'];
                unset($keys['param']['condition']);
              }

              if ($condition)
              {
                // create logger instance
                $loggers .= sprintf("\n\$logger = new %s(\$this->dispatcher, array_merge(array('auto_shutdown' => false), %s));\n\$this->factories['logger']->addLogger(\$logger);\n",
                              $keys['class'],
                              isset($keys['param']) ? var_export($keys['param'], true) : 'array()'
                            );
              }
            }

            unset($parameters['loggers']);
          }

          $instances['logger'][] = sprintf(
                         "  \$class = sfConfig::get('sf_factory_logger', '%s');\n  \$this->factories['logger'] = new \$class(\$this->dispatcher, array_merge(array('auto_shutdown' => false), sfConfig::get('sf_factory_logger_parameters', %s)));\n".
                         "  %s"
                         , $class, var_export($parameters, true), $loggers);
          break;

        case 'mailer':
          $instances['mailer'][] = sprintf(
                        "require_once sfConfig::get('sf_symfony_lib_dir').'/vendor/swiftmailer/classes/Swift.php';\n".
                        "Swift::registerAutoload();\n".
                        "sfMailer::initialize();\n".
                        "\$this->setMailerConfiguration(array_merge(array('class' => sfConfig::get('sf_factory_mailer', '%s')), sfConfig::get('sf_factory_mailer_parameters', %s)));\n"
                         , $class, var_export($parameters, true));
          break;
      }
    }

    /*
    // compile data
    $retval = sprintf("<?php\n".
                      "// auto-generated by sfFactoryConfigHandler\n".
                      "// date: %s\n%s\n%s\n",
                      date('Y/m/d H:i:s'), implode("\n", $includes),
                      implode("\n", $instances));
    */

    $retval = sprintf("<?php\n"."// auto-generated by sfFactoryConfigHandler\n"."// date: %s\n", date('Y/m/d H:i:s'));

    $retval .= "\n\$timer = sfTimerManager::getTimer('Includes'); " . implode("\n", $includes) . "\n\$timer->addTime();\n";

    foreach($instances as $key => $section)
    {
      $retval .= "\n\$timer = sfTimerManager::getTimer('Instance ". $key . "'); " . implode("\n", $section) . "\n\$timer->addTime();\n";      
    }

    //$retval .= "\n\$timer = sfTimerManager::getTimer('Instances'); " . implode("\n", $instances) . "\n\$timer->addTime();\n";

    if (! sfConfig::get('app_ya_core_plugin_enable_mailer', false))
    {
      $retval = str_replace('Swift::registerAutoload();', '// Swift::registerAutoload();', $retval);
      $retval = str_replace('sfMailer::initialize();', '// sfMailer::initialize();', $retval);
    }

    return $retval;
  }
}