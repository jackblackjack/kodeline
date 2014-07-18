<?php
/**
 * 
 */
class jPlural
{
	/** 
	 */
	public static function ru_pluralize($mValue, $singular, $plural_ver1, $plural_ver2 = null)
	{
		return (($mValue % 10 == 1 && $mValue % 100 != 11) ? 
			$singular : ($mValue % 10 >= 2 && $mValue % 10 <=4 && ($mValue % 100 < 10 || $mValue % 100 >= 20) ? $plural_ver1 : $plural_ver2));

	}
}