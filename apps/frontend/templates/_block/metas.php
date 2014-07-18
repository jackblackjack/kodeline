<?php
/**
 * Script to sets values for meta tags.
 *
 * @package     frontend
 * @subpackage  templates
 * @category    utility
 * @author      chugarev
 * @version     $Id$
 */
$sDelimeter = isset($delimeter) ? $delimeter : ' - ';
$arMetas = (!empty($metas) ? $metas->getRawValue() : array());
$arMetas = array_change_key_case($arMetas, CASE_LOWER);
$arGeneralMetas = $sf_response->getMetas();

$arMetaKeys = array_keys($arMetas);
$szMetaKeys = count($arMetaKeys);

for($i = 0; $i < $szMetaKeys; $i++)
{
  if (! is_array($arMetas[$arMetaKeys[$i]])) $arMetas[$arMetaKeys[$i]] = array($arMetas[$arMetaKeys[$i]]); 
  if ('title' !== $arMetaKeys[$i]) $sDelimeter = '. ';
  $sf_response->addMeta($arMetaKeys[$i], sprintf('%s%s%s', join($sDelimeter, $arMetas[$arMetaKeys[$i]]), $sDelimeter, (isset($arGeneralMetas[$arMetaKeys[$i]]) ? $arGeneralMetas[$arMetaKeys[$i]]  : null)));
}
$sf_response->addMeta('content-language', $sf_user->getCulture());