<?php
/**
 */
class yaConditionChain implements IteratorAggregate
{
  /** Comparison type. */
  const COMPARE_AND = 'AND';

  /** Comparison type. */
  const COMPARE_OR = 'OR';

  /** Name of chain. */  
  protected $name;

  /** List of conditions of chain. */
  protected $conditions = array();

  /** List of conjunctions for conditions in chain. */
  protected $conjunctions = array();

  public function __construct(Criteria $outer, $column, $value, $comparison = null)
  {
    $this->value = $value;
    $dotPos = strrpos($column,'.');
    if ($dotPos === false) {
      // no dot => aliased column
      $this->table = null;
      $this->column = $column;
    } else {
      $this->table = substr($column, 0, $dotPos); 
      $this->column = substr($column, $dotPos+1, strlen($column));
    }
    $this->comparison = ($comparison === null ? Criteria::EQUAL : $comparison);
    $this->init($outer);
  }

  /**
   */
  public function getName()
  {
    return $this->name;
  }

  public function setName($value)
  {
    $this->name = $value;
    return $this;
  }

  public static function fromString($sCondition)
  {
  }

  public function __toString()
  {
  }

  /**
   * Append an AND Criterion onto this Criterion's list.
   */
  public function addAnd(yaCondition $condition)
  {
    $this->conditions[] = $condition;
    $this->conjunctions[] = self::COMPARE_AND;
    return $this;
  }

  /**
   * Append an OR Criterion onto this Criterion's list.
   * @return     Criterion
   */
  public function addOr(yaCondition $condition)
  {
    $this->conditions[] = $condition;
    $this->conjunctions[] = self::COMPARE_OR;
    return $this;
  }

  /**
   */
  public function test($compareType = self::EQUAL, $rightValue = null, $bReplace = false)
  {
    
    /*
    if (null === $rightValue && null !== $this->bResult)
    {
      return $this->bResult;
    }
    */

    $rightValue = (null !== $rightValue) ? $rightValue : $this->rightValue;
    $compareType = (null === $this->compareType) ? $compareType : $this->compareType;

    if ($bReplace)
    {
      $this->compareType = $compareType;
      $this->rightValue = $rightValue;
    }

    $this->function = create_function('', "return ({$this->leftValue} $compareType $rightValue);");
    //$this->function = Closure::bind(create_function('', "return ({$this->leftValue} $compareType $rightValue);"), null);
    //$F = new ReflectionFunction('f'); $F-&amp;gt;invoke(1,2);

    $this->bResult = return $this->function();
    return $this->bResult;
  }
}