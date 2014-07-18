<?php
/**
 * 
 */
class jMath
{
  /**
   *
   */
  static function getMedian($arNumbers = array())
  {
    if (! is_array($arNumbers)) $arNumbers = func_get_args();
    rsort($arNumbers);
    $mid = (sizeof($arNumbers) / 2);
    
    return ($mid % 2 != 0) ? $arNumbers{$mid-1} : (($arNumbers{$mid-1}) + $arNumbers{$mid}) / 2;
  }
  
  static function getAverage($arNumbers = array())
  {
    if (! is_array($arNumbers)) $arNumbers = func_get_args();
    
    $sum = array_sum($arNumbers);
    $sz = sizeof($arNumbers);
			
    return (0 < $sz) ? ($sum / $sz) : false;
  }
}
