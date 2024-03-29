<?php
/**
 * Get results directly and skip hydration. Uses PDO::FETCH_COLUMN
 *
 * @package     Doctrine
 * @subpackage  Hydrate
 * @since       1.2
 */
class Doctrine_Hydrator_yaFlatDriver extends Doctrine_Hydrator_Abstract
{
  public function hydrateResultSet($stmt)
  {
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
  }
}