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

$sTableID = "tbl_products_quantity_v1";

\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');


$oSort = new CAdminSorting($sTableID, "ID", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = [
	"filter_product_name",
	"filter_product_barcode",
	"filter_product_section_id",
	"filter_date_document_from",
	"filter_date_document_to",
	"filter_hide_doc",
	'filter_store_id'
];

if ($lAdmin->IsDefaultFilter()) {
	$filter_product_name = "";
	$filter_product_barcode = "";
}

$lAdmin->InitFilter($arFilterFields);

if (trim($filter_date_document_from) != '') {
	$date_document_from = $filter_date_document_from . ' 00:00:00';
	$date_document_from_ts = strtotime($date_document_from);
} else {
	$date_document_from_ts = strtotime('01.01.2014 00:00:00');
}

if (trim($filter_date_document_to) != '') {
	$date_document_to = $filter_date_document_to . ' 23:59:59';
	$date_document_to_ts = strtotime($date_document_to);
} else {
	$date_document_to_ts = time();
}

$arResult = [];
$arStores = [];
$rsStore = CCatalogStore::GetList([], ['ACTIVE' => 'Y']);
if (!empty($filter_store_id)) {
	$selectedStores = array_flip($filter_store_id);

	while ($arStore = $rsStore->fetch()) {
		if (isset($selectedStores[$arStore['ID']])) {
			$arStore['SELECTED'] = 'Y';
		}
		$arStores[$arStore['ID']] = $arStore;
	}
} else {
	while ($arStore = $rsStore->fetch()) {
		$arStores[$arStore['ID']] = $arStore;
	}
}

$arProducts = [];

if ($set_filter == 'Y') {
	$arProductFilter = ['IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID], 'ID' => []];


	if (strlen($filter_product_name)) {
		$arProductFilter['?NAME'] = $filter_product_name;
	}

	if (strlen($filter_product_id)) {
		$arProductFilter['=ID'] = $filter_product_id;
	}

	if (is_array($filter_product_section_id)) {
		$filter_product_section_id = array_filter($filter_product_section_id);
		if (!empty($filter_product_section_id)) {
			$arCatalogProductIds = [];
			$arFilter = [
				'IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID],
				'SECTION_ID' => $filter_product_section_id,
				'INCLUDE_SUBSECTIONS' => 'Y',
			];
			$rsCatalogProductId = CIBlockElement::GetList([], $arFilter, false, false, ['ID']);
			while ($arCatalogProductId = $rsCatalogProductId->Fetch()) {
				$arCatalogProductIds[] = $arCatalogProductId['ID'];
			}

			if (!empty($arCatalogProductIds)) {
				$arFilter = [
					'IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID],
					'PROPERTY_CML2_LINK' => $arCatalogProductIds
				];
				$rsOfferProductId = CIBlockElement::GetList([], $arFilter, false, false, ['ID']);
				while ($arOfferProductId = $rsOfferProductId->Fetch()) {
					$arProductFilter['ID'][] = $arOfferProductId['ID'];
				}
			}
		}
	}

	if (strlen($filter_product_barcode)) {
		$rsBarCode = \Bitrix\Catalog\StoreBarcodeTable::getList([
			'filter' => [
				'=BARCODE' => $filter_product_barcode
			]
		]);
		while ($arBarCode = $rsBarCode->fetch()) {
			$arProductFilter['ID'][] = $arBarCode['PRODUCT_ID'];
		}
	}

	if (empty($arProductFilter['ID'])) {
		unset($arProductFilter['ID']);
	}


	$arBarCodes = [];
	$dbBarCode = CCatalogStoreBarCode::getList([], ["IBLOCK_ID" => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID]]);
	while ($arBarCode = $dbBarCode->GetNext()) {
		$arBarCodes[$arBarCode['PRODUCT_ID']] = $arBarCode['BARCODE'];
	}

	$dbProduct = CIBlockElement::GetList(["NAME" => "ASC"], $arProductFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME']);
	while ($arProduct = $dbProduct->Fetch()) {
		$arProduct['BARCODE'] = trim($arBarCodes[$arProduct['ID']]);
		$arProducts[$arProduct['ID']] = $arProduct;
	}

	foreach ($arProducts as $arProduct) {
		if (!empty($filter_store_id) && is_array($filter_store_id)) {
			$filter_store_id = array_filter($filter_store_id);

			$arProduct["DOCS"] = [];
			$rsDocElement = CCatalogStoreDocsElement::GetList(
				[],
				[
					'ELEMENT_ID' => $arProduct['ID'],
					'STORE_FROM' => $filter_store_id,
				]
			);
			while ($arDocElement = $rsDocElement->Fetch()) {
				$arProduct["DOCS"][$arDocElement['DOC_ID']][] = $arDocElement;
			}


			$rsDocElement = CCatalogStoreDocsElement::GetList(
				[],
				[
					'!DOC_ID' => array_keys($arProduct["DOCS"]),
					'ELEMENT_ID' => $arProduct['ID'],
					'STORE_TO' => $filter_store_id,
				]
			);
			while ($arDocElement = $rsDocElement->Fetch()) {
				$arProduct["DOCS"][$arDocElement['DOC_ID']][] = $arDocElement;
			}
		} else {
			$rsDocElement = CCatalogStoreDocsElement::GetList([], ['ELEMENT_ID' => $arProduct['ID']]);
			$arProduct["DOCS"] = [];
			while ($arDocElement = $rsDocElement->Fetch()) {
				$arProduct["DOCS"][$arDocElement['DOC_ID']][] = $arDocElement;
			}
		}

		$rsBasket = CSaleBasket::GetList([], ['PRODUCT_ID' => $arProduct['ID'], '!ORDER_ID' => false], false, false, ['ID', 'QUANTITY', 'ORDER_ID', 'PRODUCT_ID']);
		$arProduct["ORDER"] = [];
		while ($arBasket = $rsBasket->Fetch()) {
			$arBasket['QUANTITY'] = intval($arBasket['QUANTITY']);
			$arBasket['SHIPMENT'] = [];
			if ($arShipment = \Bitrix\Sale\Internals\ShipmentItemStoreTable::getList([
				'filter' => [
					'BASKET_ID' => $arBasket['ID'],
				],
			])->fetch()) {
				$arBasket['SHIPMENT'] = $arShipment;
			}
			$arProduct["ORDER"][$arBasket['ORDER_ID']][] = $arBasket;
		}


		$arProduct["DOC_LIST"] = [];

		if (!empty($arProduct["ORDER"])) {
			$rsOrder = CSaleOrder::GetList([], ['ID' => array_keys($arProduct["ORDER"])], false, false, ['ID', 'DATE_INSERT_FORMAT', 'STATUS_ID', 'ACCOUNT_NUMBER', 'DEDUCTED']);
			while ($arOrder = $rsOrder->fetch()) {
				if ($arOrder['STATUS_ID'] == 'RE') continue;

				$arOrder['DATE_DOCUMENT'] = $arOrder['DATE_INSERT_FORMAT'];
				$arOrder['DATE_DOCUMENT_TS'] = strtotime($arOrder['DATE_INSERT_FORMAT']);
				$arOrder['DOC_TYPE'] = 'D';
				$arProduct["DOC_LIST"][$arOrder['ID']] = $arOrder;
			}
		}


		if (!empty($arProduct["DOCS"])) {
			$rsDoc = CCatalogDocs::GetList(
				['DATE_DOCUMENT' => 'ASC'],
				[
					'STATUS' => 'Y',
					'DOC_TYPE' => ['A', 'R', 'D', 'M'],
					'ID' => array_keys($arProduct["DOCS"]),
				]
			);
			while ($arDoc = $rsDoc->fetch()) {
				if (trim($arDoc['DATE_DOCUMENT']) == '') {
					if (trim($arDoc['DATE_STATUS']) == '') {
						$arDoc['DATE_DOCUMENT']	= 'Дата не указана';
					} else {
						$arDoc['DATE_DOCUMENT']	= $arDoc['DATE_STATUS'];
					}
					$arDoc['DATE_DOCUMENT_TS'] = strtotime($arDoc['DATE_DOCUMENT']);
				} else {
					$arDoc['DATE_DOCUMENT_TS'] = strtotime($arDoc['DATE_DOCUMENT']);
				}

				$arProduct["DOC_LIST"][$arDoc['ID']] = $arDoc;
			}
		}


		uasort($arProduct["DOC_LIST"], function ($a, $b) {
			return $a['DATE_DOCUMENT_TS'] > $b['DATE_DOCUMENT_TS'];
		});



		$arProduct["QUANTITY_START"] = 0;
		$arProduct["QUANTITY_IN"] = 0;
		$arProduct["QUANTITY_OUT"] = 0;
		$arProduct["QUANTITY_FINISH"] = 0;
		$arProduct["QUANTITY_RESERVED"] = 0;

		foreach ($arProduct["DOC_LIST"] as $arDoc) {
			if ($arDoc['DATE_DOCUMENT_TS'] < $date_document_from_ts) { //проведение документа раньше выбранного интервала
				if ($arDoc['DOC_TYPE'] == 'D') {
					if (isset($arDoc['ACCOUNT_NUMBER'])) { //заказ
						if ($arDoc['STATUS_ID'] == 'F') {
							foreach ($arProduct["ORDER"][$arDoc['ID']] as $arDocElement) {
								$arProduct["QUANTITY_START"] -= $arDocElement['QUANTITY'];
							}
						}
					} else { //документ списания
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							$arProduct["QUANTITY_START"] -= $arDocElement['AMOUNT'];
						}
					}
				} else {
					if ($arDoc['DOC_TYPE'] == 'M') {
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							if ($arDocElement['STORE_FROM'] == 3) { //приход
								$arProduct["QUANTITY_START"] += $arDocElement['AMOUNT'];
							} elseif ($arDocElement['STORE_TO'] == 3) { //списание
								$arProduct["QUANTITY_START"] -= $arDocElement['AMOUNT'];
							} else {
								$arProduct["QUANTITY_START"] += $arDocElement['AMOUNT'];
								$arProduct["QUANTITY_START"] -= $arDocElement['AMOUNT'];
							}
						}
					} else {
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							$arProduct["QUANTITY_START"] += $arDocElement['QUANTITY'];
						}
					}
				}
			} else if ($arDoc['DATE_DOCUMENT_TS'] <= $date_document_to_ts) {
				if ($arDoc['DOC_TYPE'] == 'D') {
					if (isset($arDoc['ACCOUNT_NUMBER'])) { //заказ
						if ($arDoc['DEDUCTED'] == 'Y') { //отгруженг
							foreach ($arProduct["ORDER"][$arDoc['ID']] as $arDocElement) {
								$arProduct["QUANTITY_OUT"] += $arDocElement['QUANTITY'];
							}
						} else { //не отгружен
							foreach ($arProduct["ORDER"][$arDoc['ID']] as $arDocElement) {
								$arProduct["QUANTITY_RESERVED"] += $arDocElement['QUANTITY'];
							}
						}
					} else { //документ
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							$arProduct["QUANTITY_OUT"] += $arDocElement['AMOUNT'];
						}
					}
				} else {
					if ($arDoc['DOC_TYPE'] == 'M') {
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							if ($arDocElement['STORE_FROM'] == 3) { //приход
								$arProduct["QUANTITY_IN"] += $arDocElement['AMOUNT'];
							} elseif ($arDocElement['STORE_TO'] == 3) { //списание
								$arProduct["QUANTITY_OUT"] += $arDocElement['AMOUNT'];
							} else {
								$arProduct["QUANTITY_IN"] += $arDocElement['AMOUNT'];
								$arProduct["QUANTITY_OUT"] += $arDocElement['AMOUNT'];
							}
						}
					} else {
						foreach ($arProduct["DOCS"][$arDoc['ID']] as $arDocElement) {
							$arProduct["QUANTITY_IN"] += $arDocElement['AMOUNT'];
						}
					}
				}
			}
		}

		$arProduct["QUANTITY_FINISH"] = $arProduct["QUANTITY_START"] + $arProduct["QUANTITY_IN"] - $arProduct["QUANTITY_OUT"];
		$arProduct["QUANTITY_AVALIABLE"] = $arProduct["QUANTITY_FINISH"] - $arProduct["QUANTITY_RESERVED"];


		if ($arProduct["QUANTITY_START"] == 0) {
			$arProduct["QUANTITY_START"] = '';
		}
		if ($arProduct["QUANTITY_IN"] == 0) {
			$arProduct["QUANTITY_IN"] = '';
		}
		if ($arProduct["QUANTITY_OUT"] == 0) {
			$arProduct["QUANTITY_OUT"] = '';
		}
		if ($arProduct["QUANTITY_FINISH"] == 0) {
			$arProduct["QUANTITY_FINISH"] = '';
		}
		if ($arProduct["QUANTITY_RESERVED"] == 0) {
			$arProduct["QUANTITY_RESERVED"] = '';
		}
		$arResult[] = $arProduct;
	}
}
function bxOrdersSort($a, $b)
{
	global $by, $order;
	$by = toUpper($by);
	$order = toUpper($order);

	if (in_array($by, ["ID", "QUANTITY_START", "QUANTITY_IN", "QUANTITY_OUT", "QUANTITY_FINISH", "QUANTITY_AVALIABLE"])) {
		if (DoubleVal($a[$by]) == DoubleVal($b[$by]))
			return 0;
		elseif (DoubleVal($a[$by]) > DoubleVal($b[$by]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	} else if ($by == "NAME") {
		if ($a["NAME"] == $b["NAME"])
			return 0;
		elseif ($a["NAME"] > $b["NAME"])
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	}
}
uasort($arResult, "bxOrdersSort");

$arHeaders = [
	["id" => "ID", "content" => "ID", "sort" => "ID", "default" => true],
	["id" => "BARCODE", "content" => "Штрихкод", "sort" => "BARCODE", "default" => true, "align" => "left"],
	["id" => "NAME", "content" => "Наименование", "sort" => "NAME", "default" => true, "align" => "left"],
	["id" => "QUANTITY_START", "content" => "Начальный остаток", "sort" => "QUANTITY_START", "default" => true, "align" => "left"],
	["id" => "QUANTITY_IN", "content" => "Приход", "sort" => "QUANTITY_IN", "default" => true, "align" => "left"],
	["id" => "QUANTITY_OUT", "content" => "Расход", "sort" => "QUANTITY_OUT", "default" => true, "align" => "left"],
	["id" => "QUANTITY_FINISH", "content" => "Конечный остаток", "sort" => "QUANTITY_FINISH", "default" => true, "align" => "left"],
	["id" => "QUANTITY_RESERVED", "content" => "Резерв", "sort" => "QUANTITY_RESERVED", "default" => true, "align" => "left"],
	["id" => "QUANTITY_AVALIABLE", "content" => "Доступное количество", "sort" => "QUANTITY_AVALIABLE", "default" => true, "align" => "left"],
];


$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));


$key = 0;
while ($arResult = $dbResult->GetNext()) {
	$arResult['NAME'] = html_entity_decode($arResult['~NAME']);
	$arResult['QUANTITY_START'] = 0;
	$row = &$lAdmin->AddRow($arResult["ID"], $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("ID", "<a href='/bitrix/admin/iblock_element_edit.php?IBLOCK_ID=" . GOODS_IBLOCK_ID . "&type=catalog&ID=" . $arResult["ID"] . "'>" . $arResult["ID"] . "</a>");
		$row->AddViewField("NAME", "<b>" . $arResult["NAME"] . "</b>");
	}

	if ($filter_hide_doc != 'Y') {

		foreach ($arResult['DOC_LIST'] as $arDoc) {
			if ($arDoc['DATE_DOCUMENT_TS'] < $date_document_from_ts) continue;
			if ($arDoc['DATE_DOCUMENT_TS'] > $date_document_to_ts) continue;

			$arDocItem = [];
			$arDocItem["ID"] = "";
			$arDocItem["NAME"] = $arDoc['DATE_DOCUMENT'] . ", ";

			if ($arDoc['DOC_TYPE'] == 'A') {
				$arDocItem["NAME"] .= "<strong>приход товара, ";
				$arDocItem["NAME"] .= " " . $arDoc['COMMENTARY'] . "</strong>";

				foreach ($arResult['DOCS'][$arDoc['ID']] as $docElemItem) {
					if ($docElemItem['ELEMENT_ID'] == $arResult['ID']) {
						$arDocItem["QUANTITY_IN"] = $docElemItem['AMOUNT'];
						$arResult['QUANTITY_START'] += (int)$docElemItem['AMOUNT'];
						$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
						$storeFrom = $docElemItem['STORE_FROM'];
					}
					$strStore = '';
					if (isset($arStores[$docElemItem['STORE_FROM']])) {
						$strStore = 'со склада ' . $arStores[$docElemItem['STORE_FROM']]['TITLE'] . ' ';
					}
					if (isset($arStores[$docElemItem['STORE_TO']])) {
						$strStore .= 'на склад ' . $arStores[$docElemItem['STORE_TO']]['TITLE'] . ' ';
					}
					$strStore = trim($strStore);
					$row = &$lAdmin->AddRow('DOC_' . $arDoc['ID'], $arDocItem);
					$row->AddViewField("NAME", "<a href='/bitrix/admin/cat_store_document_edit.php?ID=" . $arDoc['ID'] . "&lang=ru'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
				}
			} elseif ($arDoc['DOC_TYPE'] == 'D') {
				if (isset($arDoc['ACCOUNT_NUMBER'])) { //заказ
					if($arDoc['DEDUCTED'] == "N") {
						$arDocItem["NAME"] .= "резерв товара, <strong>заказ №" . $arDoc['ID'] . '</strong>';
						$arDocItem["QUANTITY_RESERVED"] = 0;
						foreach ($arResult['ORDER'][$arDoc['ID']] as $docElemItem) {
							if ($docElemItem['PRODUCT_ID'] == $arResult['ID']) {
								$arDocItem["QUANTITY_RESERVED"] += $docElemItem['QUANTITY'];
							}
							$strStore = '';
							if (!isset($arStores[$docElemItem['SHIPMENT']['STORE_ID']])) {
								$strStore = 'склад не указан';
							} else {
								$strStore = 'со склада ' . $arStores[$docElemItem['SHIPMENT']['STORE_ID']]['TITLE'] . ' ';
							}
							$strStore = trim($strStore);
							$row = &$lAdmin->AddRow('ORDER_' . $arDoc['ID'], $arDocItem);
							$row->AddViewField("NAME", "<a href='/bitrix/admin/sale_order_view.php?ID=" . $arDoc['ID'] . "&lang=ru#basket'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
						}
					} else {
						$arDocItem["NAME"] .= "списание товара, <strong>заказ №" . $arDoc['ID'] . '</strong>';
						$arDocItem["QUANTITY_OUT"] = 0;
						foreach ($arResult['ORDER'][$arDoc['ID']] as $docElemItem) {
							if ($docElemItem['PRODUCT_ID'] == $arResult['ID']) {
								$arDocItem["QUANTITY_OUT"] += $docElemItem['QUANTITY'];
								$arResult['QUANTITY_START'] -= (int)$docElemItem['QUANTITY'];
								$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
							}
							$strStore = '';
							if (!isset($arStores[$docElemItem['SHIPMENT']['STORE_ID']])) {
								$strStore = 'склад не указан';
							} else {
								$strStore = 'со склада ' . $arStores[$docElemItem['SHIPMENT']['STORE_ID']]['TITLE'] . ' ';
							}
							$row = &$lAdmin->AddRow('ORDER_' . $arDoc['ID'], $arDocItem);
							$row->AddViewField("NAME", "<a href='/bitrix/admin/sale_order_view.php?ID=" . $arDoc['ID'] . "&lang=ru#basket'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
						}
					}
				} else {
					$arDocItem["NAME"] .= "списание товара, ";
					$arDocItem["NAME"] .= " " . $arDoc['COMMENTARY'];
					foreach ($arResult['DOCS'][$arDoc['ID']] as $docElemItem) {
						if ($docElemItem['ELEMENT_ID'] == $arResult['ID']) {
							$arDocItem["QUANTITY_OUT"] = $docElemItem['AMOUNT'];
							$arResult['QUANTITY_START'] -= $docElemItem['AMOUNT'];
							$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
						}

						$strStore = '';
						if (isset($arStores[$docElemItem['STORE_FROM']])) {
							$strStore = 'со склада ' . $arStores[$docElemItem['STORE_FROM']]['TITLE'] . ' ';
						}
						if (isset($arStores[$docElemItem['STORE_TO']])) {
							$strStore .= 'на склад ' . $arStores[$docElemItem['STORE_TO']]['TITLE'] . ' ';
						}
						$strStore = trim($strStore);

						$row = &$lAdmin->AddRow('DOC_' . $arDoc['ID'], $arDocItem);
						$row->AddViewField("NAME", "<a href='/bitrix/admin/cat_store_document_edit.php?ID=" . $arDoc['ID'] . "&lang=ru'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
					}
				}
			} elseif ($arDoc['DOC_TYPE'] == 'R') {
				$arDocItem["NAME"] .= "возврат товара, ";
				$arDocItem["NAME"] .= " " . $arDoc['COMMENTARY'];
				foreach ($arResult['DOCS'][$arDoc['ID']] as $docElemItem) {
					if ($docElemItem['ELEMENT_ID'] == $arResult['ID']) {
						$arDocItem["QUANTITY_IN"] = $docElemItem['AMOUNT'];
						$arResult['QUANTITY_START'] += $docElemItem['AMOUNT'];
						$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
					}
					$strStore = '';
					if (isset($arStores[$docElemItem['STORE_FROM']])) {
						$strStore = 'со склада ' . $arStores[$docElemItem['STORE_FROM']]['TITLE'] . ' ';
					}
					if (isset($arStores[$docElemItem['STORE_TO']])) {
						$strStore .= 'на склад ' . $arStores[$docElemItem['STORE_TO']]['TITLE'] . ' ';
					}
					$strStore = trim($strStore);

					$row = &$lAdmin->AddRow('DOC_' . $arDoc['ID'], $arDocItem);
					$row->AddViewField("NAME", "<a href='/bitrix/admin/cat_store_document_edit.php?ID=" . $arDoc['ID'] . "&lang=ru'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
				}
			} elseif ($arDoc['DOC_TYPE'] == 'M') {
				$arDocItem["NAME"] .= "перемещение товара, ";
				$arDocItem["NAME"] .= " " . $arDoc['COMMENTARY'];
				foreach ($arResult['DOCS'][$arDoc['ID']] as $docElemItem) {
					if ($docElemItem['ELEMENT_ID'] == $arResult['ID']) {
						if ($docElemItem['STORE_FROM'] == 3) {
							$arDocItem["QUANTITY_IN"] = $docElemItem['AMOUNT'];
							$arResult['QUANTITY_START'] += $docElemItem['AMOUNT'];
							$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
						} elseif ($docElemItem['STORE_TO'] == 3) {
							$arDocItem["QUANTITY_OUT"] = $docElemItem['AMOUNT'];
							$arResult['QUANTITY_START'] -= $docElemItem['AMOUNT'];
							$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
						} else {
							$arDocItem["QUANTITY_IN"] = $docElemItem['AMOUNT'];
							$arDocItem["QUANTITY_OUT"] = $docElemItem['AMOUNT'];
							$arDocItem["QUANTITY_FINISH"] = $arResult['QUANTITY_START'];
						}
					}
					$strStore = '';
					if (isset($arStores[$docElemItem['STORE_FROM']])) {
						$strStore = 'со склада ' . $arStores[$docElemItem['STORE_FROM']]['TITLE'] . ' ';
					}
					if (isset($arStores[$docElemItem['STORE_TO']])) {
						$strStore .= 'на склад ' . $arStores[$docElemItem['STORE_TO']]['TITLE'] . ' ';
					}
					$strStore = trim($strStore);

					$row = &$lAdmin->AddRow('DOC_' . $arDoc['ID'], $arDocItem);
					$row->AddViewField("NAME", "<a href='/bitrix/admin/cat_store_document_edit.php?ID=" . $arDoc['ID'] . "&lang=ru'>" . $arDocItem['NAME'] . " (" . $strStore . ")</a>");
				}
			}

			global $USER;
			if (strlen($arDocItem['QUANTITY_FINISH']) > 0 && $USER->GetLogin() == 'ayushkov') {
				if ($arDoc['DATE_DOCUMENT'] == date('d.m.Y H:i:s', strtotime($arDoc['DATE_DOCUMENT']))) {
					\Nr\Absence::fix($arResult["ID"], $arDocItem['QUANTITY_FINISH'], strtotime($arDoc['DATE_DOCUMENT']));
				}
			}
		}
	}
	$key++;
}

$arSections = [];
$arFilter = ['IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID], 'ACTIVE' => 'Y'];
$rsSection = CIBlockSection::getlist(['LEFT_MARGIN' => 'ASC'], $arFilter, false, ['ID', 'NAME', 'IBLOCK_ID', 'DEPTH_LEVEL']);
if (!empty($filter_product_section_id)) {
	$selectedSections = array_flip($filter_product_section_id);
	while ($arSection = $rsSection->fetch()) {
		$arSection['D_NAME'] = str_repeat('. ', $arSection['DEPTH_LEVEL']) . $arSection['NAME'];
		if (isset($selectedSections[$arSection['ID']])) {
			$arSection['SELECTED'] = 'Y';
		}
		$arSections[] = $arSection;
	}
} else {
	while ($arSection = $rsSection->fetch()) {
		$arSection['D_NAME'] = str_repeat('. ', $arSection['DEPTH_LEVEL']) . $arSection['NAME'];
		$arSections[] = $arSection;
	}
}


$lAdmin->AddFooter(
	[
		[
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResult->SelectedRowsCount()
		],
	]
);

$lAdmin->AddAdminContextMenu();

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle("История складских остатков");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		[
			'Наименование товара',
			'ID',
			'Штрихкод',
			'Раздел',
			'Дата',
			'Показывать документы',
			'Склад',
		]
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Наименование товара:</td>
		<td>
			<input type="text" name="filter_product_name" value="<?= $filter_product_name ?>" size="40" class="adm-input">
		</td>
	</tr>
	<tr>
		<td>ID:</td>
		<td>
			<input type="text" name="filter_product_id" value="<?= $filter_product_id ?>" size="40" class="adm-input">
		</td>
	</tr>
	<tr>
		<td>Штрихкод:</td>
		<td>
			<input type="text" name="filter_product_barcode" value="<?= $filter_product_barcode ?>" size="40" class="adm-input">
		</td>
	</tr>
	<tr>
		<td>Раздел:</td>
		<td>
			<select name="filter_product_section_id[]" multiple style="height:200px;">
				<option value="">любой</option>
				<? foreach ($arSections as $arSection) { ?>
					<option value="<?= $arSection['ID'] ?>" <?= $arSection['SELECTED'] == 'Y' ? 'selected="selected"' : '' ?>><?= $arSection['D_NAME'] ?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Дата:</td>
		<td>
			<? echo CalendarPeriod("filter_date_document_from", $filter_date_document_from, "filter_date_document_to", $filter_date_document_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Скрыть документы:</td>
		<td>
			<label>
				<input type="checkbox" name="filter_hide_doc" value="Y">
				Да
			</label>
		</td>
	</tr>
	<tr>
		<td>Склад:</td>
		<td>
			<select name="filter_store_id[]" multiple>
				<option value="">любой</option>
				<? foreach ($arStores as $arStore) { ?>
					<option value="<?= $arStore['ID'] ?>" <?= $arStore['SELECTED'] == 'Y' ? 'selected="selected"' : '' ?>><?= $arStore['TITLE'] ?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<?
	$oFilter->Buttons(
		[
			"table_id" => $sTableID,
			"url" => $APPLICATION->GetCurPage(),
			"form" => "find_form"
		]
	);
	$oFilter->End();
	?>
</form>

<?
$lAdmin->DisplayList();

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>