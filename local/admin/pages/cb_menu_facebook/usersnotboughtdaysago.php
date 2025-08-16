<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
IncludeModuleLangFile(__FILE__);
$APPLICATION->SetTitle("Пользователи не совершавшие заказы N дней Facebook");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
\Bitrix\Main\Loader::includeModule('sale');
$gridID = 'cb_facebook_audience';
require_once 'tpl_filter.php';
$grid_options = new Bitrix\Main\Grid\Options($gridID);
$nav_params = $grid_options->GetNavParams();
$nav = new Bitrix\Main\UI\PageNavigation($gridID);

$currentOptions = $grid_options->getCurrentOptions();
$sort = $grid_options->getSorting();
$maxCount = (empty($currentOptions['page_size'])) ? 20 : $currentOptions['page_size'];

$nav->allowAllRecords(true)
    ->setPageSize($nav_params['nPageSize'])
    ->initFromUri();

$filterOption = new Bitrix\Main\UI\Filter\Options('fb_filter');

$filterData = $filterOption->getFilter([]);
$filter = [];
if (!empty($filterData['DATE_from'])) {
    $filter['>=LAST_ORDER_DATE'] = $filterData['DATE_from'];
}

if (!empty($filterData['DATE_to'])) {
    $filter['<=LAST_ORDER_DATE'] = $filterData['DATE_to'];
}

//Статистика продаж
$rsBuyerStatistic = \Bitrix\Sale\Internals\BuyerStatisticTable::getList([
    'select' => ['USER_ID', 'LAST_ORDER_DATE', 'SUM_PAID'],
    'count_total' => true,
    'filter' => $filter,
    'limit' => $nav->getLimit(),
    'offset' => $nav->getOffset()
]);
$nav->setRecordCount($rsBuyerStatistic->getCount());
$arBuyerStatistic = $rsBuyerStatistic->fetchAll();
$usersID = array_column($arBuyerStatistic, 'USER_ID');

//Свойства заказов
$arSaleProps = [];
$rows = \Bitrix\Sale\Internals\OrderPropsTable::getList([
    'filter' => ['CODE' => ['EMAIL', 'PHONE', 'ZIP', 'CITY', 'ADDRESS', 'LOCATION']],
    'select' => ['ID', 'CODE'],
]);

while ($item = $rows->fetch()) {
    $arSaleProps[$item['ID']] = $item;
}

//Профили пользователей
$rows = \Bitrix\Sale\Internals\UserPropsTable::getList([
    'filter' => ['USER_ID' => $usersID],
    'select' => ['ID', 'USER_ID'],
    'order' => ['ID' => 'ASC']
]);

//Берем последний профиль
$arUserProps = [];

while ($item = $rows->fetch()) {
    $arUserProps[$item['USER_ID']] = $item;
}


//Пользователи
$rows = \Bitrix\Main\UserTable::getList([
    'filter' => ["ID" => $usersID],
    'select' => ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'PERSONAL_PHONE']
]);
$arUsers = [];
while ($item = $rows->fetch()) {
    $arUsers[$item['ID']] = $item;
}


//Профили пользователей. Значение свойств
$arUserPropsValue = [];
$locationsCode = [];
$rows = \Bitrix\Sale\Internals\UserPropsValueTable::getList([
    'filter' => ['USER_PROPS_ID' => array_column($arUserProps, 'ID'), 'ORDER_PROPS_ID' => array_column($arSaleProps, 'ID')],
    'select' => ['*']
]);

while ($item = $rows->fetch()) {
    $prop = $arSaleProps[$item['ORDER_PROPS_ID']];
    $arUserPropsValue[] = $item;
    if ($prop['CODE'] == 'LOCATION' && !empty($item['VALUE'])) {
        $locationsCode[$item['VALUE']] = $item['VALUE'];
    }

}

foreach ($arBuyerStatistic as $key => $buyerStat) {
    if(empty($arUserProps[$buyerStat['USER_ID']])){
        $lastOrder = \Bitrix\Sale\Order::getList([
            'filter' => ['USER_ID' => $buyerStat['USER_ID']],
            'order'=> ['ID' => 'desc'],
            'limit' => 1,
            'select' => ['USER_ID', 'ID']
        ])->fetch();
        $location = \Bitrix\Sale\Internals\OrderPropsValueTable::getList([
            'filter' => ['ORDER_ID' => $lastOrder['ID'], 'CODE' => 'LOCATION'],
            'limit' => 1,
            'select' => ['*']
        ])->fetch();
        $arBuyerStatistic[$key]['USER_PROPS']['LOCATION'] = $location;
    }
}

$arLocations = [];
$rows = \Bitrix\Sale\Location\LocationTable::getList([
    'filter' => ['CODE' => $locationsCode],
    'select' => ['CODE', 'CITY_ID', 'REGION_ID']
]);
while ($item = $rows->fetch()) {
    $arLocations[$item['CODE']] = $item;
}

$zipLocations = [];
$rows = \Bitrix\Sale\Location\ExternalTable::getList([
    'filter' => ['LOCATION_ID' => array_column($arLocations, 'CITY_ID'), 'SERVICE_ID' => 2],
    'select' => ['*']
]);
while ($item = $rows->fetch()) {
    $zipLocations[$item['LOCATION_ID']] = $item;
}

$arCities = [];
$rows = \Bitrix\Sale\Location\Name\LocationTable::getList([
    'filter' => ['LOCATION_ID' => array_column($arLocations, 'CITY_ID')],
    'select' => ['LOCATION_ID', 'NAME']
]);
while ($item = $rows->fetch()) {
    $arCities[$item['LOCATION_ID']] = $item;
}

$arRegions = [];
$rows = \Bitrix\Sale\Location\Name\LocationTable::getList([
    'filter' => ['LOCATION_ID' => array_column($arLocations, 'REGION_ID')],
    'select' => ['LOCATION_ID', 'NAME']
]);
while ($item = $rows->fetch()) {
    $arRegions[$item['LOCATION_ID']] = $item;
}

foreach ($arLocations as &$location) {
    $location['REGION'] = $arRegions[$location['REGION_ID']];
    $location['CITY'] = $arCities[$location['CITY_ID']];
    $location['ZIP'] = $zipLocations[$location['CITY_ID']];
    if (empty($location['ZIP']) && !empty($location['CITY'])) {
        $dadata = new \Dadata\DadataClient(DADATA_API_KEY, DADATA_SECRET_KEY);
        $result = $dadata->clean("address", $location['CITY']['NAME']);
        if (!empty($result['postal_code'])) {
            $location['ZIP']['XML_ID'] = $result['postal_code'];
            $zipID = \Bitrix\Sale\Location\ExternalTable::add([
                'SERVICE_ID' => 2,
                'XML_ID' => $result['postal_code'],
                'LOCATION_ID' => $location['CITY']['LOCATION_ID']
            ]);
        }
    }
}

$arResult['ITEMS'] = [];
foreach ($arBuyerStatistic as $buyerStat) {
    $item = $buyerStat;
    $item['USER_PROFILE'] = $arUserProps[$buyerStat['USER_ID']];

    $item['USER'] = $arUsers[$buyerStat['USER_ID']];
    foreach ($arUserPropsValue as $propValue) {
        if ($propValue['USER_PROPS_ID'] == $item['USER_PROFILE']['ID']) {
            $prop = $arSaleProps[$propValue['ORDER_PROPS_ID']];
            $item['USER_PROPS'][$prop['CODE']] = $propValue;
        }

    }
    $arResult['ITEMS'][] = $item;
}

foreach ($arResult['ITEMS'] as $key => $item){
    foreach ($item['USER_PROPS'] as $code => $prop){
        if($code == 'LOCATION'){
            $arResult['ITEMS'][$key]['USER_PROPS'][$code]['LOCATION'] = $arLocations[$prop['VALUE']];
        }
    }
}

$columns = [
    ['id' => 'email', 'name' => 'E-Mail', 'align' => 'left', 'default' => true],
    ['id' => 'phone', 'name' => 'Телефон', 'align' => 'left', 'default' => true],
    ['id' => 'madid', 'name' => 'madid', 'align' => 'left', 'default' => true],
    ['id' => 'fn', 'name' => 'Имя', 'align' => 'left', 'default' => true],
    ['id' => 'ln', 'name' => 'Фамилия', 'align' => 'left', 'default' => true],
    ['id' => 'zip', 'name' => 'Почтовый индекс', 'align' => 'left', 'default' => true],
    ['id' => 'ct', 'name' => 'Город', 'align' => 'left', 'default' => true],
    ['id' => 'st', 'name' => 'Регион', 'align' => 'left', 'default' => true],
    ['id' => 'country', 'name' => 'Страна', 'align' => 'left', 'default' => true],
    ['id' => 'dob', 'name' => 'Дата рождения', 'align' => 'left', 'default' => true],
    ['id' => 'doby', 'name' => 'Год рождения', 'align' => 'left', 'default' => true],
    ['id' => 'gen', 'name' => 'Пол', 'align' => 'left', 'default' => true],
    ['id' => 'age', 'name' => 'Возраст', 'align' => 'left', 'default' => true],
    ['id' => 'uid', 'name' => 'uid', 'align' => 'left', 'default' => true],
    ['id' => 'value', 'name' => 'Сумма заказов', 'align' => 'left', 'default' => true],
    ['id' => 'date_order', 'name' => 'Дата последнего заказа', 'align' => 'left', 'default' => true],
];

$list = [];
foreach ($arResult['ITEMS'] as $item) {
    $city = $item['USER_PROPS']['LOCATION']['LOCATION']['CITY']['NAME'];
    if (empty($city)) {
        $city = $item['USER_PROPS']['CITY']['VALUE'];
    }

    $phone = $item['USER_PROPS']['PHONE']['VALUE'];
    if (empty($phone)) {
        $phone = $item['USER']['PERSONAL_PHONE'];
    }
    if (empty($phone)) {
        continue;
    }

    $zip = $item['USER_PROPS']['LOCATION']['LOCATION']['ZIP']['XML_ID'];
    if (empty($zip) && !empty($city)) {

    }

    $list[] = [
        'data' => [
            'email' => $item['USER_PROPS']['EMAIL']['VALUE'],
            'phone' => $phone,
            'madid' => '',
            'fn' => $item['USER']['NAME'],
            'ln' => $item['USER']['LAST_NAME'],
            'zip' => $item['USER_PROPS']['LOCATION']['LOCATION']['ZIP']['XML_ID'],
            'ct' => $city,
            'st' => $item['USER_PROPS']['LOCATION']['LOCATION']['REGION']['NAME'],
            'country' => 'RU',
            'dob' => '',
            'doby' => '',
            'gen' => 'F',
            'age' => '',
            'uid' => '',
            'value' => number_format($item['SUM_PAID'], 2, '.', ''),
            'date_order' => $item['LAST_ORDER_DATE']->format('d.m.Y')
        ],
        'actions' => []
    ];
}

if (isset($_REQUEST['export_csv'])) {
    $APPLICATION->RestartBuffer();
    $items = [];

    Header("Content-Type: application/force-download");
    Header("Content-Type: application/octet-stream");
    Header("Content-Type: application/download");
    Header("Content-Disposition: attachment;filename=fb_analytic.csv");
    Header("Content-Transfer-Encoding: binary");
    ob_start();
    $df = fopen("php://output", 'w');
    $csvHeader = array_column($columns, 'id');
    foreach ($csvHeader as $key => $header) {
        if ($header == 'date_order') {
            unset($csvHeader[$key]);
        }
    }
    fputcsv($df, $csvHeader, ';');
    foreach ($list as $item) {
        unset($item['data']['date_order']);

        $data = $item['data'];
        fputcsv($df, $data, ';');
    }
    fclose($df);
    echo ob_get_clean();
    exit();
}

$APPLICATION->IncludeComponent('bitrix:main.ui.grid', '', [
    'GRID_ID' => $gridID,
    'COLUMNS' => $columns,
    'ROWS' => $list,
    'SHOW_ROW_CHECKBOXES' => false,
    'ALLOW_COLUMNS_SORT' => true,
    'ALLOW_SORT' => true,
    'SHOW_SELECTED_COUNTER' => false,
    'SHOW_TOTAL_COUNTER' => false,
    'NAV_OBJECT' => $nav,
    'ALLOW_PIN_HEADER' => true,
    "SHOW_GRID_SETTINGS_MENU" => true,
    "SHOW_NAVIGATION_PANEL" => true,
    "SHOW_PAGESIZE" => true,
    "SHOW_PAGINATION" => true,
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