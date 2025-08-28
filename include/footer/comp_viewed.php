<? if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?
global $arTheme;
$arViewedIDs = CNext::getViewedProducts((int)CSaleBasket::GetBasketUserID(false), SITE_ID);
if ($arViewedIDs) { ?>
	<? Bitrix\Main\Page\Frame::getInstance()->startDynamicWithID("viewed-block"); ?>
	<?
	// --- Подключаем ОБЩИЕ ПАРАМЕТРЫ, но УДАЛЯЕМ случайную сортировку ---
	$mainComponentParams = array(
		"IBLOCK_TYPE" => "catalog", "IBLOCK_ID" => "2", "SECTION_ID" => "", "SECTION_CODE" => "",
		"INCLUDE_SUBSECTIONS" => "Y", "HIDE_NOT_AVAILABLE" => "Y",
		
		// --- ИЗМЕНЕНИЕ: УБРАЛИ "rand", ВЕРНУЛИ СТАНДАРТНУЮ СОРТИРОВКУ ---
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		// -------------------------------------------------------------

		"SHOW_ALL_WO_SECTION" => "Y",
		"PAGE_ELEMENT_COUNT" => "10", "LINE_ELEMENT_COUNT" => "4",
		"PROPERTY_CODE" => array("BRAND","ALT_NAME"), "OFFERS_LIMIT" => "5", "DETAIL_URL" => "",
		"BASKET_URL" => "/basket/", "ACTION_VARIABLE" => "action", "PRODUCT_ID_VARIABLE" => "id",
		"CACHE_TYPE" => "A", "CACHE_TIME" => "3600", "CACHE_GROUPS" => "Y", "CACHE_FILTER" => "Y",
		"DISPLAY_COMPARE" => "N", "SET_TITLE" => "N", "SET_BROWSER_TITLE" => "N",
		"SET_META_KEYWORDS" => "N", "SET_META_DESCRIPTION" => "N", "SET_LAST_MODIFIED" => "N",
		"ADD_SECTIONS_CHAIN" => "N", "PRICE_CODE" => array("BASE"), "USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1", "PRICE_VAT_INCLUDE" => "Y", "CONVERT_CURRENCY" => "N",
		"SHOW_OLD_PRICE" => "Y", "DISPLAY_TOP_PAGER" => "N", "DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_SHOW_ALWAYS" => "N", 'COMPATIBLE_MODE' => 'Y',
		"SHOW_DISCOUNT_TIME" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SALE_STIKER" => "SALE_TEXT",
		"STIKERS_PROP" => "HIT",
		"SHOW_MEASURE" => "Y",
		"DISPLAY_WISH_BUTTONS" => "Y"
	);

	// --- Создаем фильтр для просмотренных товаров ---
	$GLOBALS['arrFilterViewed'] = array("ID" => $arViewedIDs);
	?>

	<!-- Используем правильную HTML-обертку -->
	<div class="custom-products-block">
		<div class="title-container">
			<h2 class="title">Ранее вы смотрели</h2>
		</div>
		<? $APPLICATION->IncludeComponent(
			"bitrix:catalog.section",
			"catalog_block_front", 
			array_merge($mainComponentParams, ["FILTER_NAME" => "arrFilterViewed"]),
			false,
			array("HIDE_ICONS" => "Y")
		); ?>
	</div>

	<? Bitrix\Main\Page\Frame::getInstance()->finishDynamicWithID("viewed-block", ""); ?>
<? } ?>