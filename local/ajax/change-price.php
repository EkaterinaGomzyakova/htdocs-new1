<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
CModule::IncludeModule("wl.snailshop");
if (!\WL\SnailShop::userIsStaff()) {
    throw new Exception('Доступ запрещен');
}

$price = \Bitrix\Catalog\PriceTable::getList([
    'filter' => [
        'ID' => $_REQUEST['price_id'],
        'CATALOG_GROUP_ID' => 1
    ],
    'select' => ['PRODUCT_ID', 'PRICE', 'ID']
])->fetch();

if (empty($price)) {
    \Bitrix\Catalog\Model\Price::add(["PRODUCT_ID" => $_REQUEST['product_id'], "CATALOG_GROUP_ID" => 1, "PRICE" => $_REQUEST['value'], "CURRENCY" => "RUB", "PRICE_SCALE" => $_REQUEST['value']]);
} else {
    \Bitrix\Catalog\Model\Price::update($_REQUEST['price_id'], ['PRICE' => $_REQUEST['value']]);
}

echo \Bitrix\Main\Web\Json::encode(['success' => true]);
