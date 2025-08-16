<?
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/sale/include.php");

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

$sTableID = "tbl_sales_for_the_period";
$oSort = new CAdminSorting($sTableID, "ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
	"filter_insert_date_from",
	"filter_insert_date_to",
	"filter_product_name",
	"filter_responsible_id"
);

if($lAdmin->IsDefaultFilter())
{
	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 2);
	$filter_insert_date_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 2));

	$filter_product_name = "";
	
	$set_filter = "Y";
}


$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_insert_date_from)>0)
{
	$arFilter["DATE_INSERT_FROM"] = Trim($filter_insert_date_from) . ' 00:00:00';
} else {
	$filter_insert_date_from_DAYS_TO_BACK = 1;
	$filter_insert_date_from = GetTime(time() - 86400);
	$arFilter["DATE_INSERT_FROM"] = Trim($filter_insert_date_from) . ' 00:00:00';
}

if (strlen($filter_insert_date_to)>0)
{
	$arFilter["DATE_INSERT_TO"] = Trim($filter_insert_date_to) . ' 23:59:59';
} else {
	$filter_insert_date_to_DAYS_TO_BACK = 1;
	$filter_insert_date_to = GetTime(time() - 86400) . ' 23:59:59';
	$arFilter["DATE_INSERT_TO"] = Trim($filter_insert_date_to) . ' 23:59:59';
}

if(strlen($filter_responsible_id) > 0) {
	$arFilter['RESPONSIBLE_ID'] = $filter_responsible_id;
}


$arResult = Array();

$arFilter['CANCELED'] = "N";
$arFilter['PAYED'] = "Y";


$arResponsible = [];
$rsUsers = CUser::GetList(($by = "personal_country"), ($order = "desc"), ["ACTIVE"  => "Y", "GROUPS_ID" => [1, 9, 10, 14]]);
while($arUser = $rsUsers->fetch()){
	$arResponsible[$arUser['ID']] = $arUser;
}

$dbOrder = CSaleOrder::GetList(Array("ID" => "DESC"), $arFilter, false, false, ["ID", "RESPONSIBLE_ID"]);
while($arOrder = $dbOrder->Fetch())
{
	$basket = Bitrix\Sale\Order::load($arOrder['ID'])->getBasket();
	foreach ($basket as $basketItem) {
		$productId = $basketItem->getProductId();
		$arElement = CIBlockElement::GetByID($productId)->fetch();
		$arProduct = CCatalogProduct::GetByID($productId);
		$imagePath = CFile::GetPath($arElement['PREVIEW_PICTURE']);

		if(strlen($filter_product_name) > 0 && strpos(strtolower($basketItem->getField('NAME')), strtolower($filter_product_name)) === false) {
			continue;
		}
		$arResult['ITEMS'][$arProduct['ID']] = [
			'ORDER_ID' => $arOrder['ID'],
			'PICTURE' => $imagePath,
			'NAME' => $basketItem->getField('NAME'),
			'URL' => CIBlock::GetAdminElementEditLink(GOODS_IBLOCK_ID, $productId),
			'COUNT' => $arResult['ITEMS'][$arProduct['ID']]['COUNT'] + $basketItem->getQuantity(),
			'PRICE' => $basketItem->getPrice(),
			'PRICE_FORMATED' => CurrencyFormat($basketItem->getPrice(), 'RUB'),
			'QUANTITY' => $arProduct['QUANTITY'],
			'QUANTITY_RESERVED' => $arProduct['QUANTITY_RESERVED'],
			'RESPONSIBLE' => $arResponsible[$arOrder['RESPONSIBLE_ID']]['NAME'] . " " . $arResponsible[$arOrder['RESPONSIBLE_ID']]['LAST_NAME'],
			'RESPONSIBLE_ID' => $arOrder['RESPONSIBLE_ID'],
		];
	}
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
	array("id"=>"ORDER_ID", "content"=>"Номер заказа", "sort"=>"ORDER_ID", "default"=>true),
	array("id"=>"PICTURE", "content"=>"Картинка",  "sort"=>"PICTURE", "default"=>true, "align" => "left"),
	array("id"=>"NAME", "content"=>"Наименование",  "sort"=>"NAME", "default"=>true, "align" => "left"),
	array("id"=>"PRICE", "content"=>"Цена за шт.",  "sort"=>"PRICE", "default"=>true, "align" => "left"),
	array("id"=>"COUNT", "content"=>"Кол-во",  "sort"=>"COUNT", "default"=>true, "align" => "left"),
	array("id"=>"FINAL_PRICE", "content"=>"Сумма",  "sort"=>"FINAL_PRICE", "default"=>true, "align" => "left"),
	array("id"=>"QUANTITY", "content"=>"Остаток",  "sort"=>"QUANTITY", "default"=>true, "align" => "left"),
	array("id"=>"QUANTITY_RESERVED", "content"=>"Резерв",  "sort"=>"QUANTITY_RESERVED", "default"=>true, "align" => "left"),
	array("id"=>"RESPONSIBLE_ID", "content"=>"Ответственный",  "sort"=>"RESPONSIBLE_ID", "default"=>true, "align" => "left"),
);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = is_array($arResult['ITEMS']) ? count($arResult['ITEMS']) : 0;
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

if($count) {
	$dbResult->NavStart();
}
$lAdmin->NavText($dbResult->GetNavPrint(""));

$priceOnPage = 0.0;

while ($arResult = $dbResult->GetNext())
{
	$row =& $lAdmin->AddRow($arResult["DATE"], $arResult);
	if(!empty($dbResult->arResult))
	{
		$row->AddViewField("ORDER_ID", '<a href="/bitrix/admin/sale_order_view.php?ID=' . $arResult['ORDER_ID'] . '">' . $arResult['ORDER_ID'] .'</a>');
		$row->AddViewField("PICTURE", '<img loading="lazy" style="max-width: 100px; max-height: 100px" src="' . $arResult['PICTURE'] . '"/>');
		$row->AddViewField("NAME", '<a href="' . $arResult['URL'] . '">' . $arResult['NAME'] . '</a>');
		$row->AddViewField("PRICE", $arResult['PRICE_FORMATED']);
		$row->AddViewField("COUNT", $arResult['COUNT']);
		$row->AddViewField("FINAL_PRICE", CurrencyFormat($arResult['COUNT'] * $arResult['PRICE'], 'RUB'));
		$row->AddViewField("QUANTITY", $arResult['QUANTITY']);
		$row->AddViewField("QUANTITY_RESERVED", $arResult['QUANTITY_RESERVED']);
		$row->AddViewField("RESPONSIBLE_ID", $arResult['RESPONSIBLE']);
		$priceOnPage += $arResult['COUNT'] * $arResult['PRICE'];
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
$APPLICATION->SetTitle("Продажи за период");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
	<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
	"Дата создания заказа",
	"Наименование товара",
	"Ответственный",
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
		<td>Наименование товара:</td>
		<td>
			<input type="text" name="filter_product_name" value="<?=$filter_product_name?>" size="40" class="adm-input">
		</td>
	</tr>
	<tr>
		<td>Ответственный</td>
		<td><?echo FindUserID("filter_responsible_id", $filter_responsible_id, "", "find_form");?></td>
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
CAdminMessage::ShowNote("Сумма товаров в выборке: " . CurrencyFormat($priceOnPage, "RUB"));
?>
<script>
	document.body.addEventListener('DOMSubtreeModified', function(e) {
		if (e.target.id == "<?= $sTableID?>_result_div") {
			var element = document.getElementsByClassName('adm-info-message-title');
			var items = document.querySelectorAll('.adm-list-table-row > td:nth-child(6)');
			var sum = 0.0;
			
			for (var i = 0; i < items.length; i++) {
				sum += parseFloat(items[i].innerHTML.replace(" руб.", "").replace(" ", ""));
				console.log(sum);
			}
			element[0].innerHTML = "Сумма товаров на странице: " + sum.toLocaleString('ru-RU', {
				style: 'currency',
				currency: 'RUB'
			});
		}
	});
</script>
<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>