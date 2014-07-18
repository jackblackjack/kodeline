<?php
/**
 * Script for set meta tags.
 *
 * @package     russianhotels
 * @subpackage  frontend
 * @category    templates
 * @subcategory layout
 *
 * @author     chugarev
 * @version    $Id$
 */
$sDelimeter = isset($delimeter) ? $delimeter : ' - ';
$arMetas = !empty($metas) ? $metas->getRawValue() : array();
$arMetas = array_change_key_case($arMetas, CASE_LOWER);
$arGeneralMetas = $sf_response->getMetas();

foreach($arMetas as $key => $value)
{
  if (! is_array($value)) $value = array($value);
  $sf_response->addMeta($key, join($sDelimeter, $value) . (isset($arGeneralMetas[$key]) ? $sDelimeter . $arGeneralMetas[$key]  : ''));
}
$sf_response->addMeta('content-language', $sf_user->getCulture());
?>