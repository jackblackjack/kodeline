<?php
/**
 * Parameterable toolkit for managment parameters.
 * Use current table (does alter table).
 * 
 * @todo        Don't works probably.
 * 
 * @package     jDoctrineActAsBehaviorPlugin
 * @subpackage  behaviorParameterable
 * @category    toolkit
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class ParameterableToolkitTable
{
  /**
   */
  public static function createIntegerField($modelName, array $options)
  {
    var_dump($options); die;
    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    if ($record instanceof Doctrine_Record)
    {
      // Try create new parameter in the table.
      $record->createExtendedParameter(
        $options['name'],
        array(
          'type'      => $options['type'],
          'length'    => $options['length'],
          'unsigned'  => (isset($options['is_unsigned']) ? (bool) $options['is_unsigned'] : false),
          'default'   => (isset($options['default_value']) ? $options['default_value'] : null)
        ),
        (isset($options['parent_id']) && 0 < (int) $options['parent_id'] ? $options['parent_id'] : null),
        $options['title'],
        sfContext::getInstance()->getUser()->getCulture(),
        (bool) $options['is_public'],
        (isset($options['param']) && strlen($options['param']) ? $options['param'] : null)
      );
    }
  }

# Значения параметров в виде целых чисел.
jParameterableIntegerValue:
#  options: { symfony: { form: false, filter: false } }
  actAs:
    Commentable: ~
    Complaintable: ~
    Ignoreable: ~
    Rateable: ~
    Restrictable: ~
    Watchable: ~
    Sortable: ~
    Taggable: ~
    Timestampable: ~
    Watchdogable: ~
  columns:
    component_id:   { type: integer, unsigned: true, notnull: true }
    object_id:      { type: integer, unsigned: true, notnull: true }
    parameter_id:   { type: integer, unsigned: true, notnull: true }
    value:          { type: integer, unsigned: true, notnull: false }
  indexes:
    parameterable_integer_values_search_index:
      fields: [ component_id, object_id, parameter_id, value ]
  relations:
    Component:
      class: jBehaviorComponent
      local: component_id
      type: one
      foreign: id
      foreignType: many
      foreignAlias: ParameterableIntegers
      onDelete: CASCADE
      onUpdate: RESTRICT

  /**
   */
  public static function createStringField($modelName, array $options)
  {
    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    if ($record instanceof Doctrine_Record)
    {
      // Try create new parameter in the table.
      $record->createExtendedParameter(
        $options['name'],
        array(
          'type'    => $options['type'],
          'length'  => $options['length'],
          'default' => (isset($options['default_value']) ? $options['default_value'] : null)
        ),
        (isset($options['parent_id']) && 0 < (int) $options['parent_id'] ? $options['parent_id'] : null),
        $options['title'],
        sfContext::getInstance()->getUser()->getCulture(),
        (bool) $options['is_public'],
        (isset($options['param']) && strlen($options['param']) ? $options['param'] : null)
      );
    }
  }

  /**
   */
  public static function createDecimalField($modelName, array $options)
  {
    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    if ($record instanceof Doctrine_Record)
    {
      // Try create new parameter in the table.
      $record->createExtendedParameter(
        $options['name'],
        array(
          'type'    => $options['type'],
          'length'  => $options['size'],
          'size'    => $options['size'],
          'scale'   => $options['precision'],
          'default' => (isset($options['default_value']) ? $options['default_value'] : null)
        ),
        (isset($options['parent_id']) && 0 < (int) $options['parent_id'] ? $options['parent_id'] : null),
        $options['title'],
        sfContext::getInstance()->getUser()->getCulture(),
        (bool) $options['is_public'],
        (isset($options['param']) && strlen($options['param']) ? $options['param'] : null)
      );
    }
  }

  /**
   */
  public static function createTimeField($modelName)
  {
    $arStep1Values = $this->getHandler()->getValues(self::STEP1);
    $arStep2Values = $this->getHandler()->getValues(self::STEP2);

    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    // Try create new parameter in the table.
    $record->createExtendedParameter(
      $arStep2Values['name'],
      array(
        'type'    => $arStep1Values['type'],
        'length'  => $arStep2Values['length'],
        'default' => (empty($arStep2Values['default_value']) ? null : $arStep2Values['default_value'])
      ),
      (isset($arStep1Values['parent_id']) && 0 < (int) $arStep1Values['parent_id'] ? $arStep1Values['parent_id'] : null),
      $arStep1Values['title'],
      $this->getUser()->getCulture(),
      (bool) $arStep1Values['is_public'],
      (strlen($arStep1Values['param']) ? $arStep1Values['param'] : null)
    );
  }

  /**
   */
  public static function createDateField($modelName)
  {
    $arStep1Values = $this->getHandler()->getValues(self::STEP1);
    $arStep2Values = $this->getHandler()->getValues(self::STEP2);

    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    // Try create new parameter in the table.
    $record->createExtendedParameter(
      $arStep2Values['name'],
      array(
        'type'    => $arStep1Values['type'],
        'length'  => $arStep2Values['length'],
        'default' => (empty($arStep2Values['default_value']) ? null : $arStep2Values['default_value'])
      ),
      (isset($arStep1Values['parent_id']) && 0 < (int) $arStep1Values['parent_id'] ? $arStep1Values['parent_id'] : null),
      $arStep1Values['title'],
      $this->getUser()->getCulture(),
      (bool) $arStep1Values['is_public'],
      (strlen($arStep1Values['param']) ? $arStep1Values['param'] : null)
    );
  }

  /**
   */
  public static function createDatetimeField($modelName)
  {
    $arStep1Values = $this->getHandler()->getValues(self::STEP1);
    $arStep2Values = $this->getHandler()->getValues(self::STEP2);

    // Initiate new record of the table.
    $record = Doctrine::getTable($modelName)->getRecordInstance();

    // Try create new parameter in the table.
    $record->createExtendedParameter(
      $arStep2Values['name'],
      array(
        'type'    => $arStep1Values['type'],
        'length'  => $arStep2Values['length'],
        'default' => (empty($arStep2Values['default_value']) ? null : $arStep2Values['default_value'])
      ),
      (isset($arStep1Values['parent_id']) && 0 < (int) $arStep1Values['parent_id'] ? $arStep1Values['parent_id'] : null),
      $arStep1Values['title'],
      $this->getUser()->getCulture(),
      (bool) $arStep1Values['is_public'],
      (strlen($arStep1Values['param']) ? $arStep1Values['param'] : null)
    );
  }
}