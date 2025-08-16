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
$oSort = new CAdminSorting($sTableID, "DATE_INSERT", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
	"filter_coupon",
	"filter_status_date_from",
	"filter_status_date_to",
	"filter_insert_date_from",
	"filter_insert_date_to",
	"filter_id_from",
	"filter_id_to",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_status_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_status_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_insert_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$filter_id_from = 0;
	$filter_id_to = 0;
	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = [];
$arCouponFilter = [];

if (strlen($filter_coupon) > 0) {
	$arCouponFilter["%COUPON"] = Trim($filter_coupon);
}

if (strlen($filter_status_date_from) > 0) {
	$arFilter[">DATE_STATUS"] = Trim($filter_status_date_from);
}

if (strlen($filter_insert_date_from) > 0) {
	$arFilter[">DATE_INSERT"] = Trim($filter_insert_date_from);
}

if (strlen($filter_status_date_to) > 0) {
	$arFilter["<DATE_STATUS"] = Trim($filter_status_date_to) . ' 23:59:59';
}

if (strlen($filter_insert_date_to) > 0) {
	$arFilter["<DATE_INSERT"] = Trim($filter_insert_date_to) . ' 23:59:59';
}

if (strlen($filter_id_from) > 0) {
	$arFilter[">=ID"] = Trim($filter_id_from);
}

if (strlen($filter_id_to) > 0) {
	$arFilter["<=ID"] = Trim($filter_id_to);
}

if (strlen($filter_id_from) > 0)
	$filter_id_from = Trim($filter_id_from);
else
	$filter_id_from = 0;

if (strlen($filter_id_to) > 0)
	$filter_id_to = Trim($filter_id_to);
else
	$filter_id_to = 0;


$arResult = array();
$arCurUsed = array();

$minDate = 0;
$maxDate = 0;

if (strlen($filter_status_date_from) > 0)
	$minDate = MakeTimeStamp($filter_status_date_from);
if (strlen($filter_insert_date_from) > 0)
	$maxDate = MakeTimeStamp($filter_insert_date_from);
else
	$maxDate = mktime(0, 0, 0, date("n"), date("j") + 1, date("Y"));

$arFilter['CANCELED'] = "N";


$arOrderIds = [];
$couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
	'select' => ['COUPON', 'ORDER_ID'],
	'filter' => array_merge(['!COUPON' => false], $arCouponFilter),
));

while ($arOrderCoupon = $couponList->fetch()) {
	$arOrderIds[$arOrderCoupon['ORDER_ID']] = $arOrderCoupon;
}

$arFilter['ID'] = array_keys($arOrderIds);

$arSelectedFields = array("ID", "RESPONSIBLE_ID", "STATUS_ID", "DATE_STATUS", "PRICE_DELIVERY", "PRICE", "PAY_SYSTEM_ID", "DATE_INSERT");
$dbOrder = CSaleOrder::GetList(['DATE_INSERT' => 'DESC'], $arFilter, false, false, $arSelectedFields);

$key = 0;
while ($arOrder = $dbOrder->Fetch()) {
	$arResult[$key]["COUPONS"] = $arOrderIds[$arOrder['ID']]['COUPON'];

	$arResult[$key]["ID"] = $arOrder['ID'];
	$arResult[$key]["DATE_INSERT"] = $arOrder['DATE_INSERT'];
	$arResult[$key]["STATUS"] = CSaleStatus::GetByID($arOrder['STATUS_ID'])['NAME'];
	if ($arOrder['RESPONSIBLE_ID'] > 0) {
		$arResponsible = CUser::GetByID($arOrder['RESPONSIBLE_ID'])->Fetch();
		$arResult[$key]["RESPONSIBLE"] = $arResponsible['LAST_NAME'] . " " . $arResponsible['NAME'];
	} else {
		$arResult[$key]["RESPONSIBLE"] = "";
	}

	$arResult[$key]["PRICE"] = $arOrder["PRICE"];
	$paySystem = CSalePaySystem::GetByID($arOrder["PAY_SYSTEM_ID"]);
	$arResult[$key]["PAY_SYSTEM"] = $paySystem['NAME'];

	$innerPaySystemId = 1;
	if ($arOrder["PAY_SYSTEM_ID"] == $innerPaySystemId) {
		$order = \Bitrix\Sale\Order::load($arOrder['ID']);
		$paymentCollection = $order->getPaymentCollection();
		$hasExtPayment = false;
		foreach ($paymentCollection as $payment) {
			$ps = $payment->getPaySystem();
			if ($ps->getField('ID') != $innerPaySystemId) {
				$hasExtPayment = true;
			}
		}


		foreach ($paymentCollection as $payment) {
			$ps = $payment->getPaySystem();
			if (count($paymentCollection) > 1 && $hasExtPayment && $ps->getField('ID') == $innerPaySystemId) {
				continue;
			}
			$arResult[$key]["PAY_SYSTEM"] = $payment->getField('PAY_SYSTEM_NAME');
		}
	}



	$key++;
}

function bxOrdersSort($a, $b)
{
	global $by, $sort_order;
	$by = toUpper($by);
	$order = toUpper($sort_order);

	if (in_array($by, array("ID", "PRICE", "PAY_SYSTEM"))) {
		if (DoubleVal($a[$by]) == DoubleVal($b[$by]))
			return 0;
		elseif (DoubleVal($a[$by]) > DoubleVal($b[$by]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	} else if (in_array($by, array("DATE_STATUS"))) {
		if (MakeTimeStamp($a["DATE_STATUS"]) == MakeTimeStamp($b["DATE_STATUS"]))
			return 0;
		elseif (MakeTimeStamp($a["DATE_STATUS"]) > MakeTimeStamp($b["DATE_STATUS"]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	} else if (in_array($by, array("DATE_INSERT"))) {
		if (MakeTimeStamp($a["DATE_INSERT"]) == MakeTimeStamp($b["DATE_INSERT"]))
			return 0;
		elseif (MakeTimeStamp($a["DATE_INSERT"]) > MakeTimeStamp($b["DATE_INSERT"]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	}
}
uasort($arResult, "bxOrdersSort");

$arHeaders = array(
	array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
	array("id" => "DATE_INSERT", "content" => "Дата создания заказа", "sort" => "DATE_INSERT", "default" => true, "align" => "left"),
	array("id" => "STATUS", "content" => "Статус заказа", "sort" => "STATUS", "default" => true, "align" => "left"),
	array("id" => "PRICE", "content" => "Сумма заказа",  "sort" => "PRICE", "default" => true, "align" => "left"),
	array("id" => "COUPONS", "content" => "Купоны",  "sort" => "COUPONS", "default" => true, "align" => "left"),
	array("id" => "PAY_SYSTEM", "content" => "Способ оплаты", "default" => true, "align" => "left"),
	array("id" => "RESPONSIBLE", "content" => "Ответственный", "default" => true, "align" => "left"),
);


$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

$priceOnPage = 0.0;
$key = 0;
while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($arResult["DATE"], $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("ID", "<a href='/bitrix/admin/sale_order_view.php?ID=" . $arResult["ID"] . "'>" . $arResult["ID"] . "</a>");
		$row->AddViewField("DATE_INSERT", $arResult["DATE_INSERT"]);
		$row->AddViewField("STATUS", $arResult["STATUS"]);
		$row->AddViewField("PRICE", CurrencyFormat($arResult["PRICE"], "RUB"));
		$row->AddViewField("COUPONS", $arResult["COUPONS"]);
		$priceOnPage += $arResult["PRICE"];
		$row->AddViewField("PAY_SYSTEM", $arResult["PAY_SYSTEM"]);
		$row->AddViewField("RESPONSIBLE", $arResult["RESPONSIBLE"]);
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
$APPLICATION->SetTitle("Купоны, примененные в заказах");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Дата выполнения",
			"ID",
		)
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Код купона (поиск подстроки)</td>
		<td>
			<input type="text" name="filter_coupon" value="<? echo $filter_coupon ?>" size="10">
		</td>
	</tr>
	<tr>
		<td>Дата создания заказа:</td>
		<td>
			<? echo CalendarPeriod("filter_insert_date_from", $filter_insert_date_from, "filter_insert_date_to", $filter_insert_date_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Дата присвоения заказу статуса «Получен»:</td>
		<td>
			<? echo CalendarPeriod("filter_status_date_from", $filter_status_date_from, "filter_status_date_to", $filter_status_date_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>ID заказа:</td>
		<td>
			<script language="JavaScript">
				function filter_id_from_Change() {
					if (document.find_form.filter_id_to.value.length <= 0) {
						document.find_form.filter_id_to.value = document.find_form.filter_id_from.value;
					}
				}
			</script>
			с
			<input type="text" name="filter_id_from" OnChange="filter_id_from_Change()" value="<? echo (IntVal($filter_id_from) > 0) ? IntVal($filter_id_from) : "" ?>" size="10">
			по
			<input type="text" name="filter_id_to" value="<? echo (IntVal($filter_id_to) > 0) ? IntVal($filter_id_to) : "" ?>" size="10">
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

		//console.log(document.getElementsByClassName('adm-info-message-title'));
	});
</script>
<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>