<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
	die(); ?>
<? $this->setFrameMode(true); ?>
<?// intro text ?>
<div class="text_before_items">
	<? $APPLICATION->IncludeComponent(
		"bitrix:main.include",
		"",
		array(
			"AREA_FILE_SHOW" => "page",
			"AREA_FILE_SUFFIX" => "inc",
			"EDIT_TEMPLATE" => ""
		)
	); ?>
</div>
<?
$arItemFilter = CNext::GetIBlockAllElementsFilter($arParams);
$itemsCnt = CNextCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), $arItemFilter, array());

// rss
if ($arParams['USE_RSS'] !== 'N') {
	CNext::ShowRSSIcon($arResult['FOLDER'] . $arResult['URL_TEMPLATES']['rss']);
} ?>

<? if ($arParams['USE_FILTER'] == "Y") { ?>
	<? global $arTheme;
	?>
	<? $APPLICATION->IncludeComponent(
		"bitrix:catalog.smart.filter",
		"countries",
		array(
			"IBLOCK_TYPE" => $arParams['IBLOCK_TYPE'],
			"IBLOCK_ID" => $arParams['IBLOCK_ID'],
			"SECTION_ID" => '',
			"FILTER_NAME" => $arParams['FILTER_NAME'],
			"CACHE_TYPE" => $arParams['CACHE_TYPE'],
			"CACHE_TIME" => $arParams['CACHE_TIME'],
			"CACHE_GROUPS" => $arParams['CACHE_GROUPS'],
			"SAVE_IN_SESSION" => "N",
			"XML_EXPORT" => "Y",
			"SECTION_TITLE" => "NAME",
			"SECTION_DESCRIPTION" => "DESCRIPTION",
			"SHOW_HINTS" => "N",
			'DISPLAY_ELEMENT_COUNT' => "Y",
			"INSTANT_RELOAD" => "Y",
			"VIEW_MODE" => strtolower($arTheme["FILTER_VIEW"]["VALUE"]),
			"SEF_MODE" => "N",
			"SEF_RULE" => $arResult["FOLDER"] . $arResult["URL_TEMPLATES"]["smart_filter"],
			"SMART_FILTER_PATH" => $arResult["VARIABLES"]["SMART_FILTER_PATH"],
			"HIDE_NOT_AVAILABLE" => "N",
			"PROPERTY_CODE" => ['COUNTRY']
		),
		$component
	); ?>
<? } ?>
<? if (!$itemsCnt): ?>
	<div class="alert alert-warning"><?= GetMessage("SECTION_EMPTY") ?></div>
<? else: ?>
	<?// section elements ?>
	<? global $arTheme; ?>
	<? $sViewElementsTemplate = ($arParams["SECTION_ELEMENTS_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["STAFF_PAGE"]["VALUE"] : $arParams["SECTION_ELEMENTS_TYPE_VIEW"]); ?>
	<? @include_once('page_blocks/' . $sViewElementsTemplate . '.php'); ?>
<? endif; ?>