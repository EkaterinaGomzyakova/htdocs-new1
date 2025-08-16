<?
use Bitrix\Main\Grid\Options;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UI\PageNavigation;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin.php");
Asset::getInstance()->addJs('/local/modules/custom.reports/admin/wl_purchase_price/script.js');

$APPLICATION->SetTitle("Наценка и Установка розничных цен");

try {
    Loader::includeModule('iblock');
    Loader::IncludeModule("wl.snailshop");
    if (!\WL\SnailShop::userIsStaff()) {
        throw new Exception('Доступ запрещен');
    }
    $gridID = 'wl_purchasing_price';
    $grid_options = new Options($gridID);
    $nav_params = $grid_options->GetNavParams();
    $nav = new PageNavigation('request_list');

    $filterOption = new \Bitrix\Main\UI\Filter\Options($gridID);
    $filterData = $filterOption->getFilter([]);
    $currentOptions = $grid_options->getCurrentOptions();
    $sort = $grid_options->getSorting();
    $maxCount = (empty($currentOptions['page_size'])) ? 20 : $currentOptions['page_size'];
    if (isset($_REQUEST['export_excel']) && $_REQUEST['grid_id'] == $gridID) {
        $maxCount = 99999;
    }

    //Список брендов
    $brands = [];
    $filter = ['IBLOCK_ID' => BRANDS_IBLOCK_ID, 'ACTIVE' => 'Y'];
    if ($filterData['BRAND']) {
        $filter['=ID'] = $filterData['BRAND'];
    }
    $rows = CIBlockElement::GetList(['NAME' => 'ASC'], $filter, false, false, ['ID', 'NAME']);
    while ($row = $rows->fetch()) {
        $brands[$row['ID']] = $row;
    }
    $nav->allowAllRecords(true)->setPageSize($maxCount)->initFromUri();

    //Список всех товаров
    $elements = [];
    $elementsID = [];
    $filter = ['IBLOCK_ID' => GOODS_IBLOCK_ID];
    if ($filterData['ACTIVE']) {
        $filter['ACTIVE'] = $filterData['ACTIVE'];
    }
    if ($filterData['BRAND']) {
        $filter['PROPERTY_BRAND'] = array_keys($brands);
    }
    $rows = CIBlockElement::GetList(['NAME' => 'ASC'], $filter, false, ['nPageSize' => $maxCount, 'iNumPage' => $nav->getCurrentPage()], ['ID', 'NAME', 'PROPERTY_BRAND', 'ACTIVE', 'PURCHASING_PRICE', 'CATALOG_GROUP_1']);
    $rows->NavStart();
    $nav->setRecordCount($rows->NavRecordCount);

    while ($row = $rows->fetch()) {
        $row['BRAND'] = $brands[$row['PROPERTY_BRAND_VALUE']];
        if ($row['PURCHASING_PRICE'] > 0 && $row['CATALOG_PRICE_1'] > 0) {
            $row['MARGIN_RUB'] = $row['CATALOG_PRICE_1'] - $row['PURCHASING_PRICE'];
            $row['MARGIN_PERCENT'] = round(($row['CATALOG_PRICE_1'] - $row['PURCHASING_PRICE']) / $row['PURCHASING_PRICE'] * 100);
        }
        $elements[$row['ID']] = $row;
    }

    $arResult = [];
    foreach ($elements as $element) {
        $item = [
            'ID' => $element['ID'],
            'BRAND_ID' => $element['BRAND']['ID'],
            'ACTIVE' => $element['ACTIVE'] == 'Y' ? 'Да' : 'Нет',
            'BRAND' => $element['BRAND']['NAME'],
            'NAME' => $element['NAME'],
            'PURCHASING_PRICE' => CCurrencyLang::CurrencyFormat($element['PURCHASING_PRICE'], 'RUB'),
            'PRICE' => CCurrencyLang::CurrencyFormat($element['CATALOG_PRICE_1'], "RUB"),
            'MARGIN' => CCurrencyLang::CurrencyFormat($element['MARGIN_RUB'], "RUB"),
            'MARGIN_PERCENT' => $element['MARGIN_PERCENT'] . '%',
            'DISPLAY_PRICE' => '<div class="report-purchasing-price js-price-block" data-product-id="' . intval($element['ID']) . '" data-price-id="' . intval($element['CATALOG_PRICE_1']) . '"><input type="number" value="' . $element['CATALOG_PRICE_1'] . '" class="js-price main-grid-editor main-grid-editor-text" ><button class="main-grid-buttons save js-price-save" type="button" >OK</button></div>',
        ];
        $arResult['ITEMS'][] = $item;
    }

    $filter = [];
    $list = [];
    foreach ($brands as $brand) {
        $list[$brand['ID']] = $brand['NAME'];
    }
    $filter[] = ['id' => 'BRAND', 'name' => 'Бренд', 'type' => 'list', 'items' => $list, 'default' => true];
    $filter[] = ['id' => 'ACTIVE', 'name' => 'Активность', 'type' => 'list', 'items' => ['Y' => 'Да', 'N' => 'Нет'], 'default' => true];

    $arHeaders = array(
        ["id" => "BRAND", "name" => "Бренд", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "ACTIVE", "name" => "Активность", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "NAME", "name" => "Наименование", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "PURCHASING_PRICE", "name" => "Закупочная цена", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "PRICE", "name" => "Розничная цена", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "MARGIN", "name" => "Нацненка", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "MARGIN_PERCENT", "name" => "%", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "DISPLAY_PRICE", "name" => "Изменить", "sort" => false, "default" => true, "align" => "right"],
    );
    ?>
    <link rel="stylesheet" type="text/css" href="/local/modules/custom.reports/admin/wl_purchase_price/style.css">
    <div style="display: flex; align-items: center; justify-content: space-between">
        <? $APPLICATION->IncludeComponent(
            'bitrix:main.ui.filter',
            '',
            [
                'FILTER_ID' => $gridID,
                'GRID_ID' => $gridID,
                'FILTER' => $filter,
                'ENABLE_LIVE_SEARCH' => false,
                'ENABLE_LABEL' => true,
            ]
        ); ?>
        <div class="ui-btn-primary">
            <a class="ui-btn-main" style="color: white" download="wl_purchasing_price.xls"
                href="?export_excel=Y&grid_id=<?= $gridID ?>">Выгрузить</a>
        </div>
    </div>

    <?
    $list = [];
    foreach ($arResult['ITEMS'] as $item) {
        $list[] = [
            'data' => $item
        ];
    }

    $xlsColumns = [
        ["id" => "BRAND", "name" => "Бренд", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "ACTIVE", "name" => "Активность", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "NAME", "name" => "Наименование", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "PURCHASING_PRICE", "name" => "Закупочная цена", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "PRICE", "name" => "Розничная цена", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "MARGIN", "name" => "Нацненка", "sort" => false, "default" => true, "align" => "left"],
        ["id" => "MARGIN_PERCENT", "name" => "%", "sort" => false, "default" => true, "align" => "left"],
    ];

    if (isset($_REQUEST['export_excel']) && $_REQUEST['grid_id'] == $gridID) {
        $APPLICATION->RestartBuffer();
        Header("Content-Type: application/force-download");
        Header("Content-Type: application/octet-stream");
        Header("Content-Type: application/download");
        Header("Content-Disposition: attachment;filename=most_view_products.xls");
        Header("Content-Transfer-Encoding: binary");
        require_once 'export_table.php';
        exit();
    }

    ?>
    <div id="report_ajax">
        <? $APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
            'GRID_ID' => $gridID,
            'COLUMNS' => $arHeaders,
            'ROWS' => $list,
            'NAV_OBJECT' => $nav,
            'SHOW_ROW_CHECKBOXES' => false,
            'ALLOW_COLUMNS_SORT' => true,
            'ALLOW_SORT' => true,
            'SHOW_SELECTED_COUNTER' => false,
            'SHOW_TOTAL_COUNTER' => false,
            'ALLOW_PIN_HEADER' => true,
            "SHOW_GRID_SETTINGS_MENU" => true,
            "SHOW_NAVIGATION_PANEL" => true,
            "SHOW_PAGESIZE" => true,
            "SHOW_PAGINATION" => true,
            'PAGE_SIZES' => [
                ['NAME' => "5", 'VALUE' => '5'],
                ['NAME' => '10', 'VALUE' => '10'],
                ['NAME' => '20', 'VALUE' => '20'],
                ['NAME' => '50', 'VALUE' => '50'],
                ['NAME' => '100', 'VALUE' => '100'],
                ['NAME' => '200', 'VALUE' => '200']
            ],
            'AJAX_ID' => CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
            'AJAX_MODE' => 'Y',
        ]); ?>
    </div>
    <?
} catch (Exception $exception) {
    ShowError($exception->getMessage());
}
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>