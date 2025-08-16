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

$sTableID = "tbl_coupons_orders_summary";
$oSort = new CAdminSorting($sTableID, "DATE_INSERT", "desc");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_by = $_REQUEST['by'];
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
	"filter_coupon",
	"filter_insert_date_from",
	"filter_insert_date_to",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_insert_date_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = [];
$arCouponFilter = [];

if (strlen($filter_coupon) > 0) {
	$arCouponFilter["%COUPON"] = Trim($filter_coupon);
}

if (strlen($filter_insert_date_from) > 0) {
	$arFilter["DATE_INSERT_FROM"] = Trim($filter_insert_date_from);
}

if (strlen($filter_insert_date_to) > 0) {
	$arFilter["DATE_INSERT_TO"] = Trim($filter_insert_date_to);
}

$arResult = array();
$arCurUsed = array();

$minDate = 0;
$maxDate = 0;

if (strlen($filter_insert_date_from) > 0)
	$maxDate = MakeTimeStamp($filter_insert_date_from);
else
	$maxDate = mktime(0, 0, 0, date("n"), date("j") + 1, date("Y"));

$arFilter['CANCELED'] = "N";


$arOrderCoupons = [];
$couponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList(array(
	'select' => ['COUPON', 'ORDER_ID'],
	'filter' => array_merge(['!COUPON' => false], $arCouponFilter),
));

while ($arOrderCoupon = $couponList->fetch()) {
	$arOrderCoupons[$arOrderCoupon['ORDER_ID']] = $arOrderCoupon;
}

$arResult = [];

$arFilter['ID'] = array_keys($arOrderCoupons);
$arSelectedFields = ["ID", "PRICE", "DATE_INSERT", "USER_ID"];

$dbOrder = CSaleOrder::GetList(['DATE_INSERT' => 'DESC'], $arFilter, false, false, $arSelectedFields);
while ($arOrder = $dbOrder->Fetch()) {
	$couponName = $arOrderCoupons[$arOrder['ID']]['COUPON'];

	$arResult[$couponName]['COUPON'] = $couponName;

	$arResult[$couponName]['ORDER_COUNT']++;
	$arResult[$couponName]['ORDER_SUM'] += $arOrder['PRICE'];

	//Получить кол-во новых пользователей, пришедших по купону
	$arUser = CUser::GetByID($arOrder['USER_ID'])->fetch();

	if ($arOrder['DATE_INSERT'] == $arUser['DATE_REGISTER']) {
		$arResult[$couponName]['NEW_USER_COUNT']++;
	}
}

function bxOrdersSort($a, $b)
{
	global $sort_by, $sort_order;
	$sort_by = toUpper($sort_by);
	$order = toUpper($sort_order);

	if (in_array($sort_by, array("ID", "PRICE", "PAY_SYSTEM"))) {
		if (DoubleVal($a[$sort_by]) == DoubleVal($b[$sort_by]))
			return 0;
		elseif (DoubleVal($a[$sort_by]) > DoubleVal($b[$sort_by]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	} else if (in_array($sort_by, array("DATE_INSERT"))) {
		if (MakeTimeStamp($a["DATE_INSERT"]) == MakeTimeStamp($b["DATE_INSERT"]))
			return 0;
		elseif (MakeTimeStamp($a["DATE_INSERT"]) > MakeTimeStamp($b["DATE_INSERT"]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	} else {
		if ($a[$sort_by] == $b[$sort_by])
			return 0;
		elseif (DoubleVal($a[$sort_by]) > DoubleVal($b[$sort_by]))
			return ($order == "DESC") ? -1 : 1;
		else
			return ($order == "DESC") ? 1 : -1;
	}
}
uasort($arResult, "bxOrdersSort");


$arHeaders = array(
	array("id" => "COUPON", "content" => "COUPON", "sort" => "COUPON", "default" => true),
	array("id" => "ORDER_COUNT", "content" => "Кол-во оплаченных заказов", "sort" => "ORDER_COUNT", "default" => true, "align" => "left"),
	array("id" => "ORDER_SUM", "content" => "Сумма оплаченных заказов", "sort" => "ORDER_SUM", "default" => true, "align" => "left"),
	array("id" => "NEW_USER_COUNT", "content" => "Новых покупателей", "sort" => "NEW_USER_COUNT", "default" => true, "align" => "left"),
);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));


while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($arResult["COUPON"], $arResult);
	$row->AddViewField("ORDER_COUNT", $arResult["ORDER_COUNT"]);
	$row->AddViewField("ORDER_SUM", CurrencyFormat($arResult["ORDER_SUM"], "RUB"));
	$row->AddViewField("NEW_USER_COUNT", $arResult["NEW_USER_COUNT"] ?? 0);
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
$APPLICATION->SetTitle("Купоны - сводная таблица");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Код купона",
			"Дата создания заказа",
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