<?php
/**
 */
class yaDoctrineCondition extends yaCondition
{
  public function __construct()
  {
  }

  public static function fromString($sCondition)
  {
  }

  public function __toString()
  {
  }

  /**
   */
  public function getLeftValue()
  {
    return $this->leftValue;
  }

  /**
   */
  public function getRightValue()
  {
    return $this->rightValue;
  }

  /**
   */
  public function getCompareType()
  {
    return $this->compareType;
  }

  /**
   */
  public function setCompareType($type)
  {
    switch($type)
    {
      case self::EQUAL:
      case self::NOT_EQUAL:
      case self::ALT_NOT_EQUAL:
      case self::GREATER_THAN:
      case self::LESS_THAN:
      case self::GREATER_EQUAL:
      case self::LESS_EQUAL:
      case self::IDENTICAL:
      case self::NOT_IDENTICAL:
        $this->compareType = $type;
      break;

      default:
        throw new sfException(sprintf('Compare type "%s" is illegal!', $type));
      break;
    }

    return $this;
  }

  /**
   */
  public function setLeftValue($value)
  {
    $this->leftValue = $value;
    return $this;
  }

  /**
   */
  public function setRightValue($value)
  {
    $this->rightValue = $value;
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