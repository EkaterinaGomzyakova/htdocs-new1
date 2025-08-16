<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");
require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");
$APPLICATION->SetTitle("Дисбаланс товаров на складах");

define('REPORT_PRODUCT_TYPE_SIMPLE', 1);
define('REPORT_PRODUCT_TYPE_WITH_SKU', 3);
define('REPORT_PRODUCT_TYPE_SKU', 4);

$arReportData = [];

if (empty($_GET['groupBy'])) {
    $_GET['groupBy'] = 'section';
}

if ($_GET['groupBy'] == 'section') {
    $arReportData = getDataBySections();
} elseif ($_GET['groupBy'] == 'brand') {
    $arReportData = getDataByBrand();
}

// список складов
$dbResultStore = CCatalogStore::GetList(
	[],
	['ACTIVE' => 'Y'],
	false,
	false,
	['ID', 'TITLE', 'IS_DEFAULT']
);
$storages = [];
$initialStoreAmount = [];
while ($store = $dbResultStore->Fetch()) {
	$storages[] = $store;
	$initialStoreAmount[$store['ID']] = 0;
}
$storeCount = count($storages);
$tableColCount = $storeCount + 1;
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
		display: inline-block;
	}
</style>

<form action="<?= $APPLICATION->GetCurPage() ?>" method="GET">
	<fieldset>
		<legend>Группировать товары по:</legend>
		<div>
			<input type="radio" id="section" name="groupBy" value="section" <?= $_GET['groupBy'] == 'section' ? 'checked' : '' ?>>
			<label for="section">Разделу</label>
		</div>
		<div>
			<input type="radio" id="brand" name="groupBy" value="brand" <?= $_GET['groupBy'] == 'brand' ? 'checked' : '' ?>>
			<label for="brand">Бренду</label>
		</div>
		<br>
		<input type="submit" class="adm-btn adm-btn-save" value="Применить">
	</fieldset>
</form>

<h4>Отчет предоставляет сводку наличия товаров на складах, если товар есть хотя бы на одном из складов и также его
	нету хотя бы на одном из складов</h4>
<table class="main-table">
	<tr>
		<th>
			Раздел/Товар
		</th>
		<th>
			Резерв
		</th>
		<? foreach ($storages as $store) { ?>
			<th style="width: 50px;">
				Склад <?= $store["TITLE"] ?>
			</th>
		<? } ?>
	</tr>
	<?php foreach ($arReportData as $section) { ?>
		<tr>
			<td colspan="<?= $tableColCount ?>"><strong><?= $section["NAME"] ?></strong></td>
		</tr>
		<?php foreach ($section["GOODS"] as $product) { ?>
			<tr>
				<? if (in_array($product['TYPE'], [REPORT_PRODUCT_TYPE_SIMPLE, REPORT_PRODUCT_TYPE_SKU])) { ?>
					<?
					$style = 'padding-left: 15px;';
					if ($product['TYPE'] == REPORT_PRODUCT_TYPE_SKU) {
						$style = "padding-left: 40px;";
					}
					?>

					<td style="<?= $style ?>">
						<?= $product["PRODUCT_NAME"] ?>
					</td>
					<td>
						<?= $product['QUANTITY_RESERVED'] ?>
					</td>
					<? foreach ($storages as $store) { ?>
						<td>
							<? if($store['IS_DEFAULT'] == 'Y' &&  $product["QUANTITY_RESERVED"] > 0) {?>
								<nobr><?= $product["STORE_AMOUNT"][$store["ID"]] ?> <small>(С резервом: <?= $product["STORE_AMOUNT"][$store["ID"]] - $product["QUANTITY_RESERVED"] ?>)</small></nobr>
							<? } else { ?>
								<?= $product["STORE_AMOUNT"][$store["ID"]] ?>
							<? } ?>
						</td>
					<? } ?>
					</td>
				<? } else { ?>
					<td colspan="<?= $tableColCount ?>" style="padding-left: 15px;">
						<?= $product["PRODUCT_NAME"] ?>
					</td>
				<? } ?>

			</tr>
		<?php }
	} ?>
</table>

<?
function getDataBySections() {
	$arReportData = [];

	$dbResultStore = CCatalogStore::GetList(
		[],
		['ACTIVE' => 'Y'],
		false,
		false,
		['ID', 'TITLE', 'IS_DEFAULT']
	);
	$storages = [];
	$initialStoreAmount = [];
	while ($store = $dbResultStore->Fetch()) {
		$storages[] = $store;
		$initialStoreAmount[$store['ID']] = 0;
	}

	$dbSections = CIBlockSection::GetList(
		["NAME" => "ASC"],
		["IBLOCK_ID" => GOODS_IBLOCK_ID],
		false,
		["ID"]
	);
	while ($section = $dbSections->Fetch()) {
		$sectionData = [
			"NAME" => $section["NAME"],
			"GOODS" => [],
		];
	
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
		while ($dbElement = $dbSectionElements->Fetch()) {
	
			// свойства товара
			$arProduct = CCatalogProduct::GetByID($dbElement["ID"]);
	
			if ($arProduct['TYPE'] != REPORT_PRODUCT_TYPE_WITH_SKU) {
				$dbStoreProduct = CCatalogStoreProduct::GetList(
					[],
					[
						'PRODUCT_ID' => $dbElement['ID'],
						'>AMOUNT' => 0,
					],
					false,
					false,
					['ID', 'AMOUNT', 'STORE_ID']
				);
				$dbStoreProductCount = $dbStoreProduct->SelectedRowsCount();

				if ($dbStoreProductCount > 0 && $dbStoreProductCount < count($storages)) {
					$storeAmount = $initialStoreAmount;
					while ($arStoreProduct = $dbStoreProduct->Fetch())
						$storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
	
					$product = [
						"PRODUCT_NAME" => $dbElement["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>',
						"STORE_AMOUNT" => $storeAmount,
						'IS_DEFAULT_STORE' => $storages[$arStoreProduct['STORE_ID']]['IS_DEFAULT'],
						"TYPE" => REPORT_PRODUCT_TYPE_SIMPLE,
						'QUANTITY_RESERVED' => $arProduct['QUANTITY_RESERVED']
					];
					$sectionData["GOODS"][] = $product;
				}
			} else {
				$SkuProduct = [];
				$dbSKU = CIBlockElement::GetList(
					[],
					[
						'IBLOCK_ID' => SKU_IBLOCK_ID,
						'PROPERTY_CML2_LINK' => $dbElement['ID']
					],
					false,
					false,
					['ID', 'IBLOCK_ID', 'NAME']
				);
				while ($arSKU = $dbSKU->Fetch()) {
	
					$dbStoreProduct = CCatalogStoreProduct::GetList(
						[],
						[
							'PRODUCT_ID' => $arSKU['ID'],
							'>AMOUNT' => 0,
						],
						false,
						false,
						['ID', 'AMOUNT', 'STORE_ID']
					);

					$dbStoreProductCount = $dbStoreProduct->SelectedRowsCount();
					if ($dbStoreProductCount > 0 && $dbStoreProductCount < count($storages)) {
						$storeAmount = $initialStoreAmount;
						while ($arStoreProduct = $dbStoreProduct->Fetch())
							$storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
	
						$SkuProduct[] = [
							"PRODUCT_NAME" => $arSKU["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $arSKU['ID'] . '</small>',
							"STORE_AMOUNT" => $storeAmount,
							'IS_DEFAULT_STORE' => $storages[$arStoreProduct['STORE_ID']]['IS_DEFAULT'],
							"TYPE" => REPORT_PRODUCT_TYPE_SKU,
							'QUANTITY_RESERVED' => $arProduct['QUANTITY_RESERVED']
						];
					}
				}
				if (count($SkuProduct)) {
					$sectionData["GOODS"] = array_merge(
						$sectionData["GOODS"],
						[[
							"PRODUCT_NAME" => $dbElement['NAME'] . ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>',
							"TYPE" => REPORT_PRODUCT_TYPE_WITH_SKU,
						]],
						$SkuProduct
					);
				}
			}
		}

		if (count($sectionData["GOODS"])) {
			$arReportData[] = $sectionData;
		}
	}

	return $arReportData;
}

function getDataByBrand() {
	$arReportData = [];

	$dbResultStore = CCatalogStore::GetList(
		[],
		['ACTIVE' => 'Y'],
		false,
		false,
		['ID', 'TITLE', 'IS_DEFAULT']
	);
	$storages = [];
	$initialStoreAmount = [];
	while ($store = $dbResultStore->Fetch()) {
		$storages[] = $store;
		$initialStoreAmount[$store['ID']] = 0;
	}


    $dbBrands = CIBlockElement::GetList(['NAME' => 'ASC'], ['IBLOCK_ID' => BRANDS_IBLOCK_ID], false, false, ['ID', 'NAME']);
    while ($arBrand = $dbBrands->Fetch()) {
		$dbBrandElements = CIBlockElement::GetList(["NAME" => "ASC"], ["ACTIVE" => "Y", "PROPERTY_BRAND" => $arBrand["ID"]], false, false, ["IBLOCK_ID", "ID", "NAME", 'PREVIEW_PICTURE']);

        $sectionData = [
            "NAME" => $arBrand["NAME"],
            "GOODS" => [],
        ];

        while ($dbElement = $dbBrandElements->Fetch()) {

			$arProduct = CCatalogProduct::GetByID($dbElement["ID"]);
	
			if ($arProduct['TYPE'] != REPORT_PRODUCT_TYPE_WITH_SKU) {
				$dbStoreProduct = CCatalogStoreProduct::GetList(
					[],
					[
						'PRODUCT_ID' => $dbElement['ID'],
						'>AMOUNT' => 0,
					],
					false,
					false,
					['ID', 'AMOUNT', 'STORE_ID']
				);
				$dbStoreProductCount = $dbStoreProduct->SelectedRowsCount();
				if ($dbStoreProductCount > 0 && $dbStoreProductCount < count($storages)) {
					$storeAmount = $initialStoreAmount;
					while ($arStoreProduct = $dbStoreProduct->Fetch())
						$storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
	
					$product = [
						"PRODUCT_NAME" => $dbElement["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>',
						"STORE_AMOUNT" => $storeAmount,
						'IS_DEFAULT_STORE' => $storages[$arStoreProduct['STORE_ID']]['IS_DEFAULT'],
						"TYPE" => REPORT_PRODUCT_TYPE_SIMPLE,
						'QUANTITY_RESERVED' => $arProduct['QUANTITY_RESERVED']
					];
					$sectionData["GOODS"][] = $product;
				}
			} else {
				$SkuProduct = [];
				$dbSKU = CIBlockElement::GetList(
					[],
					[
						'IBLOCK_ID' => SKU_IBLOCK_ID,
						'PROPERTY_CML2_LINK' => $dbElement['ID']
					],
					false,
					false,
					['ID', 'IBLOCK_ID', 'NAME']
				);
				while ($arSKU = $dbSKU->Fetch()) {
	
					$dbStoreProduct = CCatalogStoreProduct::GetList(
						[],
						[
							'PRODUCT_ID' => $arSKU['ID'],
							'>AMOUNT' => 0,
						],
						false,
						false,
						['ID', 'AMOUNT', 'STORE_ID']
					);
					$dbStoreProductCount = $dbStoreProduct->SelectedRowsCount();
					if ($dbStoreProductCount > 0 && $dbStoreProductCount < count($storages)) {
						$storeAmount = $initialStoreAmount;
						while ($arStoreProduct = $dbStoreProduct->Fetch())
							$storeAmount[$arStoreProduct['STORE_ID']] += intval($arStoreProduct['AMOUNT']);
	
						$SkuProduct[] = [
							"PRODUCT_NAME" => $arSKU["NAME"] . ' <small style="color: #a7a7a7">ID: ' . $arSKU['ID'] . '</small>',
							"STORE_AMOUNT" => $storeAmount,
							'IS_DEFAULT_STORE' => $storages[$arStoreProduct['STORE_ID']]['IS_DEFAULT'],
							"TYPE" => REPORT_PRODUCT_TYPE_SKU,
							'QUANTITY_RESERVED' => $arProduct['QUANTITY_RESERVED']
						];
					}
				}
				if (count($SkuProduct)) {
					$sectionData["GOODS"] = array_merge(
						$sectionData["GOODS"],
						[[
							"PRODUCT_NAME" => $dbElement['NAME'] . ' <small style="color: #a7a7a7">ID: ' . $dbElement['ID'] . '</small>',
							"TYPE" => REPORT_PRODUCT_TYPE_WITH_SKU,
						]],
						$SkuProduct
					);
				}
			}
		}

		if (count($sectionData["GOODS"])) {
			$arReportData[] = $sectionData;
		}
	}

	return $arReportData;
}
?>
<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>