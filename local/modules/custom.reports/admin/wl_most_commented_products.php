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
$APPLICATION->SetTitle("Самые комментируемые товары");

$result = [];
\Bitrix\Main\Loader::includeModule('iblock');


$filter = [
    'IBLOCK_ID' => REVIEWS_IBLOCK_ID,
    'ACTIVE' => "Y",
];

$select = ['ID', 'IBLOCK_ID', 'PROPERTY_OBJECT'];
$rows = CIBlockElement::GetList(['ID' => 'desc'], $filter, false, false, $select);

while ($row = $rows->fetch()) {
    $result['items'][$row["PROPERTY_OBJECT_VALUE"]]['COUNT'] += 1;
}

$arProducts = [];
$dbProducts = CIBlockElement::Getlist([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, '=ID' => array_keys($result['items'])], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
while($arProduct = $dbProducts->Fetch()) {
    $result['items'][$arProduct['ID']]['NAME'] = $arProduct['NAME'];
    $result['items'][$arProduct['ID']]['ID'] = $arProduct['ID'];
}

uasort($result['items'], function($a, $b) {
    return $a['COUNT'] <=> $b['COUNT'];
});

$array_name = [];
$array_count = [];
foreach ($result['items'] as $key => &$item) {
    $array_name[$item['ID']] = $item['NAME'];
    $array_count[$item['ID']] = $item['COUNT'];
}
unset($item);



$grid_options = new Bitrix\Main\Grid\Options('wl_most_commented_products_grid');
$sort = $grid_options->getSorting();
$sort_order = SORT_ASC;
if (current($sort['sort']) == 'desc') {
    $sort_order = SORT_DESC;
}
if (isset($sort['sort']['NAME'])) {
    array_multisort($array_name, $sort_order, $result['items']);
} elseif (isset($sort['sort']['count'])) {
    array_multisort($array_count, $sort_order, $result['items']);
} else {
    array_multisort($array_status_code, SORT_DESC, $array_name, SORT_ASC, $result['items']);
}

$list = [];
foreach ($result['items'] as $item) {
    $url = CIBlock::GetAdminElementEditLink(GOODS_IBLOCK_ID, $item["ID"]);
    $list[] = [
        'data' => [
            'name' => '<a href="'. $url.'">'.$item['NAME'].'</a>',
            'count' => $item['COUNT'],
        ]
    ];
}

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => 'wl_most_commented_products_grid',
    'COLUMNS' => [
        ['id' => 'name', 'name' => 'Название', 'sort' => 'NAME', 'align' => 'left', 'default' => true],
        ['id' => 'count', 'name' => 'Количество комментариев', 'sort' => 'count', 'align' => 'left', 'default' => true],
    ],
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => false,
    'ALLOW_COLUMNS_SORT' => true,
    'ALLOW_SORT' => true,
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


