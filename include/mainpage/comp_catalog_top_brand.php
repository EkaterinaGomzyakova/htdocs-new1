<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// ================================================================
// НАСТРОЙКИ БЛОКА
// ================================================================

// !!! ЗАМЕНИТЕ 155 НА ID ВАШЕГО ТОП-БРЕНДА !!!
$topBrandID = $arParams["TOP_BRAND_ID"];

// ================================================================

// Подключаем модуль инфоблоков
if (!CModule::IncludeModule('iblock')) {
    return;
}

// Получаем информацию о бренде (название и ссылку)
$arBrand = CIBlockElement::GetByID($topBrandID)->GetNext();

// Если бренд с таким ID найден
if ($arBrand) {
    
    // Создаем глобальный фильтр для компонента
    global $arrTopBrandFilter;
    $arrTopBrandFilter = array(
        "=PROPERTY_BRAND" => $topBrandID, // Фильтруем товары по свойству "BRAND", где ID = $topBrandID
    );
    
    // ОБЩИЕ ПАРАМЕТРЫ для вывода товаров.
    // Скопированы из вашего предыдущего рабочего кода.
    $mainComponentParams = array(
        "IBLOCK_TYPE" => "catalog", "IBLOCK_ID" => "2",
        "INCLUDE_SUBSECTIONS" => "Y", "HIDE_NOT_AVAILABLE" => "Y",
        
        // Сортировка по полю "Сортировка" (чтобы вы могли вручную задать топ-3)
        "ELEMENT_SORT_FIELD" => "sort", "ELEMENT_SORT_ORDER" => "asc", 
        "ELEMENT_SORT_FIELD2" => "id", "ELEMENT_SORT_ORDER2" => "desc",
        
        "SHOW_ALL_WO_SECTION" => "Y", 
        "PAGE_ELEMENT_COUNT" => "3", // <<< ВЫВОДИМ ТОЛЬКО 3 ТОВАРА
        "LINE_ELEMENT_COUNT" => "3",
        "PROPERTY_CODE" => array("BRAND", "ALT_NAME"), "OFFERS_LIMIT" => "5", 
        "BASKET_URL" => "/basket/", "ACTION_VARIABLE" => "action", "PRODUCT_ID_VARIABLE" => "id",
        "CACHE_TYPE" => "A", "CACHE_TIME" => "3600", "CACHE_GROUPS" => "Y", "CACHE_FILTER" => "Y",
        "DISPLAY_COMPARE" => "N", "SET_TITLE" => "N",
        "PRICE_CODE" => array("BASE"), "USE_PRICE_COUNT" => "N", "SHOW_PRICE_COUNT" => "1",
        "PRICE_VAT_INCLUDE" => "Y", "SHOW_OLD_PRICE" => "Y",
        'COMPATIBLE_MODE' => 'Y',
        "SHOW_DISCOUNT_TIME" => "Y", "SHOW_DISCOUNT_PERCENT" => "Y", "SALE_STIKER" => "SALE_TEXT",
        "STIKERS_PROP" => "HIT", "SHOW_MEASURE" => "Y", "DISPLAY_WISH_BUTTONS" => "Y",
    );
?>

<!-- === НАЧАЛО HTML-ВЕРСТКИ БЛОКА === -->
<div class="top-brand-block">
    <div class="top-brand-info">
        <h2 class="top-brand-title">Топ-бренд сезона</h2>
        <!-- Выводим название бренда как подзаголовок (необязательно) -->
        <p class="top-brand-subtitle"><?= $arBrand['NAME'] ?></p>
        
        <!-- Кнопка "Посмотреть все" ведет на страницу бренда -->
        <a href="<?= $arBrand['DETAIL_PAGE_URL'] ?>" class="btn btn-default top-brand-button">Посмотреть все</a>
    </div>
    <div class="top-brand-products">
        <?php
        // Вызываем компонент для отображения товаров
        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "catalog_block_top_brand", // Используем ваш кастомный или стандартный шаблон
            array_merge($mainComponentParams, ["FILTER_NAME" => "arrTopBrandFilter"]),
            false
        );
        ?>
    </div>
</div>
<!-- === КОНЕЦ HTML-ВЕРСТКИ БЛОКА === -->

<?php 
} // Закрываем проверку if ($arBrand)
?>