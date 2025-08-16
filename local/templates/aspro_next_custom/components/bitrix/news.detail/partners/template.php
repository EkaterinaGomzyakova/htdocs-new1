<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?>
<?$this->setFrameMode(true);?>
<?use \Bitrix\Main\Localization\Loc;?>
<?if($arParams["DISPLAY_PICTURE"] != "N"){
	$picture = ($arResult["FIELDS"]["DETAIL_PICTURE"] ? "DETAIL_PICTURE" : "PREVIEW_PICTURE");
	CNext::getFieldImageData($arResult, array($picture));
	$arPhoto = $arResult[$picture];
	if($arPhoto){
		$arImgs[] = array(
			'DETAIL' => $arPhoto,
			'PREVIEW' => CFile::ResizeImageGet($arPhoto["ID"], array('width' => 300, 'height' => 300), BX_RESIZE_IMAGE_PROPORTIONAL_ALT, true),
			'TITLE' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['TITLE']) ? $arPhoto['TITLE'] : $arResult['NAME'])),
			'ALT' => (strlen($arPhoto['DESCRIPTION']) ? $arPhoto['DESCRIPTION'] : (strlen($arPhoto['ALT']) ? $arPhoto['ALT'] : $arResult['NAME'])),
		);
	}
}
?>
<div class="detail <?=($templateName = $component->{"__parent"}->{"__template"}->{"__name"})?>">
	<article>
		<?// images?>
		<?if($arImgs):?>
			<div class="detailimage">
				<?if($arImgs):?>
					<div class="img-partner">
						<img src="<?=$arImgs[0]["DETAIL"]["SRC"]?>" title="<?=$arImgs[0]["TITLE"]?>" alt="<?=$arImgs[0]["ALT"]?>" class="img-responsive" />
					</div>
				<?endif;?>
			</div>
		<?endif;?>
		
		<div class="post-content">
			<?if($arParams["DISPLAY_NAME"] != "N" && strlen($arResult["NAME"])):?>
				<h2><?=$arResult["NAME"]?></h2>
			<?endif;?>
			<div class="content">
				<?// text?>
				<?if(strlen($arResult["FIELDS"]["PREVIEW_TEXT"].$arResult["FIELDS"]["DETAIL_TEXT"])):?>
					<div class="text">
						<?if($arResult["DETAIL_TEXT_TYPE"] == "text"):?>
							<p><?=$arResult["FIELDS"]["DETAIL_TEXT"];?></p>
						<?else:?>
							<?=$arResult["FIELDS"]["DETAIL_TEXT"];?>
						<?endif;?>
					</div>
				<?endif;?>
				
				<?// display properties?>
				<?if($arResult["DISPLAY_PROPERTIES"]):?>
					<hr/>
					<div class="properties">
						<?foreach($arResult["DISPLAY_PROPERTIES"] as $PCODE => $arProperty):?>
							<?$bIconBlock = ($PCODE == 'EMAIL' || $PCODE == 'PHONE' || $PCODE == 'SITE');?>
							<div class="inner-wrapper">
								<div class="property <?=($bIconBlock ? "icon-block" : "");?> <?=strtolower($PCODE);?>">
									<?if(!$bIconBlock):?>
										<?=$arProperty['NAME']?>:&nbsp;
									<?endif;?>
									<?if(is_array($arProperty["DISPLAY_VALUE"])):?>
										<?$val = implode("&nbsp;/ ", $arProperty["DISPLAY_VALUE"]);?>
									<?else:?>
										<?$val = $arProperty["DISPLAY_VALUE"];?>
									<?endif;?>
									<?if($PCODE == "SITE"):?>
										<!--noindex-->
										<a href="<?=(strpos($arProperty['VALUE'], 'http') === false ? 'http://' : '').$arProperty['VALUE'];?>" rel="nofollow" target="_blank">
											<?=$arProperty['VALUE'];?>
										</a>
										<!--/noindex-->
									<?elseif($PCODE == "EMAIL"):?>
										<a href="mailto:<?=$val?>"><?=$val?></a>
									<?else:?>
										<?=$val?>
									<?endif;?>
								</div>
							</div>
						<?endforeach;?>
					</div>
				<?endif;?>
			</div>
		</div>
	</article>
</div>

<?if($arResult['GALLERY']):?>
	<div class="wraps with-padding galerys-block">
		<hr />
		<h5><?=$arParams['T_GALLERY'];?></h5>
		<div class="small-gallery-block">
			<div class="flexslider unstyled front border custom_flex top_right color-controls" data-plugin-options='{"animation": "slide", "directionNav": true, "controlNav" :true, "animationLoop": true, "slideshow": false, "touch": true, "counts": [4, 3, 2, 1]}'>
				<ul class="slides items">
					<?foreach($arResult['GALLERY'] as $i => $arPhoto):?>
						<li class="col-md-3 item">
							<div>
								<img src="<?=$arPhoto['PREVIEW']['src']?>" class="img-responsive inline" title="<?=$arPhoto['TITLE']?>" alt="<?=$arPhoto['ALT']?>" />
							</div>
							<a href="<?=$arPhoto['DETAIL']['SRC']?>" class="fancy dark_block_animate" rel="gallery" target="_blank" title="<?=$arPhoto['TITLE']?>"></a>
						</li>
					<?endforeach;?>
				</ul>
			</div>
		</div>
	</div>
<?endif;?>

<?// docs files?>
<?if($arResult['DOCUMENTS']):?>
	<div class="wraps docs-block">
		<hr/>
		<h5><?=(strlen($arParams['T_DOCS']) ? $arParams['T_DOCS'] : Loc::getMessage('T_DOCS'))?></h5>
		<div class="files_block">
			<div class="row">
				<?foreach($arResult['DOCUMENTS']['VALUE'] as $arItem):?>
					<div class="col-md-3 col-sm-6">
						<?$arFile=CNext::GetFileInfo($arItem);?>
						<div class="file_type clearfix <?=$arFile["TYPE"];?>">
							<i class="icon"></i>
							<div class="description">
								<a target="_blank" href="<?=$arFile["SRC"];?>" class="dark_link"><?=$arFile["DESCRIPTION"];?></a>
								<span class="size">
									<?=$arFile["FILE_SIZE_FORMAT"];?>
								</span>
							</div>
						</div>
					</div>
				<?endforeach;?>
			</div>
		</div>
	</div>
<?endif;?>

<?if($arResult['LINKED_PRODUCTS']):?>
	<div class="clear"></div>
	<h2><?=Loc::getMessage('LINKED_PRODUCTS_HEADER')?><?=$arResult['NAME']?></h2>
	<div class="row">
		<?
		global $arLinkedProdctFilter;
		$arLinkedProdctFilter = array(
			"ID" => $arResult['LINKED_PRODUCTS'],
		);
		?>
		<?
		$APPLICATION->IncludeComponent(
	"bitrix:catalog.section", 
	"catalog_block", 
	array(
		"IBLOCK_TYPE" => "catalog",
		"IBLOCK_ID" => "2",
		"HIDE_NOT_AVAILABLE" => "L",
		"BASKET_URL" => "/basket/",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"SECTION_ID_VARIABLE" => "SECTION_ID",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"SEF_MODE" => "Y",
		"SEF_FOLDER" => "/catalog/",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "N",
		"AJAX_OPTION_HISTORY" => "N",
		"CACHE_TYPE" => "A",
		"CACHE_TIME" => "3600000",
		"CACHE_FILTER" => "Y",
		"CACHE_GROUPS" => "N",
		"SET_TITLE" => "N",
		"SET_STATUS_404" => "Y",
		"USE_ELEMENT_COUNTER" => "Y",
		"USE_FILTER" => "Y",
		"FILTER_NAME" => "arLinkedProdctFilter",
		"FILTER_FIELD_CODE" => array(
			0 => "",
			1 => "",
		),
		"FILTER_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "IN_STOCK",
			2 => "",
		),
		"FILTER_PRICE_CODE" => array(
			0 => "BASE",
		),
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
		"USE_REVIEW" => "Y",
		"MESSAGES_PER_PAGE" => "10",
		"USE_CAPTCHA" => "Y",
		"REVIEW_AJAX_POST" => "Y",
		"PATH_TO_SMILE" => "/bitrix/images/forum/smile/",
		"FORUM_ID" => "2",
		"URL_TEMPLATES_READ" => "",
		"SHOW_LINK_TO_FORUM" => "Y",
		"POST_FIRST_MESSAGE" => "N",
		"USE_COMPARE" => "N",
		"COMPARE_NAME" => "CATALOG_COMPARE_LIST",
		"COMPARE_FIELD_CODE" => array(
			0 => "NAME",
			1 => "TAGS",
			2 => "SORT",
			3 => "PREVIEW_PICTURE",
			4 => "",
		),
		"COMPARE_PROPERTY_CODE" => array(
			0 => "BRAND_REF",
			1 => "CML2_ARTICLE",
			2 => "VOLUME",
			3 => "BRAND",
			4 => "CML2_BASE_UNIT",
			5 => "CML2_MANUFACTURER",
			6 => "",
		),
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
		"COMPARE_ELEMENT_SORT_FIELD" => "shows",
		"COMPARE_ELEMENT_SORT_ORDER" => "asc",
		"DISPLAY_ELEMENT_SELECT_BOX" => "N",
		"PRICE_CODE" => array(
			0 => "BASE",
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "N",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"PRODUCT_PROPERTIES" => array(
		),
		"USE_PRODUCT_QUANTITY" => "Y",
		"CONVERT_CURRENCY" => "Y",
		"CURRENCY_ID" => "RUB",
		"OFFERS_CART_PROPERTIES" => "",
		"SHOW_TOP_ELEMENTS" => "Y",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "2",
		"SECTIONS_LIST_PREVIEW_PROPERTY" => "DESCRIPTION",
		"SHOW_SECTION_LIST_PICTURES" => "Y",
		"PAGE_ELEMENT_COUNT" => "20",
		"LINE_ELEMENT_COUNT" => "4",
		"ELEMENT_SORT_FIELD" => "shows",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "shows",
		"ELEMENT_SORT_ORDER2" => "asc",
		"LIST_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "BRAND",
			2 => "VOLUME",
			3 => "CML2_LINK",
			4 => "",
		),
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"LIST_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "CML2_LINK",
			2 => "DETAIL_PAGE_URL",
			3 => "",
		),
		"LIST_OFFERS_PROPERTY_CODE" => array(
			0 => "ARTICLE",
			1 => "VOLUME",
			2 => "SIZES",
			3 => "COLOR_REF",
			4 => "",
		),
		"LIST_OFFERS_LIMIT" => "10",
		"SORT_BUTTONS" => array(
			0 => "POPULARITY",
			1 => "NAME",
			2 => "PRICE",
		),
		"SORT_PRICES" => "REGION_PRICE",
		"DEFAULT_LIST_TEMPLATE" => "block",
		"SECTION_DISPLAY_PROPERTY" => "UF_SECTION_TEMPLATE",
		"LIST_DISPLAY_POPUP_IMAGE" => "Y",
		"SECTION_PREVIEW_PROPERTY" => "DESCRIPTION",
		"SHOW_SECTION_PICTURES" => "Y",
		"SHOW_SECTION_SIBLINGS" => "Y",
		"DETAIL_PROPERTY_CODE" => array(
			0 => "CML2_ARTICLE",
			1 => "BRAND",
			2 => "COUNT_IN_PACKAGE",
			3 => "VOLUME",
			4 => "BRAND_REF",
			5 => "",
		),
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_OFFERS_FIELD_CODE" => array(
			0 => "NAME",
			1 => "PREVIEW_PICTURE",
			2 => "DETAIL_PICTURE",
			3 => "DETAIL_PAGE_URL",
			4 => "",
		),
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
		"PROPERTIES_DISPLAY_LOCATION" => "DESCRIPTION",
		"SHOW_BRAND_PICTURE" => "Y",
		"SHOW_ASK_BLOCK" => "Y",
		"ASK_FORM_ID" => "2",
		"SHOW_ADDITIONAL_TAB" => "Y",
		"PROPERTIES_DISPLAY_TYPE" => "TABLE",
		"SHOW_KIT_PARTS" => "Y",
		"SHOW_KIT_PARTS_PRICES" => "Y",
		"LINK_IBLOCK_TYPE" => "aspro_next_content",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "Y",
		"ALSO_BUY_ELEMENT_COUNT" => "5",
		"ALSO_BUY_MIN_BUYES" => "2",
		"USE_STORE" => "N",
		"USE_STORE_PHONE" => "Y",
		"USE_STORE_SCHEDULE" => "Y",
		"USE_MIN_AMOUNT" => "N",
		"MIN_AMOUNT" => "10",
		"STORE_PATH" => "/contacts/stores/#store_id#/",
		"MAIN_TITLE" => "Наличие на складах",
		"MAX_AMOUNT" => "20",
		"USE_ONLY_MAX_AMOUNT" => "Y",
		"OFFERS_SORT_FIELD" => "shows",
		"OFFERS_SORT_ORDER" => "asc",
		"OFFERS_SORT_FIELD2" => "shows",
		"OFFERS_SORT_ORDER2" => "asc",
		"PAGER_TEMPLATE" => "main",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "Y",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "Y",
		"IBLOCK_STOCK_ID" => "22",
		"SHOW_QUANTITY" => "Y",
		"SHOW_MEASURE" => "Y",
		"SHOW_QUANTITY_COUNT" => "Y",
		"USE_RATING" => "Y",
		"DISPLAY_WISH_BUTTONS" => "Y",
		"DEFAULT_COUNT" => "1",
		"SHOW_HINTS" => "Y",
		"AJAX_OPTION_ADDITIONAL" => "",
		"ADD_SECTIONS_CHAIN" => "N",
		"ADD_ELEMENT_CHAIN" => "Y",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PARTIAL_PRODUCT_PROPERTIES" => "Y",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"STORES" => array(
			0 => "",
			1 => "",
		),
		"USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_EMPTY_STORE" => "Y",
		"SHOW_GENERAL_STORE_INFORMATION" => "N",
		"TOP_ELEMENT_COUNT" => "8",
		"TOP_LINE_ELEMENT_COUNT" => "4",
		"TOP_ELEMENT_SORT_FIELD" => "shows",
		"TOP_ELEMENT_SORT_ORDER" => "asc",
		"TOP_ELEMENT_SORT_FIELD2" => "shows",
		"TOP_ELEMENT_SORT_ORDER2" => "asc",
		"TOP_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"COMPONENT_TEMPLATE" => "catalog_block",
		"DETAIL_SET_CANONICAL_URL" => "Y",
		"SHOW_DEACTIVATED" => "N",
		"TOP_OFFERS_FIELD_CODE" => array(
			0 => "ID",
			1 => "",
		),
		"TOP_OFFERS_PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"TOP_OFFERS_LIMIT" => "10",
		"SECTION_TOP_BLOCK_TITLE" => "Лучшие предложения",
		"OFFER_TREE_PROPS" => array(
			0 => "SIZES",
			1 => "COLOR_REF",
		),
		"USE_BIG_DATA" => "Y",
		"BIG_DATA_RCM_TYPE" => "bestsell",
		"SHOW_DISCOUNT_PERCENT" => "Y",
		"SHOW_OLD_PRICE" => "Y",
		"VIEWED_ELEMENT_COUNT" => "20",
		"VIEWED_BLOCK_TITLE" => "Ранее вы смотрели",
		"ELEMENT_SORT_FIELD_BOX" => "name",
		"ELEMENT_SORT_ORDER_BOX" => "asc",
		"ELEMENT_SORT_FIELD_BOX2" => "id",
		"ELEMENT_SORT_ORDER_BOX2" => "desc",
		"ADD_PICT_PROP" => "MORE_PHOTO",
		"OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
		"DETAIL_ADD_DETAIL_TO_SLIDER" => "Y",
		"SKU_DETAIL_ID" => "oid",
		"USE_MAIN_ELEMENT_SECTION" => "Y",
		"SET_LAST_MODIFIED" => "Y",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"SHOW_404" => "Y",
		"MESSAGE_404" => "",
		"AJAX_FILTER_CATALOG" => "N",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"DISPLAY_ELEMENT_SLIDER" => "10",
		"SHOW_ONE_CLICK_BUY" => "Y",
		"USE_GIFTS_DETAIL" => "N",
		"USE_GIFTS_SECTION" => "N",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "N",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "8",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"OFFER_HIDE_NAME_PROPS" => "N",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"SECTION_PREVIEW_DESCRIPTION" => "Y",
		"SECTIONS_LIST_PREVIEW_DESCRIPTION" => "Y",
		"SALE_STIKER" => "SALE_TEXT",
		"SHOW_DISCOUNT_TIME" => "Y",
		"SHOW_RATING" => "Y",
		"COMPOSITE_FRAME_MODE" => "A",
		"COMPOSITE_FRAME_TYPE" => "AUTO",
		"DETAIL_OFFERS_LIMIT" => "0",
		"DETAIL_EXPANDABLES_TITLE" => "Аксессуары",
		"DETAIL_ASSOCIATED_TITLE" => "Похожие товары",
		"DETAIL_PICTURE_MODE" => "MAGNIFIER",
		"SHOW_UNABLE_SKU_PROPS" => "Y",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"DETAIL_STRICT_SECTION_CHECK" => "Y",
		"COMPATIBLE_MODE" => "Y",
		"TEMPLATE_THEME" => "blue",
		"LABEL_PROP" => "",
		"PRODUCT_DISPLAY_MODE" => "Y",
		"COMMON_SHOW_CLOSE_POPUP" => "N",
		"PRODUCT_SUBSCRIPTION" => "Y",
		"SHOW_MAX_QUANTITY" => "N",
		"MESS_BTN_BUY" => "Купить",
		"MESS_BTN_ADD_TO_BASKET" => "В корзину",
		"MESS_BTN_COMPARE" => "Сравнение",
		"MESS_BTN_DETAIL" => "Подробнее",
		"MESS_NOT_AVAILABLE" => "Нет в наличии",
		"MESS_BTN_SUBSCRIBE" => "Подписаться",
		"SIDEBAR_SECTION_SHOW" => "Y",
		"SIDEBAR_DETAIL_SHOW" => "N",
		"SIDEBAR_PATH" => "",
		"USE_SALE_BESTSELLERS" => "Y",
		"FILTER_VIEW_MODE" => "VERTICAL",
		"FILTER_HIDE_ON_MOBILE" => "N",
		"INSTANT_RELOAD" => "N",
		"COMPARE_POSITION_FIXED" => "Y",
		"COMPARE_POSITION" => "top left",
		"USE_RATIO_IN_RANGES" => "Y",
		"USE_COMMON_SETTINGS_BASKET_POPUP" => "N",
		"COMMON_ADD_TO_BASKET_ACTION" => "ADD",
		"TOP_ADD_TO_BASKET_ACTION" => "ADD",
		"SECTION_ADD_TO_BASKET_ACTION" => "ADD",
		"DETAIL_ADD_TO_BASKET_ACTION" => array(
			0 => "BUY",
		),
		"DETAIL_ADD_TO_BASKET_ACTION_PRIMARY" => array(
			0 => "BUY",
		),
		"TOP_PROPERTY_CODE_MOBILE" => "",
		"TOP_VIEW_MODE" => "SECTION",
		"TOP_PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"TOP_PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
		"TOP_ENLARGE_PRODUCT" => "STRICT",
		"TOP_SHOW_SLIDER" => "Y",
		"TOP_SLIDER_INTERVAL" => "3000",
		"TOP_SLIDER_PROGRESS" => "N",
		"SECTIONS_VIEW_MODE" => "LIST",
		"SECTIONS_SHOW_PARENT_NAME" => "Y",
		"LIST_PROPERTY_CODE_MOBILE" => "",
		"LIST_PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
		"LIST_PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false},{'VARIANT':'3','BIG_DATA':false}]",
		"LIST_ENLARGE_PRODUCT" => "STRICT",
		"LIST_SHOW_SLIDER" => "Y",
		"LIST_SLIDER_INTERVAL" => "3000",
		"LIST_SLIDER_PROGRESS" => "N",
		"DETAIL_MAIN_BLOCK_PROPERTY_CODE" => "",
		"DETAIL_MAIN_BLOCK_OFFERS_PROPERTY_CODE" => "",
		"DETAIL_USE_VOTE_RATING" => "N",
		"DETAIL_USE_COMMENTS" => "N",
		"DETAIL_BRAND_USE" => "N",
		"DETAIL_DISPLAY_NAME" => "Y",
		"DETAIL_IMAGE_RESOLUTION" => "16by9",
		"DETAIL_PRODUCT_INFO_BLOCK_ORDER" => "sku,props",
		"DETAIL_PRODUCT_PAY_BLOCK_ORDER" => "rating,price,priceRanges,quantityLimit,quantity,buttons",
		"DETAIL_SHOW_SLIDER" => "N",
		"DETAIL_DETAIL_PICTURE_MODE" => array(
			0 => "POPUP",
			1 => "MAGNIFIER",
		),
		"DETAIL_DISPLAY_PREVIEW_TEXT_MODE" => "E",
		"MESS_PRICE_RANGES_TITLE" => "Цены",
		"MESS_DESCRIPTION_TAB" => "Описание",
		"MESS_PROPERTIES_TAB" => "Характеристики",
		"MESS_COMMENTS_TAB" => "Комментарии",
		"LAZY_LOAD" => "N",
		"LOAD_ON_SCROLL" => "N",
		"USE_ENHANCED_ECOMMERCE" => "N",
		"DETAIL_DOCS_PROP" => "INSTRUCTIONS",
		"STIKERS_PROP" => "HIT",
		"USE_SHARE" => "Y",
		"TAB_OFFERS_NAME" => "",
		"TAB_DESCR_NAME" => "",
		"TAB_CHAR_NAME" => "",
		"TAB_VIDEO_NAME" => "",
		"TAB_REVIEW_NAME" => "",
		"TAB_FAQ_NAME" => "",
		"TAB_STOCK_NAME" => "",
		"TAB_DOPS_NAME" => "Доставка и оплата",
		"BLOCK_SERVICES_NAME" => "",
		"BLOCK_DOCS_NAME" => "",
		"CHEAPER_FORM_NAME" => "",
		"DIR_PARAMS" => CNext::GetDirMenuParametrs(__DIR__),
		"SHOW_CHEAPER_FORM" => "N",
		"LANDING_TITLE" => "Популярные категории",
		"LANDING_SECTION_COUNT" => "7",
		"SECTIONS_TYPE_VIEW" => "sections_1",
		"SECTION_ELEMENTS_TYPE_VIEW" => "list_elements_1",
		"ELEMENT_TYPE_VIEW" => "FROM_MODULE",
		"SHOW_ARTICLE_SKU" => "Y",
		"SORT_REGION_PRICE" => "BASE",
		"SHOW_MEASURE_WITH_RATIO" => "N",
		"SHOW_COUNTER_LIST" => "Y",
		"SHOW_DISCOUNT_TIME_EACH_SKU" => "N",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"USE_FILTER_PRICE" => "Y",
		"DISPLAY_ELEMENT_COUNT" => "Y",
		"RESTART" => "Y",
		"USE_LANGUAGE_GUESS" => "N",
		"NO_WORD_LOGIC" => "Y",
		"SHOW_SECTION_DESC" => "Y",
		"USE_ADDITIONAL_GALLERY" => "Y",
		"ADDITIONAL_GALLERY_TYPE" => "BIG",
		"ADDITIONAL_GALLERY_PROPERTY_CODE" => "-",
		"ADDITIONAL_GALLERY_OFFERS_PROPERTY_CODE" => "",
		"BLOCK_ADDITIONAL_GALLERY_NAME" => "",
		"STORES_FILTER" => "TITLE",
		"STORES_FILTER_ORDER" => "SORT_ASC",
		"FILE_404" => "",
		"SECTION_USER_FIELDS" => array(
			0 => "",
			1 => "",
		),
		"SHOW_ALL_WO_SECTION" => "Y",
		"CUSTOM_FILTER" => "",
		"PROPERTY_CODE" => array(
			0 => "",
			1 => "",
		),
		"OFFERS_LIMIT" => "0",
		"BACKGROUND_IMAGE" => "-",
		"SECTION_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/",
		"DETAIL_URL" => "#SITE_DIR#/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
		"SEF_RULE" => "",
		"SECTION_ID" => $_REQUEST["SECTION_ID"],
		"SECTION_CODE" => "",
		"SECTION_CODE_PATH" => "",
		"SET_BROWSER_TITLE" => "N",
		"BROWSER_TITLE" => "-",
		"SET_META_KEYWORDS" => "N",
		"META_KEYWORDS" => "-",
		"SET_META_DESCRIPTION" => "N",
		"META_DESCRIPTION" => "-",
		"DISPLAY_COMPARE" => "N"
	),
	false
);?>
	</div>
<?endif;?>