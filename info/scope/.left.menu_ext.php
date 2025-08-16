<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

$aMenuLinksExt = $APPLICATION->IncludeComponent(
	"wl:menu.sections.from.property",
	"",
	[
		'IBLOCK_ID' => GOODS_IBLOCK_ID,
		'PROPERTY_CODE' => 'SCOPE',
		'URL_TEMPLATE' => '/info/scope/',
		'CACHE_TIME' => 3600
	]
);
if (empty($aMenuLinks)) {
	$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
}
