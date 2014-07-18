<?php
/**
 * The base class that user defined filters should extend.
 *
 * Handles the setting and escaping of parameters.
 *
 * @author Alexander <iam.asm89@gmail.com>
 * @author Benjamin Eberlei <kontakt@beberlei.de>
 * @abstract
 */
abstract class SQLFilter
{
  /**
   * The component name.
   * @var Doctrine_Table
   */
  protected $table;

  /**
   * Parameters for the filter.
   * @var array
   */
  protected $parameters;

  /**
   * Constructs the SQLFilter object.
   *
   * @param EntityManager $em The EM
   */
  final public function __construct(Doctrine_Table $table)
  {
    $this->table = $table;
  }

  final public function getTable()
  {
    return $this->table;
  }

  /**
   * Sets a parameter that can be used by the filter.
   *
   * @param string $name Name of the parameter.
   * @param string $value Value of the parameter.
   * @param string $type The parameter type. If specified, the given value will be run through
   *                     the type conversion of this type. This is usually not needed for
   *                     strings and numeric types.
   *
   * @return SQLFilter The current SQL filter.
   */
  final public function setParameter($name, $value, $type = null)
  {
    /*
    if (null === $type) {
        $type = ParameterTypeInferer::inferType($value);
    }
    */
    $this->parameters[$name] = array('value' => $value, 'type' => $type);

    // Keep the parameters sorted for the hash
    ksort($this->parameters);

    // The filter collection of the EM is now dirty
    //$this->em->getFilters()->setFiltersStateDirty();

    return $this;
  }

  /**
   * Gets a parameter to use in a query.
   *
   * The function is responsible for the right output escaping to use the
   * value in a query.
   *
   * @param string $name Name of the parameter.
   *
   * @return string The SQL escaped parameter to use in a query.
   */
  final public function getParameter($name)
  {
    if (!isset($this->parameters[$name])) {
        throw new \InvalidArgumentException("Parameter '" . $name . "' does not exist.");
    }

    return $this->parameters[$name]['value'];
    //$this->_conn->quoteIdentifier($table->getColumnName($table->getIdentifier()));
    //return $this->em->getConnection()->quote($this->parameters[$name]['value'], $this->parameters[$name]['type']);
  }

  final public function setParameters($arParameters)
  {
    foreach ($arParameters as $name => $value) {
      $this->setParameter($name, $value);
    }

    return $this;
  }

  /**
   * Returns as string representation of the SQLFilter parameters (the state).
   *
   * @return string String representation of the SQLFilter.
   */
  final public function __toString()
  {
      return serialize($this->parameters);
  }

  /**
   * Gets the SQL query part to add to a query.
   *
   * @return string The constraint SQL if there is available, empty string otherwise
   */
  abstract public function addFilterConstraint(Doctrine_Query $query);
}