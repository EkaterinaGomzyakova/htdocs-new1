<?php
global $APPLICATION;
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Косметика со скидкой ClanBeauty.ru");
$APPLICATION->SetPageProperty('title', "Косметика со скидкой в интернет-магазине ClanBeauty.ru");
$APPLICATION->SetPageProperty("HIDE_LEFT_BLOCK", "Y");

$GLOBALS['arFilter']['PROPERTY_HIT'] = 101;
$GLOBALS['arFilter']['!SECTION_ID'] = 167; //Пробники

$isAjax = (
    isset($_SERVER["HTTP_X_REQUESTED_WITH"], $_GET["ajax_get"])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === "xmlhttprequest"
    && $_GET["ajax_get"] === 'Y'
) || (
    isset($_GET["ajax_basket"])
    && $_GET["ajax_basket"] === 'Y'
) ? 'Y' : 'N';

if ($isAjax === 'N') {
    ?>
    <!-- Табы для навигации по разделам offers -->
    <div class="offers-tabs-container">
        <ul class="offers-tabs">
            <li><a href="/offers/hits/" class="offers-tab">Хит</a></li>
            <li><a href="/offers/novelty/" class="offers-tab">Новинки</a></li>
            <li><a href="/offers/recommend/" class="offers-tab">К лету</a></li>
            <li><a href="/offers/discount/" class="offers-tab active">Скидки</a></li>
            <li><a href="/offers/to70discount/" class="offers-tab">До -70%</a></li>
            <li><a href="/offers/top/" class="offers-tab">Топ 2024</a></li>
            <li><a href="/offers/for_him/" class="offers-tab">Для него</a></li>
            <li><a href="/offers/blogger_advice/" class="offers-tab">Блогеры советуют</a></li>
        </ul>
    </div>
    <div class="ajax_load offers">
    <?php
}
$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    'catalog_block',
    array(
        "FILTER_NAME" => "arFilter",
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => "2",
        "HIDE_NOT_AVAILABLE" => "L",
        "BASKET_URL" => "/basket/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "AJAX_MODE" => "N",
        "AJAX_OPTION_JUMP" => "N",
        "AJAX_OPTION_STYLE" => "N",
        "AJAX_OPTION_HISTORY" => "N",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => "18000",
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        "SET_TITLE" => "N",
        "SET_STATUS_404" => "Y",
        "PRICE_CODE" => array(
            0 => "BASE",
        ),
        "PROPERTY_CODE" => ["BRAND"],
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "N",
        "PRODUCT_PROPERTIES" => array(),
        "USE_PRODUCT_QUANTITY" => "Y",
        "CONVERT_CURRENCY" => "Y",
        "CURRENCY_ID" => "RUB",
        "OFFERS_CART_PROPERTIES" => "",
        "SECTIONS_LIST_PREVIEW_PROPERTY" => "DESCRIPTION",
        "SHOW_SECTION_LIST_PICTURES" => "N",
        "PAGE_ELEMENT_COUNT" => "80",
        "LINE_ELEMENT_COUNT" => "4",
        "ELEMENT_SORT_FIELD" => "shows",
        "ELEMENT_SORT_ORDER" => "desc",
        "ELEMENT_SORT_FIELD2" => "shows",
        "ELEMENT_SORT_ORDER2" => "asc",
        "INCLUDE_SUBSECTIONS" => "Y",
        "LIST_DISPLAY_POPUP_IMAGE" => "Y",
        "USE_STORE" => "N",
        "USE_MIN_AMOUNT" => "N",
        "MIN_AMOUNT" => "10",
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
        "SHOW_QUANTITY" => "Y",
        "SHOW_MEASURE" => "Y",
        "SHOW_QUANTITY_COUNT" => "Y",
        "DISPLAY_WISH_BUTTONS" => "Y",
        "DEFAULT_COUNT" => "1",
        "SHOW_HINTS" => "Y",
        "AJAX_OPTION_ADDITIONAL" => "",
        "ADD_SECTIONS_CHAIN" => "Y",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PARTIAL_PRODUCT_PROPERTIES" => "Y",
        "OFFER_TREE_PROPS" => array(
            0 => "SIZES",
            1 => "COLOR_REF",
        ),

        "SHOW_DISCOUNT_PERCENT" => "Y",
        "SHOW_OLD_PRICE" => "Y",
        "USE_MAIN_ELEMENT_SECTION" => "Y",
        "SET_LAST_MODIFIED" => "Y",
        "SHOW_404" => "Y",
        "MESSAGE_404" => "",
        "OFFER_HIDE_NAME_PROPS" => "N",
        "SALE_STIKER" => "SALE_TEXT",
        "SHOW_DISCOUNT_TIME" => "Y",
        "SHOW_RATING" => "N",
        "SHOW_UNABLE_SKU_PROPS" => "Y",
        "HIDE_NOT_AVAILABLE_OFFERS" => "L",
        "STIKERS_PROP" => "HIT",
        "SHOW_ARTICLE_SKU" => "Y",
        "SHOW_MEASURE_WITH_RATIO" => "N",
        "SHOW_COUNTER_LIST" => "Y",
        "SHOW_DISCOUNT_TIME_EACH_SKU" => "N",
        "FILE_404" => "",
        "AJAX_REQUEST" => $isAjax,
        "SEF_URL_TEMPLATES" => array(
            "sections" => "",
            "section" => "#SECTION_CODE_PATH#/",
            "element" => "#SECTION_CODE_PATH#/#ELEMENT_CODE#/",
            "compare" => "compare.php?action=#ACTION_CODE#",
            "smart_filter" => "#SECTION_CODE_PATH#/filter/#SMART_FILTER_PATH#/apply/",
        ),
        "STORES" => ['1'],
        'COMPATIBLE_MODE' => 'Y',
    ), false
);
if ($isAjax === 'N') {
    ?>
    </div>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
