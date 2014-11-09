<?php
/**
 * abtract class BaseFilterBuilderQueryHelper
 *
 * @abstract
 * @package     yaFlexibleShopPlugin
 * @subpackage  FxShopFilter
 * @category    helper
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
abstract class BaseFilterBuilderQueryHelper
{
  public static function buildQuery(Doctrine_Query $query, Doctrine_Collection $rules)
  {
    die("build");
/*
    foreach($this->filter['Rules'] as $rule) {
      $fetchQuery = FilterBuilderQueryHelper::buildWhereQuery($fetchQuery, $rule['Parameter'], $rule, $rule['Parameter']['Component']);
    }
*/

  }

  /**
   * Build query by parameter definition.
   */
  public static function buildWhereQuery(Doctrine_Query $query, $parameter, $rule, $component = null)
  {
    //if (null === $component)

    $componentAlias = $component['name'];

    // Prepare query.
    $query = $query->from($componentAlias)->addSelect($componentAlias . '.*');

    // Operator "<"
    if (strlen($rule['value_less'])) {
      $query = self::getWhereLess($query, $parameter, $componentAlias, $rule['value_less'], $rule['is_and']);
    }

    // Operator "<="
    if (strlen($rule['value_max'])) {
      $query = self::getWhereLessOrEqual($query, $parameter, $componentAlias, $rule['value_max'], $rule['is_and']);
    }

    // Operator ">"
    if (strlen($rule['value_greater'])) {
      $query = self::getWhereGreater($query, $parameter, $componentAlias, $rule['value_greater'], $rule['is_and']);
    }

    // Operator ">="
    if (strlen($rule['value_min'])) {
      $query = self::getWhereGreaterOrEqual($query, $parameter, $componentAlias, $rule['value_min'], $rule['is_and']);
    }

    // Operator "=="
    if (strlen($rule['value_eq'])) {
      $query = self::getWhereEqual($query, $parameter, $componentAlias, $rule['value_eq'], $rule['is_and']);
    }

    // Operator "!="
    if (strlen($rule['value_ne'])) {
      $query = self::getWhereNotEqual($query, $parameter, $componentAlias, $rule['value_ne'], $rule['is_and']);
    }

    return $query;
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereLess($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    if ($bIsAnd) {
      return $query->andWhere($componentAlias . '.' . $parameter['name'] . ' < ?', $value);
    }

    return $query->orWhere($componentAlias . '.' . $parameter['name'] . ' < ?', $value); 
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereLessOrEqual($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    if ($bIsAnd) {
      return $query->andWhere($componentAlias . '.' . $parameter['name'] . ' <= ?', $value);
    }

    return $query->orWhere($componentAlias . '.' . $parameter['name'] . ' <= ?', $value); 
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereGreater($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    if ($bIsAnd) {
      return $query->andWhere($componentAlias . '.' . $parameter['name'] . ' > ?', $value);
    }

    return $query->orWhere($componentAlias . '.' . $parameter['name'] . ' > ?', $value); 
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereGreaterOrEqual($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    if ($bIsAnd) {
      return $query->andWhere($componentAlias . '.' . $parameter['name'] . ' >= ?', $value);
    }

    return $query->orWhere($componentAlias . '.' . $parameter['name'] . ' >= ?', $value); 
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereEqual($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    // Define values alias.
    $valueAlias = jString::getRandomString();

    // Define rules.
    $valueType = ('liblink' == $parameter['type'] ? 'integer' : $parameter['type']);

    // Define component.
    $componentInnerJoin = $componentAlias . '.' . sfInflector::camelize($valueType) . 'Values as ' . $valueAlias . ' WITH ' . $valueAlias . '.component_id = ' . $parameter['component_id'] . ' AND ' . $valueAlias . '.parameter_id = ' . $parameter['id'];

    // Join component (todo: LeftJoin?).
    $query->innerJoin($componentInnerJoin);

    if ($bIsAnd)
    {
      if ('string' === $parameter['type'])
      {
        $query->withI18n(sfContext::getInstance()->getUser()->getCulture());
        $query->andWhere($valueAlias . '.value = ?', $value);
      }
      else
      {
        $query->andWhere($valueAlias . '.value = ?', $value);
      }
    }
    else {
      if ('string' === $parameter['type'])
      {
        $query->innerJoin($componentAlias . '.Translation as psvst WITH ' . $valueAlias . '.lang = ?', sfContext::getInstance()->getUser()->getCulture());
        $query->orWhere($valueAlias . '.value = ?', $value);  
      }
      else
      {
        $query->orWhere($valueAlias . '.value = ?', $value);
      }
    }

    return $query;

//    string
//    jParameterableStringValue

//SELECT * FROM objects t
//  INNER JOIN int_property t2 ON t.id = t2.object_id AND t2.prop_id = 101 /* это цена */
//  INNER JOIN string_property t3 ON t.id = t3.object_id AND t3.prop_id = 102 /* тип масла */
//  INNER JOIN string_property t4 ON t.id = t4.object_id AND t4.prop_id = 102 /* тип масла */
//  WHERE t2.value < 30
//       AND (t3.value = 'Castrol' OR t4.value = 'Mobile')
//====
//SELECT * FROM oil
//WHERE price < 30
//     AND (type = 'Castrol' OR type = 'Mobile')
  }

  /**
   * Add query where by parameter.
   * 
   * @param Doctrine_Query $query
   * @param array|Doctrine_Record $parameter
   * @param string $componentAlias
   */
  private static function getWhereNotEqual($query, $parameter, $componentAlias, $value, $bIsAnd = true)
  {
    if ($bIsAnd) {
      return $query->andWhere($componentAlias . '.' . $parameter['name'] . ' != ?', $value);
    }

    return $query->orWhere($componentAlias . '.' . $parameter['name'] . ' != ?', $value);
  }
}