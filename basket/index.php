<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Корзина");
?>
<?if(!$USER->isAuthorized()) {?>
	<div class="alert alert-warning">
		Вы не вошли в свой профиль и не можете воспользоваться персональной скидкой и копить бонусы.
		<br>
		<a href="/auth/?backurl=%2Fbasket%2F">Войдите</a>, если у вас уже есть учетная запись, или <a href="/auth/registration/?register=yes&backurl=%2Fbasket%2F">зарегистрируйтесь</a>.
	</div>
<? } ?>
<?$APPLICATION->IncludeComponent(
	"bitrix:sale.basket.basket", 
	"custom_cart", 
	[
		"COLUMNS_LIST" => [
			0 => "NAME",
			1 => "DISCOUNT",
			2 => "PROPS",
			3 => "DELETE",
			4 => "DELAY",
			5 => "TYPE",
			6 => "PRICE",
			7 => "QUANTITY",
			8 => "SUM",
		],
		"OFFERS_PROPS" => [
			0 => "SIZES",
			1 => "COLOR_REF",
		],
		"PATH_TO_ORDER" => SITE_DIR."order/",
		"HIDE_COUPON" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
		"USE_PREPAYMENT" => "N",
		"SET_TITLE" => "N",
		"AJAX_MODE_CUSTOM" => "Y",
		"SHOW_MEASURE" => "Y",
		"PICTURE_WIDTH" => "100",
		"PICTURE_HEIGHT" => "100",
		"SHOW_FULL_ORDER_BUTTON" => "Y",
		"SHOW_FAST_ORDER_BUTTON" => "Y",
		"COMPONENT_TEMPLATE" => "custom_cart",
		"QUANTITY_FLOAT" => "N",
		"ACTION_VARIABLE" => "action",
		"TEMPLATE_THEME" => "site",
		"AUTO_CALCULATION" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"USE_GIFTS" => "Y",
		"GIFTS_PLACE" => "BOTTOM",
		"GIFTS_BLOCK_TITLE" => "Вы получаете подарок",
		"GIFTS_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"GIFTS_PRODUCT_PROPS_VARIABLE" => "prop",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Получить",
		"GIFTS_MESS_BTN_DETAIL" => "Подробнее",
		"GIFTS_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_CONVERT_CURRENCY" => "N",
		"GIFTS_HIDE_NOT_AVAILABLE" => "Y",
		"DEFERRED_REFRESH" => "Y",
		"USE_DYNAMIC_SCROLL" => "Y",
		"SHOW_FILTER" => "N",
		"SHOW_RESTORE" => "Y",
		"COLUMNS_LIST_EXT" => [
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "DELAY",
			4 => "SUM",
			5 => "PROPERTY_VOLUME",
			6 => "PROPERTY_ALT_NAME",
		],
		"COLUMNS_LIST_MOBILE" => [
			0 => "PREVIEW_PICTURE",
			1 => "DISCOUNT",
			2 => "DELETE",
			3 => "DELAY",
			4 => "SUM",
			5 => "PROPERTY_VOLUME",
			6 => "PROPERTY_ALT_NAME",
		],
		"TOTAL_BLOCK_DISPLAY" => [
			0 => "bottom",
		],
		"DISPLAY_MODE" => "compact",
		"PRICE_DISPLAY_MODE" => "Y",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"DISCOUNT_PERCENT_POSITION" => "bottom-right",
		"PRODUCT_BLOCKS_ORDER" => "props,sku,columns",
		"USE_PRICE_ANIMATION" => "Y",
		"LABEL_PROP" => [
			0 => "HIT",
		],
		"CORRECT_RATIO" => "Y",
		"COMPATIBLE_MODE" => "Y",
		"LABEL_PROP_MOBILE" => [
			0 => "HIT",
		],
		"LABEL_PROP_POSITION" => "top-center",
		"ADDITIONAL_PICT_PROP_2" => "-",
		"BASKET_IMAGES_SCALING" => "adaptive",
		"USE_ENHANCED_ECOMMERCE" => "Y",
		"EMPTY_BASKET_HINT_PATH" => "/catalog/",
		"ADDITIONAL_PICT_PROP_30" => "-",
		"DATA_LAYER_NAME" => "dataLayer",
		"BRAND_PROPERTY" => "PROPERTY_BRAND",
		"ADDITIONAL_PICT_PROP_35" => "-"
	],
	false
);?>

<?// Убираем привязку к конкретной секции - теперь товары будут из всех категорий
// $GLOBALS['arrFilterProp']['!IBLOCK_SECTION_ID'] = 167; // Закомментировано

// Создаем фильтры для каждого блока. Предполагаем, что в инфоблоке ID=2 есть свойство HIT с теми же ID значений.
global $arrFilterHit, $arrFilterBloggers, $arrFilterNew, $arrFilterSale, $arrFilterAll;

// Фильтры по свойствам HIT (если нужны)
$arrFilterHit = ["=PROPERTY_HIT" => 98]; // Только фильтр по свойству HIT
$arrFilterBloggers = ["=PROPERTY_HIT" => 183]; // Только фильтр по свойству HIT
$arrFilterNew = ["=PROPERTY_HIT" => 100]; // Только фильтр по свойству HIT
$arrFilterSale = ["=PROPERTY_HIT" => 101]; // Только фильтр по свойству HIT

// Фильтр для вывода любых товаров (без привязки к свойствам)
$arrFilterAll = array(); // Пустой фильтр - выводит любые товары

// ОБЩИЕ ПАРАМЕТРЫ для bitrix:catalog.section
$mainComponentParams = array(
    // === Ваши существующие параметры (все правильные) ===
    "IBLOCK_TYPE" => "catalog", "IBLOCK_ID" => "2", "SECTION_ID" => "", "SECTION_CODE" => "",
    "INCLUDE_SUBSECTIONS" => "Y", "HIDE_NOT_AVAILABLE" => "Y",
    "ELEMENT_SORT_FIELD" => "rand", "ELEMENT_SORT_ORDER" => "asc", "ELEMENT_SORT_FIELD2" => "id", "ELEMENT_SORT_ORDER2" => "desc",
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

    // === [ВОТ ЧТО НУЖНО ДОБАВИТЬ] ===
    // Эти параметры стандартный компонент проигнорирует, но шаблон их "увидит" и использует.
    
    "SHOW_DISCOUNT_TIME" => "Y",      // <--- Самый важный параметр для ТАЙМЕРА
    "SHOW_DISCOUNT_PERCENT" => "Y",   // <--- Параметр для отображения "-15%"
    "SALE_STIKER" => "SALE_TEXT",     // <--- Свойство для стикера "Акция"
    "STIKERS_PROP" => "HIT",          // <--- Свойство для стикеров "Хит" и т.д.
    "SHOW_MEASURE" => "Y",             // <--- Параметр для отображения единиц измерения (шт, мл)
	"DISPLAY_WISH_BUTTONS" => "Y" 
);
?>



<!-- Блок: Любые товары (пример) -->
<div class="custom-products-block">
    <div class="title-container"> <h2 class="title">Вам может понравиться  <a href="/catalog/" class="see-all-link">Все</a></h2></div>
    <? $APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_block_front", array_merge($mainComponentParams, ["FILTER_NAME" => "arrFilterAll"]), false); ?>
</div>

<? $APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	"", 
	[
		"AREA_FILE_SHOW" => "file",
		"PATH" => SITE_DIR."include/basket_bottom_description.php",
		"EDIT_TEMPLATE" => "include_area.php"
	],
	false,
	[
		"ACTIVE_COMPONENT" => "N"
	]
); ?>


<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>