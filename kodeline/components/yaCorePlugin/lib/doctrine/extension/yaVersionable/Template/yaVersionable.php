<?php

class Doctrine_Template_yaVersionable extends Doctrine_Template_Versionable
{
  /**
   * Setup the Versionable behavior for the template.
   * Will skip 'column_aggregation' inherited tables.
   *
   * @return void
   */
  public function setUp()
  {
    if ($this->_plugin->getOption('auditLog')) {
      $this->_plugin->initialize($this->_table);
    }

    $version = $this->_options['version'];
    $name = $version['name'] . (isset($version['alias']) ? ' as ' . $version['alias'] : '');
    $this->hasColumn($name, $version['type'], $version['length'], $version['options']);

    $this->addListener(new yaDoctrineAuditLogListener($this->_plugin));
  }
}