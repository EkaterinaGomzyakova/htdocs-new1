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
	'filter_special_task',
	'filter_our_products'
);

if($lAdmin->IsDefaultFilter())
{
	$filter_status_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_status_date_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 30));
	
	$filter_insert_date_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
	$filter_insert_date_from = GetTime(time()-86400*COption::GetOptionString("sale", "order_list_date", 30));
	
	$filter_special_task = ' ';
	$filter_our_products = ' ';


	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = array();

if (strlen($filter_status_date_from)>0)
{
	$arFilter["DATE_STATUS_FROM"] = trim($filter_status_date_from);
}

if (strlen($filter_insert_date_from)>0)
{
	$arFilter["DATE_INSERT_FROM"] = trim($filter_insert_date_from);
}

if (strlen($filter_status_date_to)>0)
{
	$arFilter["DATE_STATUS_TO"] = trim($filter_status_date_to);
}

if (strlen($filter_insert_date_to)>0)
{
	$arFilter["DATE_INSERT_TO"] = trim($filter_insert_date_to);
}

if (strlen($filter_special_task)>0)
{
	$arFilter["SPECIAL_TASK"] = trim($filter_special_task);
}

if (strlen($filter_our_products)>0)
{
	$arFilter["OUR_PRODUCTS"] = trim($filter_our_products);
}

$arResult = Array();

$arFilter['CANCELED'] = "N";
$arFilter['STATUS_ID'] = "F";
$arFilter['PAYED'] = "Y";
$arFilter['!PAY_SYSTEM_ID'] = PAY_SYSTEMS_ID_EXCLUDE_FROM_SALARY;
$arFilter['SPECIAL_TASK'] = [];


$giftCertificateIds = [
	GIFT_CERTIFICATE_PRODUCT_ID
];

$dbCertificateProducts = CIBLockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, "!PROPERTY_GIFT_CERTIFICATE" => false], false, false, ['ID', 'PROPERTY_GIFT_CERTIFICATE']);
while($arCertificateProduct = $dbCertificateProducts->Fetch()) {
	$giftCertificateIds[] = $arCertificateProduct['ID'];
}


$arCheapBrands = [];
$dbCheapBrands = CIBlockElement::Getlist(
	[],
	[
		'IBLOCK_ID' => \WL\IblockUtils::getIdByCode('aspro_next_brands'),
		'CODE' => ['allies_of_skin', 'psa', 'holifrog', 'hempz']
	],
	false, false,
	['ID', 'IBLOCK_ID', 'NAME']
);
while($arBrand = $dbCheapBrands->Fetch()) {
	$arCheapBrands[$arBrand['ID']] = $arBrand['NAME'];
}


$arOurBrands = [];
$arOurBrandsFilter = ['tiam', 'some_by_mi', 'benton', 'isntree', 'lagom'];
$dbOurBrands = CIBlockElement::GetList(
	[],
	[
		'IBLOCK_ID' => \WL\IblockUtils::getIdByCode('aspro_next_brands'),
		'CODE' => $arOurBrandsFilter
	]
);
while($arBrand = $dbOurBrands->Fetch()) {
	$arOurBrands[$arBrand['ID']] = $arBrand['NAME'];
}


$arSpecialTasks = [];
if(strlen($arFilter["SPECIAL_TASKS"]) > 0) {
	$arSpecialTasksFilter = explode(',', $arFilter["SPECIAL_TASKS"]);
	foreach($arSpecialTasksFilter as &$productName) {
		$product = trim($productName);
	}
	$arSpecialTasksFilter = array_unique($arSpecialTasksFilter);

	if(!empty($arSpecialTasksFilter)) {
		$dbSpecialTasks = CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => \WL\IblockUtils::getIdByCode('cosmetics'),
				'NAME' => $arSpecialTasksFilter
			],
			false, false,
			['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_BRAND']
		);
		while($arProduct = $dbSpecialTasks->Fetch()) {
			$arSpecialTasks[$arProduct['ID']] = $arProduct;
		}
	}
}



$arOurProducts = [];
if(strlen($arFilter["OUR_PRODUCTS"]) > 0) {
	$arOurProductsFilter = explode(',', $arFilter["OUR_PRODUCTS"]);
	foreach($arOurProductsFilter as &$productName) {
		$product = trim($productName);
	}
	$arOurProductsFilter = array_unique($arOurProductsFilter);

	if(!empty($arOurProductsFilter)) {
		$dbOurProducts = CIBlockElement::GetList(
			[],
			[
				'IBLOCK_ID' => \WL\IblockUtils::getIdByCode('cosmetics'),
				'NAME' => $arOurProductsFilter
			],
			false, false,
			['ID', 'IBLOCK_ID', 'NAME', 'PROPERTY_BRAND']
		);
		while($arProduct = $dbOurProducts->Fetch()) {
			$arOurProducts[$arProduct['ID']] = $arProduct;
		}
	}
}


$arSelectedFields = Array("ID", "RESPONSIBLE_ID", "STATUS_ID", "DATE_STATUS");
$dbOrder = CSaleOrder::GetList(Array("ID" => "DESC"), $arFilter, false, false, $arSelectedFields);

while($arOrder = $dbOrder->Fetch())
{
	$totalSumByRetail = 0.00;
	$totalSumByEshop = 0.00;
	$arTotalSum = [
		'ESHOP' => 0.00,
		'CHEAP_BRANDS' => 0.00,
		'SPECIAL_TASKS' => 0.00,
		'OUR_BRANDS' => 0.00,
		'RETAIL_EXCLUDE_OTHERS' => 0.00,
	];
	$order = Bitrix\Sale\Order::load($arOrder['ID']);

	$isOrderCreatedByBuyer = false;
	$propertyCollection = $order->getPropertyCollection();
	foreach($propertyCollection as $property) {
		if($property->getField('CODE') == "ORDER_CREATED_BY_BUYER") {
			$isOrderCreatedByBuyer = ($property->getValue() == "Y") ? true : false;
		}
	}

	$shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
	foreach ($shipmentCollection as $shipment)
	{
		$systemItemCollection = $shipment->getShipmentItemCollection();
		$itemCollection = $systemItemCollection->getSellableItems(); 

		foreach ($itemCollection as $shipmentItem)
		{
			$basketItem = $shipmentItem->getBasketItem();
			if(in_array($basketItem->getProductId(), $giftCertificateIds) || CIBlockElement::GetIBlockByID($basketItem->getProductId()) == ADDITIONAL_CATALOG_IBLOCK_ID) {
				continue;
			}

			$arProduct = CIBlockElement::GetList([], ['ID' => $basketItem->getProductId(), \WL\IblockUtils::getIdByCode('cosmetics')], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_BRAND'])->Fetch();

			// 1% от продажи на сайте
			// 1% от продаж брендов AOS, PSA, holifrog, HEMPZ в розницу 
			// 10% от продаж спец задач
			// 5% от продаж «Наших брендов»
			// 2% от продаж в розницу всех брендов кроме выше и ниже 
			
			if($isOrderCreatedByBuyer) {
				$arResult[$arOrder['RESPONSIBLE_ID']]['ESHOP'] += $basketItem->getQuantity() * $basketItem->getPrice();
			}
			elseif(in_array($arProduct['PROPERTY_BRAND_VALUE'], array_keys($arCheapBrands)))
			{
				$arResult[$arOrder['RESPONSIBLE_ID']]['CHEAP_BRANDS'] += $basketItem->getQuantity() * $basketItem->getPrice();
			}
			elseif(!empty($arSpecialProducts) && in_array($arProduct['ID'], array_keys($arSpecialProducts)))
			{
				$arResult[$arOrder['RESPONSIBLE_ID']]['SPECIAL_TASKS'] += $basketItem->getQuantity() * $basketItem->getPrice();
			}
			elseif(in_array($arProduct['PROPERTY_BRAND_VALUE'], array_keys($arOurBrands)) || in_array($arProduct['ID'], array_keys($arOurProducts))) {
				$arResult[$arOrder['RESPONSIBLE_ID']]['OUR_BRANDS'] += $basketItem->getQuantity() * $basketItem->getPrice();
			} else {
				$arResult[$arOrder['RESPONSIBLE_ID']]['RETAIL_EXCLUDE_OTHERS'] += $basketItem->getQuantity() * $basketItem->getPrice();
			}
		}
	}
}


$arResponsibles = [];
$arResponsiblesKeys = array_keys($arResult);
foreach($arResponsiblesKeys as $person) {
	$arDBResponsible = CUser::GetByID($person)->Fetch();
	$arResponsibles[$person] = $arDBResponsible['LAST_NAME'] . " " . $arDBResponsible['NAME'];
}

foreach($arResult as $key => $sum) {
	$arResult['ITEMS'][] = [
		"RESPONSIBLE_ID" => $key,
		"SUM_ESHOP" => $sum['ESHOP'],
		"SUM_CHEAP_BRANDS" => $sum['CHEAP_BRANDS'],
		"SUM_SPECIAL_TASKS" => $sum['SPECIAL_TASKS'],
		"SUM_OUR_BRANDS" => $sum['OUR_BRANDS'],
		"SUM_RETAIL_EXCLUDE_OTHERS" => $sum['RETAIL_EXCLUDE_OTHERS'],
		"SALARY" => ($sum['ESHOP'] * 0.01) + ($sum['CHEAP_BRANDS'] * 0.01) + ($sum['SPECIAL_TASKS'] * 0.1) + ($sum['OUR_BRANDS'] * 0.05) + ($sum['RETAIL_EXCLUDE_OTHERS'] * 0.02)
	];
}

$arHeaders = array(
	array("id"=>"RESPONSIBLE_ID", "content"=>"Ответственный", "sort"=>"RESPONSIBLE_ID", "default"=>true),
	array("id"=>"SUM_ESHOP", "content"=>"Интернет-магазин",  "sort"=>"SUM_ESHOP", "default"=>true, "align" => "left"),
	array("id"=>"SUM_CHEAP_BRANDS", "content"=>"Бренды: " . implode(', ', $arOurBrandsFilter),  "sort"=>"SUM_CHEAP_BRANDS", "default"=>true, "align" => "left"),
	array("id"=>"SUM_SPECIAL_TASKS", "content"=>"Особые задания",  "sort"=>"SUM_SPECIAL_TASKS", "default"=>true, "align" => "left"),
	array("id"=>"SUM_OUR_BRANDS", "content"=>"Наши бренды",  "sort"=>"SUM_OUR_BRANDS", "default"=>true, "align" => "left"),
	array("id"=>"SUM_RETAIL_EXCLUDE_OTHERS", "content"=>"Розница, остальное",  "sort"=>"SUM_RETAIL_EXCLUDE_OTHERS", "default"=>true, "align" => "left"),
	array("id"=>"SUM_TOTAL", "content"=>"Сумма",  "sort"=>"SUM_TOTAL", "default"=>true, "align" => "left"),
	array("id"=>"SALARY", "content"=>"Зарплата",  "sort"=>"SALARY", "default"=>true, "align" => "left"),
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
		$row->AddViewField("RESPONSIBLE_ID", $arResponsibles[$arResult['RESPONSIBLE_ID']]);
		$row->AddViewField("SUM_ESHOP", '<nobr>' . CurrencyFormat($arResult['SUM_ESHOP'], "RUB") . '</nobr>');
		$row->AddViewField("SUM_CHEAP_BRANDS", '<nobr>' . CurrencyFormat($arResult['SUM_CHEAP_BRANDS'], "RUB") . '</nobr>');
		$row->AddViewField("SUM_SPECIAL_TASKS", '<nobr>' . CurrencyFormat($arResult['SUM_SPECIAL_TASKS'], "RUB") . '</nobr>');
		$row->AddViewField("SUM_OUR_BRANDS", '<nobr>' . CurrencyFormat($arResult['SUM_OUR_BRANDS'], "RUB") . '</nobr>');
		$row->AddViewField("SUM_RETAIL_EXCLUDE_OTHERS", '<nobr>' . CurrencyFormat($arResult['SUM_RETAIL_EXCLUDE_OTHERS'], "RUB") . '</nobr>');
		$row->AddViewField("SUM_TOTAL", '<nobr>' . CurrencyFormat($arResult['SUM_TOTAL'], "RUB") . '</nobr>');
		$row->AddViewField("SALARY", '<nobr>' . CurrencyFormat($arResult['SALARY'], "RUB") . '</nobr>');
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
$APPLICATION->SetTitle("Продажи сотрудников за период (без сертификатов)");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<?echo $APPLICATION->GetCurPage()?>?">
	<?
$oFilter = new CAdminFilter(
	$sTableID."_filter",
	array(
		"Дата создания заказа",
		"Дата присвоения заказу статуса Выполнен",
		"Специальные задания",
		"Добавить к нашим брендам товары"
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
		<td>Специальные задания (названия через запятую):</td>
		<td>
			<input type="text" name="filter_special_task" value="<?=$filter_special_task?>" size="40" class="adm-input">
		</td>
	</tr>
	<tr>
		<td>Добавить к нашим брендам товары (названия через запятую):</td>
		<td>
			<input type="text" name="filter_our_products" value="<?=$filter_our_products?>" size="40" class="adm-input">
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
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>