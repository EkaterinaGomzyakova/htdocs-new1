<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

// Ваш обязательный глобальный фильтр
$GLOBALS['arrFilterProp']['!IBLOCK_SECTION_ID'] = 167;

// Создаем фильтры для каждого блока. Предполагаем, что в инфоблоке ID=2 есть свойство HIT с теми же ID значений.
global $arrFilterHit, $arrFilterBloggers, $arrFilterNew, $arrFilterSale;
$arrFilterHit = array_merge($GLOBALS['arrFilterProp'], ["=PROPERTY_HIT" => 98]);
$arrFilterBloggers = array_merge($GLOBALS['arrFilterProp'], ["=PROPERTY_HIT" => 183]);
$arrFilterNew = array_merge($GLOBALS['arrFilterProp'], ["=PROPERTY_HIT" => 100]);
$arrFilterSale = array_merge($GLOBALS['arrFilterProp'], ["=PROPERTY_HIT" => 101]);

// ОБЩИЕ ПАРАМЕТРЫ для bitrix:catalog.section
$mainComponentParams = array(
    // === Ваши существующие параметры (все правильные) ===
    "IBLOCK_TYPE" => "catalog", "IBLOCK_ID" => "2", "SECTION_ID" => "", "SECTION_CODE" => "",
    "INCLUDE_SUBSECTIONS" => "Y", "HIDE_NOT_AVAILABLE" => "Y",
    "ELEMENT_SORT_FIELD" => "rand", "ELEMENT_SORT_ORDER" => "asc", "ELEMENT_SORT_FIELD2" => "id", "ELEMENT_SORT_ORDER2" => "desc",
    "SHOW_ALL_WO_SECTION" => "Y",
    "PAGE_ELEMENT_COUNT" => "3", "LINE_ELEMENT_COUNT" => "3",
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


<!-- ================= НАЧАЛО БЛОКА "СКИДКИ" ================= -->
<div class="discounts-block">
    
    <!-- Левая колонка с информацией -->
    <div class="discounts-info">
        <h2 class="discounts-title">Скидки</h2>
        <a href="/offers/discount/" class="discounts-block-button">
            Посмотреть все
        </a>
    </div>

    <!-- Правая колонка со слайдером товаров -->
    <div class="discounts-slider">
        <?
        // Мы оставляем ВАШ вызов компонента без изменений.
        // Он просто будет находиться внутри нашей новой обертки.
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "catalog_block_discount", // Убедитесь, что это ваш правильный шаблон
            array_merge($mainComponentParams, ["FILTER_NAME" => "arrFilterSale"]),
            false
        );
        ?>
    </div>

</div>
<!-- ================= КОНЕЦ БЛОКА "СКИДКИ" ================= -->





