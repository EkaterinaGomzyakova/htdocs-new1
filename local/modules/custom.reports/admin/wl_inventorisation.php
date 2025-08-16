<?php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D") {
    $APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));
}

if (!CBXFeatures::IsFeatureEnabled('SaleReports')) {
    require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");

    ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
    die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");
require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle("Инвентаризация");
$arReportData = [];

define('REPORT_PRODUCT_TYPE_SIMPLE', 1);
define('REPORT_PRODUCT_TYPE_WITH_SKU', 3);
define('REPORT_PRODUCT_TYPE_SKU', 4);

$storages = [];
$dbResultStore = CCatalogStore::GetList([], ['ACTIVE' => 'Y'], false, false, ['ID', 'TITLE', 'IS_DEFAULT']);
while ($arStore = $dbResultStore->Fetch()) {
    $storages[] = $arStore;
}

$request = Bitrix\Main\Context::getCurrent()->getRequest();
$hideZeroAmount = $request->get('hideZeroAmount') === 'Y';
$showPictures = $request->get('hidePictures') !== 'Y';
$showProductId = $request->get('hideProductId') !== 'Y';

if (empty($_GET['groupBy'])) {
    $_GET['groupBy'] = 'section';
}

$groupBy = $request->get('groupBy') ?? 'none';
$arReportData = match ($groupBy) {
    'section' => getDataBySections(
        hideZeroAmount: $hideZeroAmount,
        showPictures  : $showPictures,
        showProductId : $showProductId
    ),
    'brand'   => getDataByBrand(
        hideZeroAmount: $hideZeroAmount,
        showPictures  : $showPictures,
        showProductId : $showProductId
    ),
    'none'    => getDataWithoutGroup(
        hideZeroAmount: $hideZeroAmount,
        showPictures  : $showPictures,
        showProductId : $showProductId
    )
};

?>
    <style>
      .main-table {
        background-color: white;
        border-collapse: collapse;
        width: 100%;
      }

      .main-table td,
      .main-table th {
        border: 1px solid gray;
        padding: 4px 8px;
      }

      tr.red td {
        background-color: coral;
      }

      fieldset {
        display: inline-block;
        margin-bottom: 5px;
        vertical-align: top;
      }

      img {
        max-width: 100px;
      }
    </style>

    <form action="<?= $APPLICATION->GetCurPage() ?>" method="GET">
        <fieldset>
            <legend>Общие:</legend>
            <div>
                <input type="checkbox"
                       id="hidePictures"
                       name="hidePictures"
                       value="Y"
                    <?= $request->get('hidePictures') === 'Y' ? 'checked' : '' ?>
                >
                <label for="showPictures">Скрывать картинки</label>
            </div>
            <div>
                <input type="checkbox" id="hideProductId" name="hideProductId"
                       value="Y" <?= $showProductId ? '' : 'checked' ?>>
                <label for="hideProductId">Скрывать ID</label>
            </div>
            <div>
                <input type="checkbox" id="hideZeroAmount" name="hideZeroAmount"
                       value="Y" <?= $hideZeroAmount ? 'checked' : '' ?>>
                <label for="hideZeroAmount">Скрывать нулевые остатки</label>
            </div>
        </fieldset>
        <fieldset>
            <legend>Группировать товары по:</legend>
            <div>
                <input type="radio" id="withoutGroup" name="groupBy"
                       value="none" <?= $groupBy === 'none' ? 'checked' : '' ?>>
                <label for="withoutGroup">Без группировки</label>
            </div>
            <div>
                <input type="radio" id="section" name="groupBy"
                       value="section" <?= $groupBy === 'section' ? 'checked' : '' ?>>
                <label for="section">Разделу</label>
            </div>
            <div>
                <input type="radio" id="brand" name="groupBy"
                       value="brand" <?= $groupBy === 'brand' ? 'checked' : '' ?>>
                <label for="brand">Бренду</label>
            </div>
        </fieldset>
        <br>
        <input type="submit" class="adm-btn adm-btn-save" value="Применить">
    </form>

    <table class="main-table">
        <tr>
            <?php if ($showPictures) { ?>
                <th>
                    Картинка
                </th>
            <?php } ?>
            <th>
                Раздел/Товар
            </th>
            <th>
                SKU
            </th>
            <?php foreach ($storages as $store) { ?>
                <th style="width: 50px;">
                    Склад <?= $store["TITLE"] ?>
                </th>
                <?php if ($store['IS_DEFAULT'] === 'Y') { ?>
                <th style="width: 50px;">
                    Склад <?= $store["TITLE"] ?> <small>(с&nbsp;резервом)</small>
                </th>
                <?php } ?>
            <?php } ?>
            <th style="width: 50px;">
                Резерв
            </th>
            <th style="width: 50px;">
                Доступный остаток
            </th>
            <th style="width: 50px;">
                Остаток <small>по данным из документов</small>
            </th>
            <th style="width: 50px;">
                Закупочная цена
            </th>
        </tr>
        <?php foreach ($arReportData as $section) { ?>
            <?php if ($section['NAME']) { ?>
                <tr>
                    <td colspan="7"><strong><?= $section["NAME"] ?></strong></td>
                </tr>
            <?php } ?>
            <?php foreach ($section["GOODS"] as $product) { ?>
                <tr <?php if ($product['TOTAL_QUANTITY'] != $product['QUANTITY'] + $product['QUANTITY_RESERVED']) {
                    echo 'class="red"';
                } ?>>
                    <?php if (in_array($product['TYPE'], [REPORT_PRODUCT_TYPE_SIMPLE, REPORT_PRODUCT_TYPE_SKU])) { ?>
                        <?php
                        $style = 'padding-left: 15px;';
                        if ($product['TYPE'] == REPORT_PRODUCT_TYPE_SKU) {
                            $style = "padding-left: 40px;";
                        }
                        ?>
                        <?php if ($showPictures) { ?>
                            <td style="<?= $style ?>">
                                <?php if (!empty($product['IMAGE'])) { ?>
                                    <img src="<?= $product['IMAGE'] ?>" loading="lazy">
                                <?php } ?>
                            </td>
                        <?php } ?>
                        <td>
                            <?= $product["PRODUCT_NAME"] ?>
                        </td>
                        <td>
                            <?= $product["SKU_NAME"] ?>
                        </td>
                        <?php foreach ($storages as $store) { ?>
                            <td>
                                <?php if (!empty($product["STORE_AMOUNT"][$store["ID"]])) {
                                    echo $product["STORE_AMOUNT"][$store["ID"]];
                                } else { ?>
                                    0
                                <?php } ?>
                            </td>
                            <?php if ($store['IS_DEFAULT'] === 'Y') { ?>
                                <td>
                                    <?php
                                    if (!empty($product["STORE_AMOUNT"][$store["ID"]])) {
                                        if ($product["QUANTITY_RESERVED"] > 0) {
                                            echo $product["STORE_AMOUNT"][$store["ID"]] - $product["QUANTITY_RESERVED"];
                                        } else {
                                            echo $product["STORE_AMOUNT"][$store["ID"]];
                                        }
                                    } else {
                                        echo '0';
                                    } ?>
                                </td>
                            <?php } ?>
                        <?php } ?>
                        <td>
                            <?= $product["QUANTITY_RESERVED"] ?>
                        </td>
                        <td>
                            <?= $product["QUANTITY"] ?>
                        </td>
                        <td>
                            <?= $product["TOTAL_QUANTITY"] ?>
                        </td>
                        <td>
                            <?= $product["PURCHASING_PRICE"] ?>
                        </td>
                    <?php } ?>
                </tr>
            <?php }
        } ?>
    </table>
<?php

function getDataBySections(bool $hideZeroAmount, bool $showPictures = true, bool $showProductId = true)
{
    $arReportData = [];
    $dbSections = CIBlockSection::GetList(["NAME" => "ASC"], ["IBLOCK_ID" => GOODS_IBLOCK_ID], false, ["ID"]);
    while ($section = $dbSections->Fetch()) {
        $dbSectionElements = CIBlockElement::GetList(["name" => "ASC"],
            ["ACTIVE" => "Y", "IBLOCK_SECTION_ID" => $section["ID"]],
            false,
            false,
            ["IBLOCK_ID", "ID", "NAME", 'PREVIEW_PICTURE']);

        $sectionData = [
            "NAME"  => $section["NAME"],
            "GOODS" => [],
        ];

        while ($dbElement = $dbSectionElements->Fetch()) {
            $goods = getRow(
                dbElement     : $dbElement,
                hideZeroAmount: $hideZeroAmount,
                showPictures  : $showPictures,
                showProductId : $showProductId
            );

            $sectionData["GOODS"] = array_merge($sectionData["GOODS"], $goods);
        }
        if (empty($sectionData["GOODS"])) {
            continue;
        }
        $arReportData[] = $sectionData;
    }
    return $arReportData;
}

function getDataByBrand(bool $hideZeroAmount, bool $showPictures = true, bool $showProductId = true)
{
    $arReportData = [];

    $dbBrands = CIBlockElement::GetList(['NAME' => 'ASC'],
        ['IBLOCK_ID' => BRANDS_IBLOCK_ID],
        false,
        false,
        ['ID', 'NAME']);
    while ($arBrand = $dbBrands->Fetch()) {
        $dbBrandElements = CIBlockElement::GetList(["NAME" => "ASC"],
            ["ACTIVE" => "Y", "PROPERTY_BRAND" => $arBrand["ID"]],
            false,
            false,
            ["IBLOCK_ID", "ID", "NAME", 'PREVIEW_PICTURE']);

        $sectionData = [
            "NAME"  => $arBrand["NAME"],
            "GOODS" => [],
        ];

        while ($dbElement = $dbBrandElements->Fetch()) {
            $goods = getRow(
                dbElement     : $dbElement,
                hideZeroAmount: $hideZeroAmount,
                showPictures  : $showPictures,
                showProductId : $showProductId
            );

            $sectionData["GOODS"] = array_merge($sectionData["GOODS"], $goods);
        }
        if (empty($sectionData["GOODS"])) {
            continue;
        }
        $arReportData[] = $sectionData;
    }

    return $arReportData;
}

/**
 * @param bool $hideZeroAmount
 * @param bool $showPictures
 *
 * @return array
 */
function getDataWithoutGroup(bool $hideZeroAmount, bool $showPictures = true, bool $showProductId = true): array
{
    $arReportData = [];
    $dbElements = CIBlockElement::GetList(["name" => "ASC"],
        ["ACTIVE" => "Y", "IBLOCK_ID" => GOODS_IBLOCK_ID],
        false,
        false,
        ["IBLOCK_ID", "ID", "NAME", 'PREVIEW_PICTURE']);
    $sectionData = [
        "NAME"  => null,
        "GOODS" => [],
    ];
    while ($dbElement = $dbElements->Fetch()) {
        $goods = getRow(
            dbElement     : $dbElement,
            hideZeroAmount: $hideZeroAmount,
            showPictures  : $showPictures,
            showProductId : $showProductId
        );

        $sectionData["GOODS"] = array_merge($sectionData["GOODS"], $goods);
    }
    $arReportData[] = $sectionData;
    return $arReportData;
}

function isAllZeros(array $arr): bool
{
    return array_filter($arr, fn($num) => $num !== 0) === [];
}

/**
 * @param array $dbElement
 * @param bool  $hideZeroAmount
 * @param bool  $showPictures
 *
 * @return array
 */
function getRow(array $dbElement, bool $hideZeroAmount, bool $showPictures = true, bool $showProductId = true): array
{
    $result = [];
    $arProduct = CCatalogProduct::GetByID($dbElement["ID"]);
    if ($arProduct['TYPE'] != REPORT_PRODUCT_TYPE_WITH_SKU) {
        $storeAmount = [];
        $storeAmountSum = 0;
        $arStoreProductFilter = [
            'PRODUCT_ID' => $dbElement['ID'],
        ];

        $dbStoreProduct = CCatalogStoreProduct::GetList([],
            $arStoreProductFilter,
            false,
            false,
            ['ID', 'AMOUNT', 'STORE_ID']);

        while ($arStoreProduct = $dbStoreProduct->Fetch()) {
            $storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
            $storeAmountSum += intval($arStoreProduct['AMOUNT']);
        }

        if ($hideZeroAmount && isAllZeros($storeAmount)) {
            return $result;
        }

        $productName = $dbElement["NAME"];
        if ($showProductId) {
            $productName .= ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>';
        }

        $product = [
            'IMAGE'             => $showPictures ? CFile::GetPath($dbElement['PREVIEW_PICTURE']) : null,
            "PRODUCT_NAME"      => $productName,
            "SKU_NAME"          => null,
            "QUANTITY"          => $arProduct["QUANTITY"],
            "QUANTITY_RESERVED" => $arProduct["QUANTITY_RESERVED"],
            "TOTAL_QUANTITY"    => $arProduct["QUANTITY"] + $arProduct["QUANTITY_RESERVED"],
            "STORE_AMOUNT_SUM"  => $storeAmountSum,
            "STORE_AMOUNT"      => $storeAmount,
            "TYPE"              => REPORT_PRODUCT_TYPE_SIMPLE,
            "PURCHASING_PRICE"  => $arProduct['PURCHASING_PRICE'],
        ];

        $result[] = $product;
    } else {
        $parentProduct = [
            "PRODUCT_NAME" => $dbElement["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>',
            "TYPE"         => REPORT_PRODUCT_TYPE_WITH_SKU,
        ];
        $result[] = $parentProduct;

        $dbSKU = CIBlockElement::GetList([],
            ['IBLOCK_ID' => SKU_IBLOCK_ID, 'PROPERTY_CML2_LINK' => $dbElement['ID']],
            false,
            false,
            ['ID', 'IBLOCK_ID', 'NAME', 'QUANTITY', 'QUANTITY_RESERVED', 'PREVIEW_PICTURE', 'PURCHASING_PRICE']);

        while ($arSKU = $dbSKU->Fetch()) {
            $storeAmount = [];
            $storeAmountSum = 0;

            $arStoreProductFilter = [
                'PRODUCT_ID' => $arSKU['ID'],
            ];

            $dbStoreProduct = CCatalogStoreProduct::GetList([],
                $arStoreProductFilter,
                false,
                false,
                ['ID', 'AMOUNT', 'STORE_ID']);

            while ($arStoreProduct = $dbStoreProduct->Fetch()) {
                $storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
                $storeAmountSum += intval($arStoreProduct['AMOUNT']);
            }

            if ($hideZeroAmount && isAllZeros($storeAmount)) {
                continue;
            }

            $productName = $dbElement["NAME"];
            if ($showProductId) {
                $productName .= ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>';
            }

            $skuName = $arSKU["NAME"];
            if ($showProductId) {
                $skuName .= ' <small style="color: #a7a7a7">ID: ' . $arSKU['ID'] . '</small>';
            }

            $product = [
                'IMAGE'             => $showPictures ? CFile::GetPath($arSKU["PREVIEW_PICTURE"]) : null,
                "PRODUCT_NAME"      => $productName,
                "SKU_NAME"          => $skuName,
                "QUANTITY"          => $arSKU["QUANTITY"],
                "QUANTITY_RESERVED" => $arSKU["QUANTITY_RESERVED"],
                "TOTAL_QUANTITY"    => $arSKU["QUANTITY"] + $arSKU["QUANTITY_RESERVED"],
                "STORE_AMOUNT_SUM"  => $storeAmountSum,
                "STORE_AMOUNT"      => $storeAmount,
                "TYPE"              => REPORT_PRODUCT_TYPE_SKU,
                "PURCHASING_PRICE"  => $arSKU['PURCHASING_PRICE'],
            ];
            $result[] = $product;
        }
    }

    return $result;
}

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>