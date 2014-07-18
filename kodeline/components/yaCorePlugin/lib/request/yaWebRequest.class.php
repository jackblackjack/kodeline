<?php
/**
 * Extends symfony sfWebRequest.
 *
 * @package     yaCorePlugin
 * @subpackage  lib.request
 * @author      chuga
 * @version     $Id$
 */
class yaWebRequest extends sfWebRequest
{
  /**
   * Возвращает true если указанный параметр имеет указанное значение.
   * 
   * @param string $name Имя проверяемого параметра
   * @param mixed $value Требуемое значение проверяемого параметра
   * @return boolean
   */
  public function hasParameterValue($name, $value)
  {
    if (! $this->parameterHolder->has($name))
    {
      return (null == $value);
    }
    return ($value == $this->parameterHolder->get($name));
  }

  /**
   * Возвращает true если указанный параметр равен одному из указанных значений.
   * 
   * @param string $name Имя проверяемого параметра
   * @param array $values Список требуемых значений проверяемого параметра
   * @return boolean
   */
  public function hasParameterValues($name, array $values)
  {
    foreach($values as $value)
    {
      if ($this->hasParameterValue($name, $value))
      {
        return true;
      }
    }

    return false;
  }

  /**
   * Возвращает список параметров, имена которых не совпадают со списком указанных.
   * 
   * @param array $exclude Список исключаемых имен параметров.
   * @param string $type Тип фильтруемых параметров.
   * @return boolean
   */
  public function getFilteredParameters(array $exclude, $type = null)
  {
    // Определение типа фильтруемых параметров.
    $type = ucfirst(strtolower((('POST' == strtoupper($type)) ? 'POST' : (('GET' == strtoupper($type)) ? 'GET' : 'REQUEST'))));

    // Выборка списка параметров.
    $arParameters = array();
    $methodName = 'get' . $type . 'Parameters';
    $method = new sfCallable(array($this, $methodName));
    if (is_callable($method->getCallable())) $arParameters = $method->call();

    foreach($exclude as $name) unset($arParameters[$name]);
    return $arParameters;
  }

  /**
   */
  public function getMergedParameters($type, $parameters, $excludes = array())
  {
    // Определение типа фильтруемых параметров.
    $type = ucfirst(strtolower((('POST' == strtoupper($type)) ? 'POST' : (('GET' == strtoupper($type)) ? 'GET' : 'REQUEST'))));

    // Выборка списка параметров.
    $arParameters = array();
    $methodName = 'get' . $type . 'Parameters';

    $method = new sfCallable(array($this, $methodName));
    if (is_callable($method->getCallable())) $arParameters = $method->call();

    return array_merge($arParameters, array_intersect_key($parameters, array_fill_keys(array_filter(array_keys($parameters), function ($v) use ($excludes) { return !in_array($v, $excludes); }), 1)));
  } 
}