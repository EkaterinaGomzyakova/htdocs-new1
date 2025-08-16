<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if (!CBXFeatures::IsFeatureEnabled('SaleReports')) {
    require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");

    ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");
require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle("Отчет по предельному количеству товаров");

$result = [];
\Bitrix\Main\Loader::includeModule('iblock');
$filter = [
    'IBLOCK_ID' => GOODS_IBLOCK_ID,
    '!PROPERTY_MIN_REMAINS' => false,
    'ACTIVE' => 'Y'
];
$select = ['ID', 'NAME', 'IBLOCK_ID', 'PROPERTY_MIN_REMAINS'];
$rows = CIBlockElement::GetList(['ID' => 'desc'], $filter, false, false, $select);

while ($row = $rows->fetch()) {
    if (empty($row['PROPERTY_MIN_REMAINS_VALUE'])) {
        $row['PROPERTY_MIN_REMAINS_VALUE'] = 0;
    }
    $result['items'][$row['ID']] = $row;
}

$products = \Bitrix\Catalog\ProductTable::getList([
    'filter' => ['ID' => array_column($result['items'], 'ID')],
    'select' => ['ID', 'QUANTITY']
]);
while ($row = $products->fetch()) {
    $item = $result['items'][$row['ID']];
    $result['items'][$row['ID']] = array_merge($row, $item);
}

$array_status_code = [];
$array_name = [];
$array_quantity = [];
$array_min_quantity = [];
foreach ($result['items'] as $key => &$item) {
    $item['status_code'] = 0;
    $item['status'] = '<span style="color: #259501">Достаточно</span>';

    if ($item['QUANTITY'] <= $item['PROPERTY_MIN_REMAINS_VALUE']) {
        $item['status_code'] = 100;
        $item['status'] = '<span style="color: #e34848">Требуется заказать</span>';
    } else {
        $nearQuantity = $item['PROPERTY_MIN_REMAINS_VALUE'] + $item['PROPERTY_MIN_REMAINS_VALUE'] / 100 * 20;
        if ($item['QUANTITY'] <= $nearQuantity) {
            $item['status_code'] = 50;
            $item['status'] = '<span style="color: #cbd524">Мало</span>';
        }
    }
    if($item['status_code'] == 0){
        unset($result['items'][$key]);
    }
    $array_status_code[$key] = $item['status_code'];
    $array_name[$key] = $item['NAME'];
    $array_quantity[$key] = $item['QUANTITY'];
    $array_min_quantity[$key] = $item['PROPERTY_MIN_REMAINS_VALUE'];
}
unset($item);

$grid_options = new Bitrix\Main\Grid\Options('wl_maximum_quantity_goods_grid');

$list = [];
foreach ($result['items'] as $item) {
    $list[] = [
        'data' => [
            'id' => $item['ID'],
            'name' => '<a href="/bitrix/admin/cat_product_edit.php?IBLOCK_ID=2&type=catalog&lang=ru&ID='.$item['ID'].'&find_section_section=-1&WF=Y">'.$item['NAME'].'</a>',
            'status' => $item['status'],
            'quantity' => $item['QUANTITY'],
            'min_quantity' => $item['PROPERTY_MIN_REMAINS_VALUE'],
        ]
    ];
}

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'wl_maximum_quantity_goods_grid',
    'COLUMNS' => [
        ['id' => 'id', 'name' => 'ID', 'align' => 'left', 'default' => true],
        ['id' => 'status', 'name' => 'Статус', 'sort' => 'status', 'align' => 'left', 'default' => true],
        ['id' => 'name', 'name' => 'Название', 'sort' => 'NAME', 'align' => 'left', 'default' => true],
        ['id' => 'quantity', 'name' => 'Количество на складе', 'sort' => 'quantity', 'align' => 'left', 'default' => true],
        ['id' => 'min_quantity', 'name' => 'Минимальный остаток', 'sort' => 'min_quantity', 'align' => 'left', 'default' => true],
    ],
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => false,
    'ALLOW_COLUMNS_SORT' => false,
    'ALLOW_SORT' => false,
    'SHOW_SELECTED_COUNTER' => false,
    'SHOW_TOTAL_COUNTER' => false,
    'ALLOW_PIN_HEADER' => true,
    "SHOW_GRID_SETTINGS_MENU" => true,
    "SHOW_NAVIGATION_PANEL" => false,
    "SHOW_PAGESIZE" => false,
    "SHOW_PAGINATION" => false,
    'AJAX_MODE' => 'Y',
    'AJAX_ID' => \CAjax::getComponentID('bitrix:main.ui.grid', '.default', ''),
    'PAGE_SIZES' => [
        ['NAME' => '10', 'VALUE' => '10'],
        ['NAME' => '20', 'VALUE' => '20'],
        ['NAME' => '50', 'VALUE' => '50'],
        ['NAME' => '100', 'VALUE' => '100'],
        ['NAME' => '200', 'VALUE' => '200'],
    ],
]);
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");


