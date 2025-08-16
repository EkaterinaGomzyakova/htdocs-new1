<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Sale;
use \Bitrix\Sale\Exchange\Integration\Admin;

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
global $USER;

if ($saleModulePermissions == "D" || !$USER->isAdmin())
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));


if (!CBXFeatures::IsFeatureEnabled('SaleReports')) {
	require($DOCUMENT_ROOT . "/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");

$sTableID = "tbl_profit_by_goods";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = [
	"filter_date_payed_from",
	"filter_date_payed_to",
	"filter_company_id",
	"filter_brand_id",
];

if ($lAdmin->IsDefaultFilter()) {
	$filter_date_payed_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_date_payed_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));
	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = [
	'!PAY_SYSTEM_ID' => [20]
];

if (strlen($filter_date_payed_from) > 0) {
	$arFilter[">=DATE_PAYED"] = Trim($filter_date_payed_from);
} else {
	$filter_date_payed_from = date('01.m.Y');
	$arFilter[">=DATE_PAYED"] = date('01.m.Y');
}

if (strlen($filter_date_payed_to) > 0) {
	$arFilter["<=DATE_PAYED"] = Trim($filter_date_payed_to) . ' 23:59:59';
}


$arResult['ITEMS'] = [];

$arFilter['CANCELED'] = "N";
$arFilter['PAYED'] = "Y";

$arCompanies = [];
$dbCompanies = \Bitrix\Sale\CompanyTable::getList([]);
while ($arCompany = $dbCompanies->Fetch()) {
	$arCompanies[$arCompany['CODE']] = $arCompany;
}

$arCompanies['eshop'] = [
	'ID' => 'eshop',
	'NAME' => 'Интернет-магазин',
];


$arBrands = [];
$dbBrands = CIBlockElement::GetList(['NAME' => 'ASC'], ['IBLOCK_ID' => BRANDS_IBLOCK_ID], false, false, ['ID', 'NAME']);
while($arBrand = $dbBrands->Fetch()) {
	$arBrands[$arBrand['ID']] = $arBrand['NAME'];
}


$giftCertificateIds = [
	GIFT_CERTIFICATE_PRODUCT_ID,
];
$dbCertificateProducts = CIBLockElement::GetList(
	[],
	[
		'IBLOCK_ID' => GOODS_IBLOCK_ID,
		"!PROPERTY_GIFT_CERTIFICATE" => false,
	],
	false,
	false,
	['ID', 'PROPERTY_GIFT_CERTIFICATE']
);
while ($arCertificateProduct = $dbCertificateProducts->Fetch()) {
	$giftCertificateIds[] = $arCertificateProduct['ID'];
}


if (!empty($filter_company_id)) {

	if ($filter_company_id == "eshop") {
		$arFilter['COMPANY_ID'] = $arCompanies['PROSPEKT_61']['ID'];
		$arFilter['=PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
		$arFilter['=PROPERTY.VALUE'] = 'Y';
	} elseif ($filter_company_id == $arCompanies['PROSPEKT_61']['ID']) {
		$arFilter['COMPANY_ID'] = $filter_company_id;
		$arFilter['=PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
		$arFilter['PROPERTY.VALUE'] = 'N';
	} else {
		$arFilter['COMPANY_ID'] = $filter_company_id;
	}
}

$arSelectedFields = ["ID"];


$totalRetailSum = 0.00;
$totalPurchasingSum = 0.00;
$totalMarginSum = 0.00;

$dbOrders =  \Bitrix\Sale\Order::getList([
	'select' => $arSelectedFields,
	'filter' => $arFilter,
]);

while ($arOrder = $dbOrders->Fetch()) {
	$order = \Bitrix\Sale\Order::load($arOrder['ID']);
	$basket = $order->getBasket();
	foreach ($basket as $basketItem) {
		$productId = $basketItem->getProductId();
		if (in_array($productId, $giftCertificateIds)) {
			continue;
		}
		

		$arElement = CIBlockElement::GetList([], ['ID' => $productId], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_BRAND', 'PROPERTY_CODE_1C'])->GetNext();

		if(!empty($filter_brand_id) && $filter_brand_id != $arElement['PROPERTY_BRAND_VALUE']) {
			continue;
		}

		$arResult['ITEMS'][$productId]['BRAND_NAME'] = $arBrands[$arElement['PROPERTY_BRAND_VALUE']];
		$arResult['ITEMS'][$productId]['CODE_1C'] = $arElement['PROPERTY_CODE_1C_VALUE'];

		$productName = $basketItem->getField('NAME');
		$arResult['ITEMS'][$productId]['QUANTITY'] += $basketItem->getQuantity();

		$purchasingPrice = getPurchasingPrice($productId, $order->getField('DATE_INSERT'));

		$arResult['ITEMS'][$productId]['PRICE_RETAIL'] = $basketItem->getPrice();
		$arResult['ITEMS'][$productId]['PRICE_PURCHASING'] = $purchasingPrice;
		$arResult['ITEMS'][$productId]['MARGIN_BY_PIECE'] = $basketItem->getPrice() - $purchasingPrice;

		$productPurchasingSum = $purchasingPrice * $basketItem->getQuantity();
		$arResult['ITEMS'][$productId]['NAME'] = $productName . ' [' . $productId . ']';

		$productRetailSum = $basketItem->getPrice() * $basketItem->getQuantity();
		$arResult['ITEMS'][$productId]['SUM_RETAIL'] += $productRetailSum;
		$arResult['ITEMS'][$productId]['SUM_PURCHASING'] += $productPurchasingSum;

		$totalRetailSum += $productRetailSum;
		$totalPurchasingSum += $productPurchasingSum;
	}
}

foreach ($arResult['ITEMS'] as $productId => $arProduct) {
	if ($arProduct['SUM_PURCHASING'] == 0 || $arProduct['SUM_RETAIL'] == 0) {
		$arResult['ITEMS'][$productId]['PROFIT'] = 0;
	} else {
		$arResult['ITEMS'][$productId]['PROFIT'] = round(($arProduct['SUM_RETAIL'] / $arProduct['SUM_PURCHASING']) * 100 - 100, 2);
	}

	$arResult['ITEMS'][$productId]['MARGIN'] = $arProduct['SUM_RETAIL'] - $arProduct['SUM_PURCHASING'];
	$totalMarginSum += $arResult['ITEMS'][$productId]['MARGIN'];
}

if (isset($_REQUEST['by'])) {
	usort($arResult['ITEMS'], function ($a, $b) {
		if ($_REQUEST['order'] == 'asc') {
			return $a[$_REQUEST['by']] <=> $b[$_REQUEST['by']];
		} else {
			return - ($a[$_REQUEST['by']] <=> $b[$_REQUEST['by']]);
		}
	});
}

$totalString = "Розница: " . CurrencyFormat($totalRetailSum, "RUB") . ",<br>Закупка: " . CurrencyFormat($totalPurchasingSum, 'RUB') . ',<br>Маржинальность: ' . CurrencyFormat($totalMarginSum, 'RUB');

$arHeaders = [
	["id" => "NAME", "content" => "Товар", "sort" => "NAME", "default" => true],
	["id" => "BRAND_NAME", "content" => "Бренд", "sort" => "BRAND_NAME", "default" => true],
	["id" => "CODE_1C", "content" => "Код в 1С", "sort" => "CODE_1C", "default" => true],
	["id" => "PRICE_RETAIL", "content" => "Розница",  "sort" => "PRICE_RETAIL", "default" => true, "align" => "left"],
	["id" => "PRICE_PURCHASING", "content" => "Закупка",  "sort" => "PRICE_PURCHASING", "default" => true, "align" => "left"],
	["id" => "MARGIN_BY_PIECE", "content" => "Наценка за шт.",  "sort" => "MARGIN_BY_PIECE", "default" => true, "align" => "left"],
	["id" => "QUANTITY", "content" => "Кол-во",  "sort" => "QUANTITY", "default" => true, "align" => "left"],
	["id" => "SUM_RETAIL", "content" => "Сумма в розницу",  "sort" => "SUM_RETAIL", "default" => true, "align" => "left"],
	["id" => "SUM_PURCHASING", "content" => "Сумма в закупке",  "sort" => "SUM_PURCHASING", "default" => true, "align" => "left"],
	["id" => "MARGIN", "content" => "Маржинальность",  "sort" => "MARGIN", "default" => true, "align" => "left"],
	["id" => "PROFIT", "content" => "Рентабельность, %",  "sort" => "PROFIT", "default" => true, "align" => "left"],
	["id" => "SUMMARY", "content" => $totalString,  "sort" => "PROFIT", "default" => true, "align" => "left"],
];

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($arResult["NAME"], $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("BRAND_NAME", $arResult['BRAND_NAME']);
		$row->AddViewField("PRICE_RETAIL", CurrencyFormat($arResult['PRICE_RETAIL'], "RUB"));
		$row->AddViewField("PRICE_PURCHASING", CurrencyFormat($arResult['PRICE_PURCHASING'], "RUB"));
		$row->AddViewField("QUANTITY", $arResult['QUANTITY']);
		$row->AddViewField("SUM_RETAIL", CurrencyFormat($arResult['SUM_RETAIL'], "RUB"));
		$row->AddViewField("SUM_PURCHASING", CurrencyFormat($arResult['SUM_PURCHASING'], "RUB"));
		$row->AddViewField("MARGIN", CurrencyFormat($arResult['MARGIN'], "RUB"));
		if ($arResult['PROFIT'] == 0) {
			$row->AddViewField("PROFIT", '<span style="background:red">' . $arResult['PROFIT'] . '</span>');
		} else {
			$row->AddViewField("PROFIT", $arResult['PROFIT']);
		}
		$row->AddViewField("SUMMARY", '');
	}
	$key++;
}

$lAdmin->AddFooter(
	[
		[
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResult->SelectedRowsCount()
		],
	],
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
$APPLICATION->SetTitle("Доходность по товарам за период");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		[
			"Дата оплаты",
			"Офис",
			"Бренд",
		]
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Дата оплаты:</td>
		<td>
			<? echo CalendarPeriod("filter_date_payed_from", $filter_date_payed_from, "filter_date_payed_to", $filter_date_payed_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Офис:</td>
		<td>
			<select name="filter_company_id">
				<option value="">Не выбрано</option>
				<? foreach ($arCompanies as $arCompany) { ?>
					<option value="<?= $arCompany['ID'] ?>" <? if ($filter_company_id == $arCompany['ID']) { ?>selected<? } ?>><?= $arCompany['NAME'] ?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Бренд:</td>
		<td>
			<select name="filter_brand_id">
				<option value="">Не выбрано</option>
				<? foreach ($arBrands as $id => $name) { ?>
					<option value="<?= $id ?>" <? if ($filter_brand_id == $id) { ?>selected<? } ?>><?= $name ?></option>
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
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>
<?

function getPurchasingPrice($productId, $date)
{
	$date = strtotime($date);

	$arPurchasingPrices = [];
	$arPurchasingPrices[] = [
		"PRICE" => 0.00,
		'DATE' => strtotime('01.01.1970')
	];

	$items = \WL\HL::table('HistoryPrice')
		->filter(['UF_PRODUCT_ID' => $productId, 'UF_PRICE_TYPE' => false])
		->sort(['UF_TIMESTAMP' => 'ASC'])
		->select(['UF_PRICE', 'UF_TIMESTAMP', 'UF_PRODUCT_ID'])->all();

	foreach ($items as $item) {
		unset($item['ID']);
		$item['DATE'] = strtotime($item['UF_TIMESTAMP']);
		$item['DATE_FORMAT'] = $item['UF_TIMESTAMP']->format('d.m.Y');
		$item['PRICE'] = $item['UF_PRICE'];
		$item['PRODUCT_ID'] = $item['UF_PRODUCT_ID'];
		$arPurchasingPrices[] = $item;
	}

	$arPurchasingPrices[] = [
		"PRICE" => 0.00,
		'DATE' => strtotime('31.12.9999')
	];


	foreach ($arPurchasingPrices as $key => $arPrice) {
		if ($key < 1) {
			continue;
		}

		if ($date >= $arPrice['DATE'] && $date < $arPurchasingPrices[$key + 1]['DATE']) {
			return $arPrice['PRICE'];
		}
	}

	return 0;
}
