<?php

/*
 * This file is part of the symfony package.
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineRecordI18nFilter implements access to the translated properties for
 * the current culture from the internationalized model.
 *
 * @package    symfony
 * @subpackage doctrine
 * @author     Jonathan H. Wage <jonwage@gmail.com>
 * @version    SVN: $Id: yaDoctrineRecordI18nFilter.class.php 2341 2010-09-25 20:15:24Z pinhead $
 */
class yaDoctrineRecordI18nFilter extends Doctrine_Record_Filter
{
  public static $fields = array();

  public function init()
  {
  }

  /**
   * Implementation of filterSet() to call set on Translation relationship to allow
   * access to I18n properties from the main object.
   *
   * @param Doctrine_Record $record
   * @param string $name Name of the property
   * @param string $value Value of the property
   * @return void
   */
  public function filterSet(Doctrine_Record $record, $fieldName, $value)
  {
    $translation = $record->get('Translation');
    $culture = myDoctrineRecord::getDefaultCulture();

    if($translation->contains($culture))
    {
      $i18n = $record->get('Translation')->get($culture);
    }
    else
    {
      $i18n = $record->get('Translation')->get($culture);
      /*
       * If translation is new
       * populate it with i18n fallback
       */
      if ($i18n->state() == Doctrine_Record::STATE_TDIRTY)
      {
        if($fallback = $record->getI18nFallBack())
        {
          $fallBackData = $fallback->toArray();
          unset($fallBackData['id'], $fallBackData['lang']);
          $i18n->fromArray($fallBackData);
        }
      }
    }

    if (!ctype_lower($fieldName) && !$i18n->contains($fieldName))
    {
      $underscoredFieldName = yaString::underscore($fieldName);
      if (strpos($underscoredFieldName, '_') !== false && $i18n->contains($underscoredFieldName))
      {
        $fieldName = $underscoredFieldName;
      }
    }

    $i18n->set($fieldName, $value);
    return $value;
  }

  /**
   * filterGet
   * defines an implementation for filtering the get() method of Doctrine_Record
   *
   * @param mixed $name                       name of the property or related component
   */
  public function filterGet(Doctrine_Record $record, $name)
  {
    // fields are mapped directly in the yaDoctrineRecord class
    // for performance reasons, but relations are mapped here.

    if ($this->getTable()->hasI18n() && $this->getTable()->getI18nTable()->hasRelation($name))
    {
      return $record->getCurrentTranslation()->get($name);
    }

    throw new Doctrine_Record_UnknownPropertyException(sprintf('Unknown record property / related component "%s" on "%s"', $name, get_class($record)));
  }
}