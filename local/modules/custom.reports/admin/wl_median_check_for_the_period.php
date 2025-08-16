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

$sTableID = "tbl_sales_for_the_period";
$lAdmin = new CAdminList($sTableID);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
	"filter_insert_date_from",
	"filter_insert_date_to",
	"filter_responsible_id",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 2);
	$filter_insert_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 2));

	$set_filter = "Y";
}


$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_insert_date_from) > 0) {
	$arFilter[">DATE_STATUS"] = Trim($filter_insert_date_from) . ' 00:00:00';
} else {
	$filter_insert_date_from_DAYS_TO_BACK = 1;
	$filter_insert_date_from = GetTime(time() - 86400);
	$arFilter[">DATE_STATUS"] = Trim($filter_insert_date_from) . ' 00:00:00';
}

if (strlen($filter_insert_date_to) > 0) {
	$arFilter["<DATE_STATUS"] = Trim($filter_insert_date_to) . ' 23:59:59';
} else {
	$filter_insert_date_to_DAYS_TO_BACK = 1;
	$filter_insert_date_to = GetTime(time() - 86400) . ' 23:59:59';
	$arFilter["<DATE_STATUS"] = Trim($filter_insert_date_to) . ' 23:59:59';
}

if (strlen($filter_responsible_id) > 0) {
	$arFilter['RESPONSIBLE_ID'] = $filter_responsible_id;
}

$arResult = array();

$arFilter['CANCELED'] = "N";
$arFilter['PAYED'] = "Y";
$arFilter['STATUS_ID'] = 'F';

$arExcludedPaysystems = [];
$dbExcludedPaysystem = \Bitrix\Sale\PaySystem\Manager::getList(
	[
		'filter' => ['XML_ID' => ['ad']],
		'select' => ['ID']
	]
);
while($arPaysystem = $dbExcludedPaysystem->fetch()) {
	$arExcludedPaysystems[] = $arPaysystem['ID'];
}
$arFilter['!PAY_SYSTEM_ID'] = $arExcludedPaysystems;


$arCompanies = \Bitrix\Sale\CompanyTable::getList([])->fetchAll();

$arCompanies[] = [
	'ID' => 'eshop',
	'NAME' => 'Интернет-магазин',
];


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


$arResponsible = [];
$rsUsers = CUser::GetList(($by = "personal_country"), ($order = "desc"), ["ACTIVE"  => "Y", "GROUPS_ID" => [1, 9, 10, 14]]);
while ($arUser = $rsUsers->fetch()) {
	$arResponsible[$arUser['ID']] = $arUser;
}

$arCompanyInfo = [];

$dbOrder = \Bitrix\Sale\Order::getList(['filter' => $arFilter, 'select' => ["ID"]]);
while ($order = $dbOrder->fetch()) {
	$totalSum = 0.00;
	$order = \Bitrix\Sale\Order::load($order['ID']);
	$basket = $order->getBasket();

	$orderCreatedByBuyer = false;
	$propertyCollection = $order->getPropertyCollection();
	foreach ($propertyCollection as $property) {
		if ($property->getField('CODE') == "ORDER_CREATED_BY_BUYER" && $property->getValue() == "Y") {
			$orderCreatedByBuyer = true;
		}
	}

	$companyId = false;
	if ($orderCreatedByBuyer) {
		$companyId = 'eshop';
	} else {
		$companyId = $order->getField('COMPANY_ID');
	}

	foreach ($basket as $basketItem) {
		$arCompanyInfo[$companyId]['TOTAL_PRODUCT_COUNT'] += $basketItem->getQuantity();
	}

	$shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
	foreach ($shipmentCollection as $shipment) {
		$systemItemCollection = $shipment->getShipmentItemCollection();
		$itemCollection = $systemItemCollection->getSellableItems();

		foreach ($itemCollection as $shipmentItem) {
			$basketItem = $shipmentItem->getBasketItem();
			if (in_array($basketItem->getProductId(), $giftCertificateIds) || CIBlockElement::GetIBlockByID(
					$basketItem->getProductId()
				) == ADDITIONAL_CATALOG_IBLOCK_ID) {
				continue;
			}
			$totalSum += $basketItem->getQuantity() * $basketItem->getPrice();
		}
		$arCompanyInfo[$companyId]['TOTAL_SUM'] += $totalSum;
	}

	$arCompanyInfo[$companyId]['TOTAL_ORDER_COUNT']++;
}

$arResult['ITEMS'] = [];
foreach ($arCompanies as $company) {
	if ($arCompanyInfo[$company['ID']]['TOTAL_ORDER_COUNT'] > 0) {
		$arResult['ITEMS'][$company['ID']] = [
			'MEDIAN_CHECK_SUM' => CurrencyFormat(round($arCompanyInfo[$company['ID']]['TOTAL_SUM'] / $arCompanyInfo[$company['ID']]['TOTAL_ORDER_COUNT'], 2), "RUB"),
			'MEDIAN_PRODUCT_COUNT' => round($arCompanyInfo[$company['ID']]['TOTAL_PRODUCT_COUNT'] / $arCompanyInfo[$company['ID']]['TOTAL_ORDER_COUNT'], 2),
			'PRODUCT_COUNT' => $arCompanyInfo[$company['ID']]['TOTAL_PRODUCT_COUNT'],
			'COMPANY' => $company['NAME'],
			'ORDER_COUNT' => $arCompanyInfo[$company['ID']]['TOTAL_ORDER_COUNT'],
			'SUM' => CurrencyFormat($arCompanyInfo[$company['ID']]['TOTAL_SUM'], 'RUB'),
		];
	}
}


$arHeaders = array(
	array("id" => "COMPANY", "content" => "Магазин",  "sort" => "COMPANY", "default" => true, "align" => "left"),
	array("id" => "MEDIAN_CHECK_SUM", "content" => "Средний чек", "sort" => "MEDIAN_CHECK_SUM", "default" => true),
	array("id" => "MEDIAN_PRODUCT_COUNT", "content" => "Среднее кол-во товаров в чеке",  "sort" => "MEDIAN_PRODUCT_COUNT", "default" => true, "align" => "left"),
	array("id" => "PRODUCT_COUNT", "content" => "Кол-во товаров",  "sort" => "PRODUCT_COUNT", "default" => true, "align" => "left"),
	array("id" => "ORDER_COUNT", "content" => "Кол-во чеков",  "sort" => "ORDER_COUNT", "default" => true, "align" => "left"),
	array("id" => "SUM", "content" => "Сумма",  "sort" => "SUM", "default" => true, "align" => "left"),

);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult['ITEMS']);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow('', $arResult);
	$key++;
}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle("Средний чек и кол-во товаров за период");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		[
			"Дата выполнения заказа",
			"Ответственный",
		]
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Дата выполнения заказа:</td>
		<td>
			<? echo CalendarPeriod("filter_insert_date_from", $filter_insert_date_from, "filter_insert_date_to", $filter_insert_date_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Ответственный</td>
		<td><? echo FindUserID("filter_responsible_id", $filter_responsible_id, "", "find_form"); ?></td>
	</tr>
	<?
	$oFilter->Buttons(
		array(
			"table_id" => $sTableID,
			"url" => $APPLICATION->GetCurPage(),
			"form" => "find_form"
		)
	);
	$oFilter->End();
	?>
</form>

<?

$lAdmin->DisplayList();
?>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>