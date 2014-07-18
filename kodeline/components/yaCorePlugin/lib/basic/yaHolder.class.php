<?php
/**
 * Represents basic holder class.
 *
 * @package    yaCorePlugin
 * @subpackage lib.basic
 * @author     pinhead
 * @version    SVN: $Id: yaHolder.class.php 2365 2010-09-28 13:37:13Z pinhead $
 */
class yaHolder implements ArrayAccess, Countable, IteratorAggregate, Serializable
{
  /**
   * Sort order constants
   *
   * @var integer
   */
  const
    ASC   = 0,
    DESC  = 1;

  /**
   * Data holder.
   *
   * @var array
   */
  private $container = array();

  /**
   * Class constructor.
   *
   * @param array $data
   */
  public function __construct($data = array())
  {
    $this->initialize($data);
  }

  /**
   * Clone.
   *
   * @return yaHolder
   */
  public function __clone()
  {
    return new self($this->container);
  }

  /**
   * Initialize.
   *
   * @param array $data
   */
  public function initialize($data = array())
  {
    $this->fromArray($data);
    $this->configure();
  }

  /**
   * Configure method for overloading.
   */
  public function configure()
  {
  }

  /**
   * Retrieves internal container array
   *
   * @return array
   */
  public function getAll()
  {
    return $this->container;
  }

  /**
   * Sets container offset value
   *
   * @param mixed $offset
   * @param mixed $value
   */
  public function set($offset, $value)
  {
    $this->container[$offset] = $value;
  }

  /**
   * Sets an array of parameters.
   *
   * If an existing parameter name matches any of the keys in the supplied
   * array, the associated value will be overridden.
   *
   * @param array $parameters  An associative array of parameters and their associated values
   */
  public function add($values)
  {
    if (null === $values)
    {
      return;
    }

    foreach ($values as $offset => $value)
    {
      $this->container[$offset] = $value;
    }
  }

  /**
   * Sets container offset value by reference
   *
   * @param mixed $offset
   * @param mixed $value
   */
  public function setByRef($offset, & $value)
  {
    $this->container[$offset] = & $value;
  }

  /**
   * Checks that container value exists
   *
   * @param mixed $offset
   *
   * @return boolean
   */
  public function has($offset)
  {
    return array_key_exists($offset, $this->container);
  }

  /**
   * Retrieves container offset value.
   *
   * @param mixed $offset
   *
   * @return mixed
   */
  public function & get($offset)
  {
    if (is_array($this->container[$offset]))
    {
      $parameter = new self($this->container[$offset]);
      return $parameter;
    }

    $parameter = & $this->container[$offset];
    return $parameter;
  }

  /**
   * @see set()
   */
  public function offsetSet($offset, $value)
  {
    $this->set($offset, $value);
  }

  /**
   * @see has()
   */
  public function offsetExists($offset)
  {
    return $this->has($offset);
  }

  /**
   * Unsets container offset value.
   *
   * @param mixed $offset
   */
  public function offsetUnset($offset)
  {
    unset($this->container[$offset]);
  }

  /**
   * @see get()
   *
   * @param mixed $offset
   */
  public function offsetGet($offset)
  {
    return $this->get($offset);
  }

  /**
   * @see count()
   *
   */
  public function count()
  {
    return count($this->container);
  }

  /**
   * Deletes element from container array
   *
   * @param mixed $offset
   */
  public function delete($offset)
  {
    array_splice($this->container, $offset, 1);
  }


  /**
   * Set container from array.
   *
   * @param array $data
   */
  public function fromArray($data)
  {
    $this->container = $data;
  }

  /**
   * Synchronize container with array.
   *
   * @param array $data
   */
  public function synWithArray($data)
  {
    foreach ($data as $offset => $value)
    {
      $this->container[$offset] = $value;
    }
  }

  /**
   * Export container data as array.
   *
   * @param boolean $deep
   *
   * @return array
   */
  public function toArray($deep = true)
  {
    $result = $this->_toArray($this->container, $deep);

    return $result;
  }

  /**
   * Convert container data to array.
   *
   * @param mixed   $data
   * @param boolean $deep
   *
   * @return array
   */
  protected function _toArray($data, $deep = true)
  {
    $result = array();

    foreach ($data as $offset => $value)
    {
      if ($deep && ($value instanceof self))
      {
        $result[$offset] = $value->toArray();
      }
      else if (is_array($value) && $deep)
      {
        $result[$offset] = $this->_toArray($value);
      }
      else
      {
        $result[$offset] = $value;
      }
    }

    return $result;
  }

  /**
   * Merge with another yaHolder object
   *
   * @param yaHolder  $holder
   */
  public function merge(yaHolder $holder)
  {
    if (! $holder instanceof yaHolder)
    {
      throw sfException('Argument is not instance of yaHolder class.');
    }

    $this->container = array_merge($this->container, $holder->getAll());
  }

  /**
   * @see set()
   */
  public function __set($name, $value)
  {
    if (! $this->has($name))
    {
      $name = yaString::underscore($name);
    }

    $this->set($name, $value);
  }

  /**
   * @see get()
   */
  public function &__get($name)
  {
    if (! $this->has($name))
    {
      $name = yaString::underscore($name);
    }

    return $this->get($name);
  }

  /**
   * @see IteratorAggregate
   */
  public function getIterator()
  {
    return new ArrayIterator($this->container);
  }

  /**
   * @see Serializable
   */
  public function serialize()
  {
    return serialize($this->container);
  }

  /**
   * @see Serializable
   */
  public function unserialize($serialized)
  {
    $this->container = unserialize($serialized);
  }

  /**
   * Checks whenever container is assotiative array
   *
   * @return boolean
   */
  public function isAssoc()
  {
    return count($this->container) && is_array($this->container) && array_diff_key($this->container, array_keys(array_keys($this->container)));
  }

  /**
   * Reorder and indexes numerically container array
   */
  public function reorder()
  {
    $this->container = array_values($this->container);
  }

  /**
   * Sort data
   *
   * @param string  $field
   * @param int     $order
   *
   * @return yaHolder
   */
  public function sort($field, $order = self::ASC)
  {
    if ($this->isAssoc())
    {
      return false;
    }

    reset($this->container);

    if (null === ($key = key($this->container)))
    {
      return false;
    }

    $callbackCode = "return strcasecmp(\$a, \$b);";
    if (is_integer($this->container[$key][$field]))
    {
      $callbackCode = "if (\$a == \$b) return 0; return \$a < \$b ? -1 : 1;";
    }

    $sortCallback = create_function('$a, $b', $callbackCode);

    $count = count($this->container);
    for ($i = 1; $i <= ($count - 1); $i++)
    {
      $element = $this->container[$i];
      $j = $i - 1;
      while ($j >= 0 && (($order == self::ASC && $sortCallback($this->container[$j][$field], $element[$field]) > 0) ||
                         ($order == self::DESC && $sortCallback($this->container[$j][$field], $element[$field]) < 0)))
      {
        $this->container[$j + 1] = $this->container[$j];
        $j--;
      }
      $this->container[$j + 1] = $element;
    }

    return $this;
  }
}