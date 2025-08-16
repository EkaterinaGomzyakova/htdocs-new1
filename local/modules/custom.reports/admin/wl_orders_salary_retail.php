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

$sTableID = "tbl_coupons_orders";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
	"filter_status_date_from",
	"filter_status_date_to",
	"filter_insert_date_from",
	"filter_insert_date_to",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_status_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_status_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_insert_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_status_date_from) > 0) {
	$arFilter["DATE_STATUS_FROM"] = Trim($filter_status_date_from);
}

if (strlen($filter_insert_date_from) > 0) {
	$arFilter["DATE_INSERT_FROM"] = Trim($filter_insert_date_from);
}

if (strlen($filter_status_date_to) > 0) {
	$arFilter["DATE_STATUS_TO"] = Trim($filter_status_date_to) . ' 23:59:59';
}

if (strlen($filter_insert_date_to) > 0) {
	$arFilter["DATE_INSERT_TO"] = Trim($filter_insert_date_to) . ' 23:59:59';
}


$arResult = array();

$arFilter['CANCELED'] = "N";
$arFilter['STATUS_ID'] = "F";
$arFilter['PAYED'] = "Y";
$arFilter['!PAY_SYSTEM_ID'] = PAY_SYSTEMS_ID_EXCLUDE_FROM_SALARY;

$giftCertificateIds = [
	GIFT_CERTIFICATE_PRODUCT_ID
];

$dbCertificateProducts = CIBLockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, "!PROPERTY_GIFT_CERTIFICATE" => false], false, false, ['ID', 'PROPERTY_GIFT_CERTIFICATE']);
while ($arCertificateProduct = $dbCertificateProducts->Fetch()) {
	$giftCertificateIds[] = $arCertificateProduct['ID'];
}


$arSelectedFields = array("ID", "RESPONSIBLE_ID", "STATUS_ID", "DATE_STATUS");
$dbOrder = CSaleOrder::GetList(array("ID" => "DESC"), $arFilter, false, false, $arSelectedFields);

while ($arOrder = $dbOrder->Fetch()) {
	$totalSumByRetail = 0.00;
	$quantityTotal = 0;
	$order = Bitrix\Sale\Order::load($arOrder['ID']);

	$isOrderCreatedByBuyer = false;
	$propertyCollection = $order->getPropertyCollection();
	foreach ($propertyCollection as $property) {
		if ($property->getField('CODE') == "ORDER_CREATED_BY_BUYER") {
			$isOrderCreatedByBuyer = ($property->getValue() == "Y") ? true : false;
		}
	}

	if($isOrderCreatedByBuyer) {
		continue;
	}


	$shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
	foreach ($shipmentCollection as $shipment) {
		$systemItemCollection = $shipment->getShipmentItemCollection();
		$itemCollection = $systemItemCollection->getSellableItems();

		foreach ($itemCollection as $shipmentItem) {
			$basketItem = $shipmentItem->getBasketItem();
			if (in_array($basketItem->getProductId(), $giftCertificateIds) || CIBlockElement::GetIBlockByID($basketItem->getProductId()) == ADDITIONAL_CATALOG_IBLOCK_ID) {
				continue;
			}

			$totalSumByRetail += $basketItem->getQuantity() * $basketItem->getPrice();

			$quantityTotal += $basketItem->getQuantity();
		}
	}

	$arResult[$arOrder['RESPONSIBLE_ID']]['SUM_TOTAL'] += $totalSumByRetail;

	$arResult[$arOrder['RESPONSIBLE_ID']]['PRODUCT_QUANTITY'] += $quantityTotal;
	$arResult[$arOrder['RESPONSIBLE_ID']]['ORDERS_COUNT'] += 1;
}

$arResponsibles = [];
$arResponsiblesKeys = array_keys($arResult);
foreach ($arResponsiblesKeys as $person) {
	$arDBResponsible = CUser::GetByID($person)->Fetch();
	$arResponsibles[$person] = $arDBResponsible['LAST_NAME'] . " " . $arDBResponsible['NAME'];
}

foreach ($arResult as $key => $sum) {
	$arResult['ITEMS'][] = [
		"RESPONSIBLE_ID" => $key,
		"SUM_TOTAL" => $sum['SUM_TOTAL'],
		"ORDER_COUNT" => $sum['ORDERS_COUNT'],
		"MEDIAN_CHECK" => $sum['SUM_TOTAL'] / $sum['ORDERS_COUNT'],
		"MEDIAN_QUANTITY" => $sum['PRODUCT_QUANTITY'] / $sum['ORDERS_COUNT'],
		"PRODUCT_QUANTITY" => $sum['PRODUCT_QUANTITY'],
	];
}

$arHeaders = array(
	array("id" => "RESPONSIBLE_ID", "content" => "Ответственный", "sort" => "RESPONSIBLE_ID", "default" => true),
	array("id" => "SUM_TOTAL", "content" => "Итого",  "sort" => "SUM_TOTAL", "default" => true, "align" => "left"),
	array("id" => "ORDER_COUNT", "content" => "Кол-во заказов",  "sort" => "ORDER_COUNT", "default" => true, "align" => "left"),
	array("id" => "MEDIAN_CHECK", "content" => "Средний чек",  "sort" => "MEDIAN_CHECK", "default" => true, "align" => "left"),
	array("id" => "MEDIAN_QUANTITY", "content" => "Среднее кол-во товаров в заказе",  "sort" => "MEDIAN_QUANTITY", "default" => true, "align" => "left"),
	array("id" => "PRODUCT_QUANTITY", "content" => "Кол-во товаров",  "sort" => "PRODUCT_QUANTITY", "default" => true, "align" => "left"),
);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult['ITEMS']);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

$priceOnPage = 0.0;

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($arResult["DATE"], $arResult);
	if (!empty($dbResult->arResult)) {
		$userLink = "[<a href='/bitrix/admin/user_edit.php?ID=" . $arResult['RESPONSIBLE_ID'] . "' target='_blank'>" . $arResult['RESPONSIBLE_ID'] ."</a>]";
		$row->AddViewField("RESPONSIBLE_ID", $userLink . " " . $arResponsibles[$arResult['RESPONSIBLE_ID']]);
		$row->AddViewField("SUM_TOTAL", CurrencyFormat($arResult['SUM_TOTAL'], "RUB"));
		$row->AddViewField("MEDIAN_CHECK", CurrencyFormat($arResult['MEDIAN_CHECK'], "RUB"));
		$row->AddViewField("MEDIAN_QUANTITY", round($arResult['MEDIAN_QUANTITY'], 2));
		$row->AddViewField("PRODUCT_QUANTITY", $arResult['PRODUCT_QUANTITY']);
		$priceOnPage += $arResult['SUM_TOTAL'];
	}
	$key++;
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResult->SelectedRowsCount()
		),
	)
);

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle("Продажи сотрудников в розницу (без сертификатов)");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Дата создания",
			"Дата выполнения",
		)
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Дата создания заказа:</td>
		<td>
			<? echo CalendarPeriod("filter_insert_date_from", $filter_insert_date_from, "filter_insert_date_to", $filter_insert_date_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Дата присвоения заказу статуса Выполнен:</td>
		<td>
			<? echo CalendarPeriod("filter_status_date_from", $filter_status_date_from, "filter_status_date_to", $filter_status_date_to, "find_form", "Y") ?>
		</td>
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
CAdminMessage::ShowNote("Сумма заказов в выборке: " . CurrencyFormat($priceOnPage, "RUB"));
?>
<script>
	document.body.addEventListener('DOMSubtreeModified', function(e) {

		if (e.target.id == "tbl_coupons_orders_result_div") {
			var element = document.getElementsByClassName('adm-info-message-title');
			var items = document.querySelectorAll('.adm-list-table-row > td:nth-child(4)');
			var sum = 0.0;
			for (var i = 0; i < items.length; i++) {
				sum += parseFloat(items[i].innerHTML.replace(" руб.", "").replace(" ", ""));
			}
			element[0].innerHTML = "Сумма заказов в выборке: " + sum.toLocaleString('ru-RU', {
				style: 'currency',
				currency: 'RUB'
			});
		}
	});
</script>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>