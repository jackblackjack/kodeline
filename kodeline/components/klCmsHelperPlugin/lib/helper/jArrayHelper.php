<?php
/**
 * 
 */
class jArray
{
  /**
   * Удаляет строку в массиве массивов.
   * 
   * <example>
   * jArray::deleteRow(array(array('1' , '2' , '3' ), array('4' , '5' , '6' )), 1);
   * => array(array('1' , '2' , '3' ));
   * </example>
   * 
   * @param array $array
   * @param integer $offset
   * @return array
   */
  public static function deleteRow(&$array, $offset) {
    return array_splice($array, $offset, 1);
  }
 
  /**
   * Удаляет столбец в массиве массивов.
   * 
   * <example>
   * jArray::deleteCol(array(array('1' , '2' , '3' ), array('4' , '5' , '6' )), 1);
   * => array(array('1', '3'), array('4', '6'));
   * </example>
   * 
   * @param array $array
   * @param integer $offset
   * @return boolean
   */
  public static function deleteCol(&$array, $offset) {
    return array_walk($array, function (&$v) use ($offset) {
      array_splice($v, $offset, 1);
    });
  }

  /**
   * Обрабатывает рекурсивно массив подобно array_map.
   * 
   * @param function $callback
   * @param array $array
   * @return array
   */
  public static function array_map($callback, $array)
  {
    $_ =& $array;

    foreach ($_ as $key => $val) {
      $_[$key] = (is_array($_[$key]) ? array_map_recursive($callback, $_[$key]) : call_user_func($callback, $val));
    }

    return $_;
  }
}
