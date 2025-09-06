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

<?$APPLICATION->IncludeComponent(
	"bitrix:main.include", 
	"basket", 
	array(
		"COMPONENT_TEMPLATE" => "basket",
		"PATH" => SITE_DIR."include/comp_basket_bigdata.php",
		"AREA_FILE_SHOW" => "file",
		"AREA_FILE_SUFFIX" => "",
		"AREA_FILE_RECURSIVE" => "Y",
		"EDIT_TEMPLATE" => "standard.php",
		"PRICE_CODE" => array(
			0 => "BASE",
			1 => "OPT",
		),
		"STORES" => array(
			0 => "1",
			1 => "2",
			2 => "",
		),
		"BIG_DATA_RCM_TYPE" => "bestsell",
		"STIKERS_PROP" => "HIT",
		"SALE_STIKER" => "SALE_TEXT"
	),
	false
);?>

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