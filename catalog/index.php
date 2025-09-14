<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог косметики");

if(isset($_REQUEST['q'])) {
	$_REQUEST['q'] = '"' . $_REQUEST['q'] . '"';
	$_REQUEST['q'] = str_replace('""', '"', $_REQUEST['q']); //replace double quotes
}

// контроль кеша, состояния на складах
$rsStoreProduct = \Bitrix\Catalog\StoreProductTable::getList(array(
    'order' => ['PRODUCT_ID' => 'asc', 'STORE_ID' => 'asc'],
    'filter' => ['STORE.ACTIVE' => 'Y'],
	'select' => ['STORE_ID', 'AMOUNT', 'QUANTITY_RESERVED', 'PRODUCT_ID']
));
$arProductInStores = [];
while($arStoreProduct=$rsStoreProduct->fetch()) {
    $data = $arStoreProduct['STORE_ID'] . ',' . $arStoreProduct['AMOUNT'] . ',' . $arStoreProduct['QUANTITY_RESERVED'] . ',';
    if (!isset($arProductInStores[$arStoreProduct['PRODUCT_ID']]))
        $arProductInStores[$arStoreProduct['PRODUCT_ID']] = $data;
    else
        $arProductInStores[$arStoreProduct['PRODUCT_ID']] .= $data;
}
?>

<div class="visible-xs mobile-search">
	<?if(!isset($_REQUEST['q'])) {?>
		<? $APPLICATION->IncludeComponent(
			"bitrix:main.include",
			"",
			array(
				"AREA_FILE_SHOW" => "file",
				"PATH" => SITE_DIR . "include/top_page/search.title.catalog.php",
				"EDIT_TEMPLATE" => "include_area.php"
			)
		); ?>
	<? } ?>
</div>

<?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	"main", 
	array(
		"ACTION_VARIABLE" => "action",
		"ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE" => "",
		"ADDITIONAL_GALLERY_PROPERTY_CODE" => "-",
		"ADDITIONAL_GALLERY_TYPE" => "BIG",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"AJAX_FILTER_CATALOG" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"ALSO_BUY_ELEMENT_COUNT" => "5",
		"ALSO_BUY_MIN_BUYES" => "2",
		"ASK_FORM_ID" => "2",
		"BASKET_URL" => "/basket/",
		"BIG_DATA_RCM_TYPE" => "similar_sell",
		"BLOCK_ADDITIONAL_GALLERY_NAME" => "",
		"BLOCK_DOCS_NAME" => "",
		"BLOCK_SERVICES_NAME" => "",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "Y",
		"CACHE_TIME" => "3600",
		"CACHE_TYPE" => "A",
		"CHEAPER_FORM_NAME" => "",
		"COMMON_ADD_TO_BASKET_ACTION" => "ADD",
		"COMMON_SHOW_CLOSE_POPUP" => "N",
		"COMPARE_ELEMENT_SORT_FIELD" => "shows",
		"COMPARE_ELEMENT_SORT_ORDER" => "asc",
		"DETAIL_BRAND_PROP" => array(
			"PREVIEW_PICTURE",
			"DETAIL_PICTURE",
		),
		"COMPARE_FIELD_CODE" => array(
			0 => "NAME",
			1 => "TAGS",
			2 => "SORT",
			3 => "PREVIEW_PICTURE",
			4 => "",
		),
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"COMPARE_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "",
		),
		"COMPARE_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTICLE",
			1 => "VOLUME",
			2 => "SIZES",
			3 => "COLOR_REF",
			4 => "",
		),
		"COMPARE_POSITION" => "top left",
		"COMPARE_POSITION_FIXED" => "Y",
		"COMPARE_PROPERTY_CODE" => array(
			0 => "BRAND_REF",
			1 => "CML2_ARTICLE",
			2 => "VOLUME",
			3 => "BRAND",
			4 => "CML2_BASE_UNIT",
			5 => "CML2_MANUFACTURER",
			6 => "",
		),
		"COMPATIBLE_MODE" => "Y",
		"COMPONENT_TEMPLATE" => "main",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"DEFAULT_COUNT" => "1",
		"DEFAULT_LIST_TEMPLATE" => "block",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "Y",
		"DETAIL_ADD_TO_BASKET_ACTION" => array(
			0 => "BUY",
		),
		"DETAIL_ADD_TO_BASKET_ACTION_PRIMARY" => array(
			0 => "BUY",
		),
		"DETAIL_ASSOCIATED_TITLE" => "Похожие товары",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"DETAIL_BRAND_USE" => "N",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_DETAIL_PICTURE_MODE" => array(
			0 => "POPUP",
			1 => "MAGNIFIER",
		),
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"DETAIL_DOCS_PROP" => "INSTRUCTIONS",
		"DETAIL_EXPANDABLES_TITLE" => "С этим товаром рекомендуем",
		"DETAIL_IMAGE_RESOLUTION" => "16by9",
		"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => "",
		"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => "",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "DETAIL_PAGE_URL",
			4 => "PREVIEW_TEXT",
		),
		"DETAIL_OFFERS_LIMIT" => "0",
		"DETAIL_OFFERS_PROPERTY_CODE" => array(
			0 => "FRAROMA",
			1 => "ARTICLE",
			2 => "SPORT",
			3 => "VLAGOOTVOD",
			4 => "AGE",
			5 => "RUKAV",
			6 => "KAPUSHON",
			7 => "FRCOLLECTION",
			8 => "FRLINE",
			9 => "FRFITIL",
			10 => "VOLUME",
			11 => "FRMADEIN",
			12 => "FRELITE",
			13 => "SIZES",
			14 => "TALL",
			15 => "FRFAMILY",
			16 => "FRSOSTAVCANDLE",
			17 => "FRTYPE",
			18 => "FRFORM",
			19 => "COLOR_REF",
			20 => "",
		),
		"DETAIL_PICTURE_MODE" => "MAGNIFIER",
		"DETAIL_PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
		"DETAIL_PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "COUNT_IN_PACKAGE",
			2 => "VOLUME",
			3 => "CONTENTS",
			4 => "TIP_KOJI",
			5 => "BRAND",
			6 => "",
		),
		"DETAIL_SET_CANONICAL_URL" => "Y",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "Y",
		"DETAIL_SHOW_SLIDER" => "N",
		"DETAIL_STRICT_SECTION_CHECK" => "Y",
		"DETAIL_USE_COMMENTS" => "N",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DIR_PARAMS" => CNext::GetDirMenuParametrs(__DIR__),
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"DISPLAY_ELEMENT_COUNT" => "Y",
		"DISPLAY_ELEMENT_SELECT_BOX" => "N",
		"DISPLAY_ELEMENT_SLIDER" => "10",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_WISH_BUTTONS" => "Y",
		"ELEMENT_SORT_FIELD" => "shows",
		"ELEMENT_SORT_FIELD2" => "shows",
		"ELEMENT_SORT_FIELD_BOX" => "name",
		"ELEMENT_SORT_FIELD_BOX2" => "id",
		"ELEMENT_SORT_ORDER" => "desc",
		"ELEMENT_SORT_ORDER2" => "asc",
		"ELEMENT_SORT_ORDER_BOX" => "asc",
		"ELEMENT_SORT_ORDER_BOX2" => "desc",
		"ELEMENT_TYPE_VIEW" => "FROM_MODULE",
		"FIELDS" => array(
			0 => "",
			1 => "",
		),
		"FILE_404" => "",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_HIDE_ON_MOBILE" => "N",
		"FILTER_NAME" => "NEXT_SMART_FILTER",
		"FILTER_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "",
		),
		"FILTER_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "COLOR",
			2 => "CML2_LINK",
			3 => "",
		),
		"FILTER_PRICE_CODE" => array(
			0 => "BASE",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "IN_STOCK",
			2 => "",
		),
		"FILTER_VIEW_MODE" => "VERTICAL",
		"FORUM_ID" => "2",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"HIDE_NOT_AVAILABLE" => "L",
		"HIDE_NOT_AVAILABLE_OFFERS" => "L",
		"IBLOCK_ID" => "2",
		"IBLOCK_STOCK_ID" => "22",
		"IBLOCK_TYPE" => "catalog",
		"INCLUDE_SUBSECTIONS" => "Y",
		"INSTANT_RELOAD" => "N",
		"LABEL_PROP" => "",
		"LANDING_SECTION_COUNT" => "7",
		"LANDING_TITLE" => "Популярные категории",
		"LAZY_LOAD" => "N",
		"LINE_ELEMENT_COUNT" => "4",

		"LINK_IBLOCK_ID" => "2",
		"LINK_FIELD_CODE" => array(
			0 => "ID",
			1 => "IBLOCK_ID",
			2 => "NAME",
			3 => "PREVIEW_PICTURE",
			4 => "DETAIL_PAGE_URL",
			5 => "",
		),
		"LINK_PROPERTY_CODE" => array(
			0 => "BRAND",
			1 => "CML2_ARTICLE",
			2 => "",
		),

		        "LINK_IBLOCK_ID" => "2",
		"LINK_FIELD_CODE" => array(
			0 => "ID",
			1 => "IBLOCK_ID",
			2 => "NAME",
			3 => "PREVIEW_PICTURE",
			4 => "DETAIL_PAGE_URL",
			5 => "DETAIL_PICTURE",
			6 => "IBLOCK_SECTION_ID",
			7 => "",
		),
		"LINK_PROPERTY_CODE" => array(
			0 => "BRAND",
			1 => "CML2_ARTICLE",
			2 => "",
		),
		"LIST_BROWSER_TITLE" => "-",
		"LIST_DISPLAY_POPUP_IMAGE" => "Y",
		"LIST_ENLARGE_PRODUCT" => "STRICT",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_META_KEYWORDS" => "-",
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "CML2_LINK",
			2 => "DETAIL_PAGE_URL",
			3 => "",
		),
		"LIST_OFFERS_LIMIT" => "1000",
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTICLE",
			1 => "VOLUME",
			2 => "SIZES",
			3 => "COLOR_REF",
			4 => "",
		),
		"LIST_PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"LIST_PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
		"LIST_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "COUNT_IN_PACKAGE",
			2 => "VOLUME",
			3 => "BRAND",
			4 => "TYPE",
			5 => "CML2_LINK",
			6 => "",
		),
		"LIST_PROPERTY_CODE_MOBILE" => "",
		"LIST_SHOW_SLIDER" => "Y",
		"LIST_SLIDER_INTERVAL" => "3000",
		"LIST_SLIDER_PROGRESS" => "N",
		"LOAD_ON_SCROLL" => "N",
		"MAIN_TITLE" => "Наличие на складах",
		"MAX_AMOUNT" => "20",
		"MESSAGES_PER_PAGE" => "10",
		"MESSAGE_404" => "",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"MESS_COMMENTS_TAB" => "Комментарии",
		"MESS_DESCRIPTION_TAB" => "Описание",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_PRICE_RANGES_TITLE" => "Цены",
		"MESS_PROPERTIES_TAB" => "Характеристики",
		"MIN_AMOUNT" => "10",
		"NO_WORD_LOGIC" => "Y",
		"OFFERS_CART_PROPERTIES" => "",
		"OFFERS_SORT_FIELD" => "shows",
		"OFFERS_SORT_FIELD2" => "shows",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_ORDER2" => "asc",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_HIDE_NAME_PROPS" => "N",
		"OFFER_TREE_PROPS" => array(
			"VARIANT",
            'NOMINAL',
            'DESIGN',
            'GIFT_CERTIFICATE_PRINT'
		),
        "OFFER_TREE_LIST_PROPS" => [],
		"PAGER_BASE_LINK_ENABLE" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_TEMPLATE" => "main",
		"PAGER_TITLE" => "Товары",
		"PAGE_ELEMENT_COUNT" => "40",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"POST_FIRST_MESSAGE" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"PRICE_VAT_INCLUDE" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"PRODUCT_ID_VARIABLE" => "id",
		"PRODUCT_PROPERTIES" => "",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"PROPERTIES_DISPLAY_LOCATION" => "DESCRIPTION",
		"PROPERTIES_DISPLAY_TYPE" => "BLOCK",
		"RESTART" => "Y",
		"REVIEW_AJAX_POST" => "Y",
		"SALE_STIKER" => "SALE_TEXT",
		"SECTIONS_LIST_PREVIEW_DESCRIPTION" => "N",
		"SECTIONS_LIST_PREVIEW_PROPERTY" => "DESCRIPTION",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"SECTIONS_TYPE_VIEW" => "sections_1",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTION_ADD_TO_BASKET_ACTION" => "ADD",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"SECTION_COUNT_ELEMENTS" => "N",
		"SECTION_DISPLAY_PROPERTY" => "UF_SECTION_TEMPLATE",
		"SECTION_ELEMENTS_TYPE_VIEW" => "list_elements_1",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"SECTION_PREVIEW_DESCRIPTION" => "Y",
		"SECTION_PREVIEW_PROPERTY" => "DESCRIPTION",
		"SECTION_TOP_BLOCK_TITLE" => "Лучшие предложения",
		"SECTION_TOP_DEPTH" => "2",
		"SEF_FOLDER" => "/catalog/",
		"SEF_MODE" => "Y",
		"SET_LAST_MODIFIED" => "Y",
		"SET_STATUS_404" => "Y",
		"SET_TITLE" => "Y",
		"SHOW_404" => "Y",
		"SHOW_ADDITIONAL_TAB" => "Y",
		"SHOW_ARTICLE_SKU" => "Y",
		"SHOW_ASK_BLOCK" => "Y",
		"SHOW_BRAND_PICTURE" => "N",
		"SHOW_CHEAPER_FORM" => "N",
		"SHOW_COUNTER_LIST" => "Y",
		"SHOW_DEACTIVATED" => "N",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_DISCOUNT_TIME" => "Y",
		"SHOW_DISCOUNT_TIME_EACH_SKU" => "N",
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"SHOW_HINTS" => "Y",
		"SHOW_KIT_PARTS" => "Y",
		"SHOW_KIT_PARTS_PRICES" => "Y",
		"SHOW_LINK_TO_FORUM" => "Y",
		"SHOW_MAX_QUANTITY" => "N",
		"SHOW_MEASURE" => "Y",
		"SHOW_MEASURE_WITH_RATIO" => "N",
		"SHOW_OLD_PRICE" => "Y",
		"SHOW_ONE_CLICK_BUY" => "Y",
		"SHOW_PRICE_COUNT" => "1",
		"SHOW_QUANTITY" => "Y",
		"SHOW_QUANTITY_COUNT" => "Y",
		"SHOW_RATING" => "N",
		"SHOW_SECTION_DESC" => "Y",
		"SHOW_SECTION_LIST_PICTURES" => "N",
		"SHOW_SECTION_PICTURES" => "N",
		"SHOW_SECTION_SIBLINGS" => "Y",
		"SHOW_TOP_ELEMENTS" => "Y",
		"SHOW_UNABLE_SKU_PROPS" => "Y",
		"SIDEBAR_DETAIL_SHOW" => "N",
		"SIDEBAR_PATH" => "",
		"SIDEBAR_SECTION_SHOW" => "N",
		"SKU_DETAIL_ID" => "oid",
		"SORT_BUTTONS" => array(
			0 => "POPULARITY",
			1 => "NAME",
			2 => "PRICE",
		),
		"SORT_PRICES" => "BASE",
		"SORT_REGION_PRICE" => "BASE",
		"STIKERS_PROP" => "HIT",
		"STORES" => array(
			0 => "1",
		),
		"STORES_FILTER" => "TITLE",
		"STORES_FILTER_ORDER" => "SORT_ASC",
		"STORE_PATH" => "/contacts/stores/#store_id#/",
		"TAB_CHAR_NAME" => "",
		"TAB_DESCR_NAME" => "",
		"TAB_DOPS_NAME" => "Доставка и оплата",
		"TAB_FAQ_NAME" => "",
		"TAB_OFFERS_NAME" => "",
		"TAB_REVIEW_NAME" => "",
		"TAB_STOCK_NAME" => "",
		"TAB_VIDEO_NAME" => "",
		"TEMPLATE_THEME" => "blue",
		"TOP_ADD_TO_BASKET_ACTION" => "ADD",
		"TOP_ELEMENT_COUNT" => "8",
		"TOP_ELEMENT_SORT_FIELD" => "shows",
		"TOP_ELEMENT_SORT_FIELD2" => "shows",
		"TOP_ELEMENT_SORT_ORDER" => "asc",
		"TOP_ELEMENT_SORT_ORDER2" => "asc",
		"TOP_ENLARGE_PRODUCT" => "STRICT",
		"TOP_LINE_ELEMENT_COUNT" => "4",
		"TOP_OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "",
		),
		"TOP_OFFERS_LIMIT" => "10",
		"TOP_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"TOP_PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"TOP_PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
		"TOP_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"TOP_PROPERTY_CODE_MOBILE" => "",
		"TOP_SHOW_SLIDER" => "Y",
		"TOP_SLIDER_INTERVAL" => "3000",
		"TOP_SLIDER_PROGRESS" => "N",
		"TOP_VIEW_MODE" => "SECTION",
		"URL_TEMPLATES_READ" => "",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"USE_ADDITIONAL_GALLERY" => "Y",
		"USE_ALSO_BUY" => "Y",
		"USE_BIG_DATA" => "N",
		"USE_CAPTCHA" => "Y",
		"USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
		"USE_COMPARE" => "N",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_ENHANCED_ECOMMERCE" => "Y",
		"USE_FILTER" => "Y",
		"USE_FILTER_PRICE" => "Y",
		"USE_GIFTS_DETAIL" => "N",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "N",
		"USE_GIFTS_SECTION" => "N",
		"USE_LANGUAGE_GUESS" => "N",
		"USE_MAIN_ELEMENT_SECTION" => "Y",
		"USE_MIN_AMOUNT" => "N",
		"USE_ONLY_MAX_AMOUNT" => "Y",
		"USE_PRICE_COUNT" => "N",
		"USE_PRODUCT_QUANTITY" => "Y",
		"USE_RATING" => "N",
		"USE_RATIO_IN_RANGES" => "Y",
		"USE_REVIEW" => "Y",
		"USE_SALE_BESTSELLERS" => "Y",
		"USE_SHARE" => "N",
		"USE_STORE" => "N",
		"USE_STORE_PHONE" => "Y",
		"USE_STORE_SCHEDULE" => "Y",
		"VIEWED_BLOCK_TITLE" => "Ранее вы смотрели",
		"VIEWED_ELEMENT_COUNT" => "20",
		"SHOW_SKU_DESCRIPTION" => "Y",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE_PATH#/",
			"element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
			"compare" => "compare.php?action=#ACTION_CODE#",
			"smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
		),
		"VARIABLE_ALIASES" => array(
			"compare" => array(
				"ACTION_CODE" => "action",
			),
		),
        'CACHE_CONTROL_STORE_STATUS' => $arProductInStores
    ),
	false
);?>

<style>
/* Применяем стили выпадающего меню к странице каталога */
.catalog-page-wrapper {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    align-items: flex-start;
    justify-content: center;
    padding-top: 0;
}

.catalog-page-content {
    background: #ffffff;
    border-radius: 0;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
    width: 100%;
    height: 100%;
    max-height: 100vh;
    overflow: hidden;
    animation: catalogDropdownSlideIn 0.3s ease-out;
    display: flex;
    flex-direction: column;
}

@keyframes catalogDropdownSlideIn {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.catalog-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 40px;
    border-bottom: 3px solid #1a4d3a;
    background: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 2px 10px rgba(26, 77, 58, 0.1);
}

.catalog-page-header h1 {
    margin: 0;
    font-size: 32px;
    font-weight: 700;
    color: #1a4d3a;
    text-shadow: none;
}

.catalog-page-close {
    display: flex;
    align-items: center;
}

.catalog-page-back-btn {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    background: #1a4d3a;
    color: #ffffff;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid #1a4d3a;
}

.catalog-page-back-btn:hover {
    background: #ffffff;
    color: #1a4d3a;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(26, 77, 58, 0.3);
    text-decoration: none;
}

.catalog-page-back-btn i {
    margin-right: 8px;
    font-size: 16px;
}

.catalog-page-body {
    padding: 40px;
    flex: 1;
    overflow-y: auto;
    background: #ffffff;
}

.catalog-categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 40px;
    max-width: 1400px;
    margin: 0 auto;
}

.catalog-category-item {
    border: 2px solid #e8f5e8;
    border-radius: 16px;
    padding: 30px;
    transition: all 0.3s ease;
    background: #ffffff;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(26, 77, 58, 0.08);
}

.catalog-category-item::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(26, 77, 58, 0.05) 0%, rgba(26, 77, 58, 0.02) 100%);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.catalog-category-item:hover {
    border-color: #1a4d3a;
    box-shadow: 0 8px 32px rgba(26, 77, 58, 0.15);
    transform: translateY(-4px) scale(1.02);
}

.catalog-category-item:hover::before {
    opacity: 1;
}

.catalog-category-link {
    display: flex;
    align-items: center;
    text-decoration: none;
    color: #1a4d3a;
    font-size: 22px;
    font-weight: 700;
    margin-bottom: 20px;
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.catalog-category-link:hover {
    color: #1a4d3a;
    transform: translateX(5px);
    text-decoration: none;
}

.catalog-category-link i {
    margin-right: 16px;
    font-size: 24px;
    color: #1a4d3a;
}

.catalog-subcategories {
    display: flex;
    flex-direction: column;
    gap: 12px;
    position: relative;
    z-index: 2;
}

.catalog-subcategory-link {
    display: block;
    padding: 12px 16px;
    text-decoration: none;
    color: #666666;
    font-size: 16px;
    border-radius: 8px;
    transition: all 0.3s ease;
    background: #f8f9fa;
    border-left: 3px solid transparent;
}

.catalog-subcategory-link:hover {
    background: #e8f5e8;
    color: #1a4d3a;
    padding-left: 24px;
    border-left-color: #1a4d3a;
    transform: translateX(8px);
    text-decoration: none;
}

.catalog-page-footer {
    padding: 30px 40px;
    border-top: 3px solid #1a4d3a;
    background: #ffffff;
    text-align: center;
    flex-shrink: 0;
    box-shadow: 0 -2px 10px rgba(26, 77, 58, 0.1);
}

.catalog-view-all {
    display: inline-block;
    padding: 16px 40px;
    background: #1a4d3a;
    color: #ffffff;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 700;
    font-size: 18px;
    transition: all 0.3s ease;
    box-shadow: 0 4px 16px rgba(26, 77, 58, 0.2);
    border: 2px solid #1a4d3a;
}

.catalog-view-all:hover {
    background: #ffffff;
    color: #1a4d3a;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(26, 77, 58, 0.3);
    border-color: #1a4d3a;
    text-decoration: none;
}

/* Адаптивность */
@media (max-width: 768px) {
    .catalog-page-content {
        width: 100%;
        height: 100%;
        border-radius: 0;
    }
    
    .catalog-categories-grid {
        grid-template-columns: 1fr;
        gap: 30px;
        padding: 0 20px;
    }
    
    .catalog-page-header {
        padding: 20px 30px;
    }
    
    .catalog-page-header h1 {
        font-size: 24px;
    }
    
    .catalog-page-body {
        padding: 30px 20px;
    }
    
    .catalog-category-item {
        padding: 25px;
    }
    
    .catalog-category-link {
        font-size: 20px;
    }
    
    .catalog-category-link i {
        font-size: 20px;
    }
    
    .catalog-subcategory-link {
        font-size: 14px;
        padding: 10px 14px;
    }
}

@media (max-width: 480px) {
    .catalog-page-header {
        padding: 15px 20px;
    }
    
    .catalog-page-header h1 {
        font-size: 20px;
    }
    
    .catalog-page-back-btn {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .catalog-page-body {
        padding: 20px 15px;
    }
    
    .catalog-categories-grid {
        gap: 20px;
        padding: 0 10px;
    }
    
    .catalog-category-item {
        padding: 20px;
    }
    
    .catalog-category-link {
        font-size: 18px;
    }
    
    .catalog-subcategory-link {
        font-size: 13px;
        padding: 8px 12px;
    }
}
</style>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>