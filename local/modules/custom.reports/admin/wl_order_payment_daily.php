<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

use Bitrix\Sale\Internals\PaymentTable;

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
	"filter_date_paid_from",
	"filter_date_paid_to",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_date_paid_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_date_paid_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = [];

if (strlen($filter_date_paid_from) > 0) {
	$arFilter[">=DATE_PAID"] = Trim($filter_date_paid_from);
}

if (strlen($filter_date_paid_to) > 0) {
	$arFilter["<=DATE_PAID"] = Trim($filter_date_paid_to) . " 23:59:59";
}

$res = \Bitrix\Sale\Internals\CompanyTable::getList([]);
$companies = $res->fetchAll();

$companies['eshop'] = [
	"ID" => 'eshop',
	'NAME' => "Интернет-магазин"
];

$arResult['ITEMS'] = [];
$arFilter['PAID'] = "Y";

$select = array();

$dbPayments = PaymentTable::getList([
	'select' => ['ID', 'ORDER_ID', 'DATE_PAID', 'SUM'],
	'filter' => $arFilter,
]);

while ($arPayment = $dbPayments->Fetch()) {
	$dbOrder = Bitrix\Sale\Order::load($arPayment['ORDER_ID']);
	$companyId = $dbOrder->getField('COMPANY_ID');
	$propertyCollection = $dbOrder->getPropertyCollection();
	foreach ($propertyCollection as $prop) {
		if ($prop->getField('CODE') == "ORDER_CREATED_BY_BUYER" && $prop->getValue() == "Y") {
			$companyId = 'eshop';
			break;
		}
	}
	$arResult['ITEMS'][$arPayment['DATE_PAID']->format('d.m.Y')][$companyId]['SUM'] += $arPayment['SUM'];
	$arResult['ITEMS'][$arPayment['DATE_PAID']->format('d.m.Y')][$companyId]['DATE'] = $arPayment['DATE_PAID']->format('d.m.Y');
}

foreach($arResult['ITEMS'] as $date => &$array) {
	foreach($companies as $company) {
		if(empty($array[$company['ID']]['SUM'])) {
			$array[$company['ID']]['SUM'] = 0;
			$array[$company['ID']]['DATE'] = $date;
		}
	}
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

$arHeaders = [
	["id" => "DATE", "content" => "Дата", "sort" => "DATE", "default" => true],
];
foreach ($companies as $company) {
	$arHeaders[] = ["id" => "COMPANY_" . $company['ID'], "content" => $company['NAME'], "sort" => "COMPANY_" . $company['ID'], "default" => true];
}

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow('', $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("DATE", $arResult[$company['ID']]['DATE']);

		foreach($companies as $company) {
			if($_GET['mode'] == 'excel') {
				$row->AddViewField("COMPANY_" . $company['ID'], $arResult[$company['ID']]['SUM'], 'RUB');
			} else {
				$row->AddViewField("COMPANY_" . $company['ID'], CurrencyFormat($arResult[$company['ID']]['SUM'], 'RUB'));
			}
		}
	}
}

$lAdmin->AddAdminContextMenu();

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
$APPLICATION->SetTitle("Способы доставки за период");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Дата оплаты",
		)
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Дата оплаты:</td>
		<td>
			<? echo CalendarPeriod("filter_date_paid_from", $filter_date_paid_from, "filter_date_paid_to", $filter_date_paid_to, "find_form", "Y") ?>
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