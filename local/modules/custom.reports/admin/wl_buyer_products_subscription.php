<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

use Bitrix\Catalog\SubscribeTable;

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");


define('REPORT_PRODUCT_TYPE_SIMPLE', 1);
define('REPORT_PRODUCT_TYPE_WITH_SKU', 3);
define('REPORT_PRODUCT_TYPE_SKU', 4);


$APPLICATION->SetTitle("Подписки покупателей");


// параметры
$daysFrom = (int)($_GET['daysFrom'] ?: 0);
$daysTo = (int)($_GET['daysTo'] ?: 0);
$daysTo = $daysTo > $daysFrom ? $daysTo : 0;
$groupBy = $_GET['groupBy'] ?: 'section';


// список складов
$stores = [];
$defaultStoreId = 0;
$defaultStoreTitle = '';
$dbResultStore = CCatalogStore::GetList(
    [],
    ['ACTIVE' => 'Y'],
    false,
    false,
    ['ID', 'TITLE', 'IS_DEFAULT']
);
while ($arStore = $dbResultStore->Fetch()) {
    $storeId = (int)$arStore['ID'];
    if ($arStore['IS_DEFAULT'] === 'Y') {
        $defaultStoreId = $storeId;
        $defaultStoreTitle = $arStore['TITLE'];
    } else {
        $stores[$storeId] = $arStore['TITLE'];
    }
}


// подписки покупателей
$itemsSubscribed = [];
$dateFilter = [];
if ($daysTo) {
    $dateFilter['><DATE_FROM'] = [
        ConvertTimeStamp(time() - 86400 * $daysTo, "FULL"),
        ConvertTimeStamp(time() - 86400 * $daysFrom, "FULL"),
    ];
} elseif ($daysFrom) {
    $dateFilter['<DATE_FROM'] = ConvertTimeStamp(time() - 86400 * $daysFrom, "FULL");
}
$dbList = SubscribeTable::getList([
    'select' => ['ITEM_ID'],
    'filter' => $dateFilter,
]);
while ($arItem = $dbList->Fetch()) {
    $itemId = (int)$arItem['ITEM_ID'];
    if (!isset($itemsSubscribed[$itemId])) {
        $itemsSubscribed[$itemId] = 1;
    } else {
        $itemsSubscribed[$itemId]++;
    }
}


$arReportData = [];

// группировка по разделам
if ($groupBy == 'section') {

    // Разделы
    $dbSections = CIBlockSection::GetList(
        ["NAME" => "ASC"],
        ["IBLOCK_ID" => GOODS_IBLOCK_ID],
        false,
        ["ID"]
    );
    while ($section = $dbSections->Fetch()) {

        // товары из раздела
        $dbSectionElements = CIBlockElement::GetList(
            ["name" => "ASC"],
            [
                "ACTIVE" => "Y",
                "SECTION_ID" => $section["ID"]
            ],
            false,
            false,
            ["IBLOCK_ID", "ID", "NAME"]
        );

        $goods = makeReportByGroup($dbSectionElements, $itemsSubscribed, $stores, $defaultStoreId);

        if (count($goods)) {
            $arReportData[] = [
                "NAME" => $section["NAME"],
                "GOODS" => $goods,
            ];
        }
    }
}

// группировка по брендам
if ($groupBy == 'brand') {

    // бренды
    $dbBrands = CIBlockElement::GetList(
        ['NAME' => 'ASC'],
        ['IBLOCK_ID' => BRANDS_IBLOCK_ID],
        false,
        false,
        ['ID', 'NAME']
    );
    while ($arBrand = $dbBrands->Fetch()) {

        // товары брендов
        $dbBrandElements = CIBlockElement::GetList(
            ["NAME" => "ASC"],
            [
                "ACTIVE" => "Y",
                "PROPERTY_BRAND" => $arBrand["ID"]
            ],
            false,
            false,
            ["IBLOCK_ID", "ID", "NAME", 'PREVIEW_PICTURE']
        );

        $goods = makeReportByGroup($dbBrandElements, $itemsSubscribed, $stores, $defaultStoreId);

        if (count($goods)) {
            $arReportData[] = [
                "NAME" => $arBrand["NAME"],
                "GOODS" => $goods,
            ];
        }
    }
}

/**
 * Получение отчета по группе с коллекцией товаров
 * @param CIBlockResult $dbGroupElements - колекция товаров
 * @param array $itemsSubscribed - массив ключ-значение по ID число подписок на товары
 * @param array $stores - массив ключ-значение по ID название склада
 * @param int $defaultStoreId - ID склада по умолчанию
 * @return array
 */
function makeReportByGroup (CIBlockResult $dbGroupElements, array $itemsSubscribed, array $stores, int $defaultStoreId) : array{
    $itemsSubscribedKeys = array_keys($itemsSubscribed);
    $storeKeys = array_keys($stores);

    $result = [];

    while ($arGroupElement = $dbGroupElements->Fetch()) {

        // ID товара
        $productId = (int)$arGroupElement["ID"];
        // тип товара
        $productType = CCatalogProduct::GetByID($productId)['TYPE'];

        // обычный товар, без SKU
        if ($productType != REPORT_PRODUCT_TYPE_WITH_SKU && in_array($productId, $itemsSubscribedKeys)) {
            $dbStoreProduct = CCatalogStoreProduct::GetList(
                [],
                ['PRODUCT_ID' => $productId],
                false,
                false,
                ['AMOUNT', 'STORE_ID']
            );
            $defaultStoreAmount = 0;
            $storeAmount = array_fill_keys($storeKeys, 0);
            while ($arStoreProduct = $dbStoreProduct->Fetch()) {
                $storeId = (int)$arStoreProduct['STORE_ID'];
                if ($storeId == $defaultStoreId) {
                    $defaultStoreAmount += (int)$arStoreProduct['AMOUNT'];
                } else {
                    $storeAmount[$storeId] += (int)$arStoreProduct['AMOUNT'];
                }
            }
            if (array_sum($storeAmount) && !$defaultStoreAmount) {
                $result[] = [
                    "PRODUCT_NAME" => $arGroupElement["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $productId . '</small>',
                    "SUBSCRIBED_COUNT" => $itemsSubscribed[$productId],
                    "STORE_AMOUNT" => $storeAmount,
                    "TYPE" => REPORT_PRODUCT_TYPE_SIMPLE
                ];
            }
        }

        // товар со SKU
        if ($productType == REPORT_PRODUCT_TYPE_WITH_SKU) {
            $SkuProduct = [];
            $dbSKU = CIBlockElement::GetList(
                [],
                [
                    'IBLOCK_ID' => SKU_IBLOCK_ID,
                    'PROPERTY_CML2_LINK' => $productId,
                ],
                false,
                false,
                ['ID', 'IBLOCK_ID', 'NAME']
            );
            while ($arSKU = $dbSKU->Fetch()) {

                // ID SKU
                $skuId = (int)$arSKU["ID"];

                if (!in_array($skuId, $itemsSubscribedKeys)) {
                    continue;
                }

                $dbStoreProduct = CCatalogStoreProduct::GetList(
                    [],
                    ['PRODUCT_ID' => $skuId],
                    false,
                    false,
                    ['AMOUNT', 'STORE_ID']
                );
                $defaultStoreAmount = 0;
                $storeAmount = array_fill_keys($storeKeys, 0);
                while ($arStoreProduct = $dbStoreProduct->Fetch()) {
                    $storeId = (int)$arStoreProduct['STORE_ID'];
                    if ($storeId == $defaultStoreId) {
                        $defaultStoreAmount += (int)$arStoreProduct['AMOUNT'];
                    } else {
                        $storeAmount[$storeId] += (int)$arStoreProduct['AMOUNT'];
                    }
                }
                if (array_sum($storeAmount) && !$defaultStoreAmount) {
                    $SkuProduct[] = [
                        "PRODUCT_NAME" => $arSKU["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $skuId . '</small>',
                        "SUBSCRIBED_COUNT" => $itemsSubscribed[$skuId],
                        "STORE_AMOUNT" => $storeAmount,
                        "TYPE" => REPORT_PRODUCT_TYPE_SKU
                    ];
                }
            }
            if (count($SkuProduct)) {
                $result = array_merge(
                    $result,
                    [[
                        "PRODUCT_NAME" => $arGroupElement['NAME'] . ' <small style="color: #a7a7a7">ID: ' . $productId . '</small>',
                        "TYPE" => REPORT_PRODUCT_TYPE_WITH_SKU,
                    ]],
                    $SkuProduct
                );
            }
        }
    }
    return $result;
}

?>
    <style>
        .main-table {
            background-color: white;
            border-collapse: collapse;
            width: 100%;
        }

        .main-table td, .main-table th {
            border: 1px solid gray;
            padding: 4px 8px;
        }

        fieldset {
            display: block;
            float: left;
            width: 160px;
            height: 60px;
        }

        form br {
            clear: both;
        }

        form input[type="number"] {
            width: 130px;
        }
    </style>

    <form action="<?= $APPLICATION->GetCurPage() ?>" method="GET">
        <fieldset>
            <legend>Группировать товары по:</legend>
            <div>
                <input type="radio" id="section" name="groupBy"
                       value="section" <?= $groupBy == 'section' ? 'checked' : '' ?>>
                <label for="section">Разделу</label>
            </div>
            <div>
                <input type="radio" id="brand" name="groupBy"
                       value="brand" <?= $groupBy == 'brand' ? 'checked' : '' ?>>
                <label for="brand">Бренду</label>
            </div>
        </fieldset>
        <fieldset>
            <legend>Кол-во дней ожидания:</legend>
            <div>
                <label for="daysFrom">От</label>
                <input type="number" id="daysFrom" name="daysFrom"
                       value="<?= $daysFrom ?>">
                <label for="daysTo">До</label>
                <input type="number" id="daysTo" name="daysTo"
                       value="<?= $daysTo ?>">
            </div>
        </fieldset>
        <br><br>
        <input type="submit" class="adm-btn adm-btn-save" value="Применить">
    </form>

    <h4>Отчет предоставляет сводку подписок покупателей на товары, если товара нет на складе по умолчанию
        (<?= $defaultStoreTitle ?>), но есть на других складах</h4>
    <table class="main-table">
        <tr>
            <th>
                Раздел/Товар
            </th>
            <th style="width:70px">
                Число<br>подписок
            </th>
            <? foreach ($stores as $store) { ?>
                <th style="width: 50px;">
                    Склад<br>
                    <nobr><?= $store ?></nobr>
                </th>
            <? } ?>
        </tr>
        <?php foreach ($arReportData as $section) { ?>
            <tr>
                <td colspan="<?= count($stores) + 2 ?>"><strong><?= $section["NAME"] ?></strong></td>
            </tr>
            <?php foreach ($section["GOODS"] as $product) { ?>
                <tr>
                    <? if (in_array($product['TYPE'], [REPORT_PRODUCT_TYPE_SIMPLE, REPORT_PRODUCT_TYPE_SKU])) { ?>
                        <td style="padding-left: <?= $product['TYPE'] == REPORT_PRODUCT_TYPE_SKU ? 40 : 15 ?>px;">
                            <?= $product["PRODUCT_NAME"] ?>
                        </td>
                        <td>
                            <?= $product["SUBSCRIBED_COUNT"] ?>
                        </td>
                        <? foreach (array_keys($stores) as $storeId) { ?>
                            <td>
                                <?= $product["STORE_AMOUNT"][$storeId] ?>
                            </td>
                        <? } ?>
                        </td>
                    <? } else { ?>
                        <td colspan="<?= count($stores) + 2 ?>" style="padding-left: 15px;">
                            <?= $product["PRODUCT_NAME"] ?>
                        </td>
                    <? } ?>

                </tr>
            <?php }
        } ?>
    </table>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>