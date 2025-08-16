<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Sale;
use \Bitrix\Sale\Exchange\Integration\Admin;

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm(GetMessage("ACCESS_DENIED"));

if(!CBXFeatures::IsFeatureEnabled('SaleReports'))
{
	require($DOCUMENT_ROOT."/bitrix/modules/main/include/prolog_admin_after.php");

	ShowError(GetMessage("SALE_FEATURE_NOT_ALLOW"));

	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
	die();
}

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/prolog.php");

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
	"filter_id_from",
	"filter_id_to",
	"filter_pay_system"
);

if($lAdmin->IsDefaultFilter())
{
	$filter_status_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_status_date_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 30));
	
	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_insert_date_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 30));
	
	$filter_id_from = 0;
	$filter_id_to = 0;
	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_status_date_from)>0)
{
	$arFilter["DATE_STATUS_FROM"] = Trim($filter_status_date_from);
}

if (strlen($filter_insert_date_from)>0)
{
	$arFilter["DATE_INSERT_FROM"] = Trim($filter_insert_date_from);
}

if (strlen($filter_status_date_to)>0)
{
	$arFilter["DATE_STATUS_TO"] = Trim($filter_status_date_to);
}

if (strlen($filter_insert_date_to)>0)
{
	$arFilter["DATE_INSERT_TO"] = Trim($filter_insert_date_to);
}

if (strlen($filter_id_from)>0)
{
	$arFilter[">=ID"] = Trim($filter_id_from);
}

if (strlen($filter_id_to)>0)
{
	$arFilter["<=ID"] = Trim($filter_id_to);
}

if (strlen($filter_id_from)>0)
	$filter_id_from = Trim($filter_id_from);
else
	$filter_id_from = 0;

if (strlen($filter_id_to)>0)
	$filter_id_to = Trim($filter_id_to);
else
	$filter_id_to = 0;


$arResult = Array();

$arFilter['CANCELED'] = "N";
$arFilter['STATUS_ID'] = "F";
$arFilter['PAYED'] = "Y";

if(!empty($filter_pay_system) && is_array($filter_pay_system)) {
	$arFilter['DELIVERY_ID'] = $filter_pay_system;
}

$arSelectedFields = Array("ID", "STATUS_ID", "DATE_STATUS", "DELIVERY_ID");
$dbOrder = CSaleOrder::GetList(Array("ID" => "DESC"), $arFilter, false, false, $arSelectedFields);

$arShipments = [];
while($arOrder = $dbOrder->Fetch())
{
	$totalSum = 0.00;
	$order = Bitrix\Sale\Order::load($arOrder['ID']);
	$shipmentCollection = $order->getShipmentCollection();
	foreach ($shipmentCollection as $shipment)
	{
		if($shipment->isShipped()) {
			$arResult[$shipment->getDeliveryId()]['SUM'] += $order->getPrice();
			$totalSum += $order->getPrice();
			$arShipments[$shipment->getDeliveryId()] = $shipment->getDeliveryName();
			continue 2;
		}
	}
}

foreach($arResult as $key => $sum) {
	$arResult['ITEMS'][] = [
		"DELIVERY_ID" => $key,
		"SUM" => $sum['SUM']
	];
}

if(isset($_REQUEST['by'])) {
	usort($arResult['ITEMS'], function($a, $b) {
		if($_REQUEST['order'] == 'asc') {
			return $a[$_REQUEST['by']] <=> $b[$_REQUEST['by']];
		} else {
			return -($a[$_REQUEST['by']] <=> $b[$_REQUEST['by']]);
		}
	});
}

$arHeaders = array(
	array("id"=>"DELIVERY_ID", "content"=>"Служба доставки", "sort"=>"DELIVERY_ID", "default"=>true),
	array("id"=>"SUM", "content"=>"Сумма продаж",  "sort"=>"SUM", "default"=>true, "align" => "left"),
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

while ($arResult = $dbResult->GetNext())
{
	$row =& $lAdmin->AddRow($arResult["DATE"], $arResult);
	if(!empty($dbResult->arResult))
	{
		$row->AddViewField("DELIVERY_ID", $arShipments[$arResult['DELIVERY_ID']]);
		$row->AddViewField("SUM", CurrencyFormat($arResult['SUM'], "RUB"));
		$priceOnPage += $arResult["SUM"];
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
$APPLICATION->SetTitle("Способы доставки за период");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
	<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
	"Дата выполнения",
	"Дата присвоения статуса Выполнен",
	"ID"
	)
);

$oFilter->Begin();
?>
	<tr>
		<td>Дата создания заказа:</td>
		<td>
			<?echo CalendarPeriod("filter_insert_date_from", $filter_insert_date_from, "filter_insert_date_to", $filter_insert_date_to, "find_form", "Y")?>
		</td>
	</tr>
	<tr>
		<td>Дата присвоения заказу статуса Выполнен:</td>
		<td>
			<?echo CalendarPeriod("filter_status_date_from", $filter_status_date_from, "filter_status_date_to", $filter_status_date_to, "find_form", "Y")?>
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
			<input type="text" name="filter_id_from" OnChange="filter_id_from_Change()" value="<?echo (IntVal($filter_id_from)>0)?IntVal($filter_id_from):""?>" size="10">
			по
			<input type="text" name="filter_id_to" value="<?echo (IntVal($filter_id_to)>0)?IntVal($filter_id_to):""?>" size="10">
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
CAdminMessage::ShowNote("Сумма заказов в выборке: ".CurrencyFormat($priceOnPage, "RUB"));
?>
<script>
	document.body.addEventListener('DOMSubtreeModified', function(e) {

		if (e.target.id == "tbl_coupons_orders_result_div") {
			var element = document.getElementsByClassName('adm-info-message-title');
			var items = document.querySelectorAll('.adm-list-table-row > td:nth-child(2)');
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
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>