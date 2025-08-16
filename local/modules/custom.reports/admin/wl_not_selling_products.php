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

$arResult = [];
$sTableID = "tbl_not_selling_products";

$sort_by = 'ID';
$sort_order = 'DESC';

if (!empty($_REQUEST['order']) || !empty($_REQUEST['by'])) {
	if($_REQUEST['by'] != 'DATE_INSERT') {
		$sort_order = $_REQUEST['order'];
		$sort_by = $_REQUEST['by'];
	}
}

$oSort = new CAdminSorting($sTableID, $sort_by, $sort_order);
$lAdmin = new CAdminList($sTableID, $oSort);

$arFilterFields = array(
	"filter_date_insert_from",
	"filter_date_insert_to",
	"filter_only_available",
	"filter_name",
);

if ($lAdmin->IsDefaultFilter()) {
	$filter_date_insert_from_DAYS_TO_BACK = 30;
	$filter_date_insert_from = GetTime(time() - 86400 * 30);

	$filter_date_insert_to_DAYS_TO_BACK = 0;
	$filter_date_insert_to = GetTime(time());

	$filter_name = '';

	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arProductFilter = [];
$arBasketFilter = [];

if (strlen($filter_date_insert_from) > 0) {
	$arBasketFilter['>=DATE_INSERT'] = trim($filter_date_insert_from);
}

if (strlen($filter_date_insert_to) > 0) {
	$arBasketFilter['<=DATE_INSERT'] = trim($filter_date_insert_to) . ' 23:59:59';
}

if ($filter_only_available == "Y") {
	$arProductFilter['AVAILABLE'] = "Y";
}

if (strlen($filter_name) > 0) {
	$arProductFilter['%NAME'] = trim($filter_name);
}

$arProductFilter = array_merge(['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ACTIVE' => 'Y'], $arProductFilter);

$arProducts = [];
$dbProducts = CIBlockElement::GetList([$sort_by => $sort_order], $arProductFilter, false, false, ['ID', 'IBLOCK_ID', 'NAME', 'AVAILABLE', 'TYPE']);
while ($arProduct = $dbProducts->Fetch()) {
	if($arProduct['TYPE'] == 3) { //product with SKU
		$dbSKUs = CIBlockElement::GetList([$sort_by => $sort_order], ['PROPERTY_CML2_LINK' => $arProduct['ID']], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'AVAILABLE', 'TYPE']);
		while($arSKU = $dbSKUs->Fetch()) {
			$arProducts[$arSKU['ID']] = $arSKU;
		}
	} else {
		$arProducts[$arProduct['ID']] = $arProduct;
	}
}

foreach ($arProducts as $productId => $arProduct) {
	$arBasket = Bitrix\Sale\Internals\BasketTable::getList([
		'filter' => array_merge(['PRODUCT_ID' => $productId, '!ORDER_ID' => false], $arBasketFilter),
		'limit' => 1,
		'order' => ['DATE_INSERT' => 'DESC'],
		'select' => ['PRODUCT_ID', 'DATE_INSERT']
	])->fetch();


	if (empty($arBasket)) {

		$arLastBuyBasket = Bitrix\Sale\Internals\BasketTable::getList([
			'filter' => ['PRODUCT_ID' => $productId, '!ORDER_ID' => false],
			'limit' => 1,
			'order' => ['DATE_INSERT' => 'DESC'],
			'select' => ['PRODUCT_ID', 'DATE_INSERT']
		])->fetch();

		$arResult[] = [
			'ID' => $productId,
			'NAME' => $arProduct['NAME'],
			'DATE_INSERT' => strtotime($arLastBuyBasket['DATE_INSERT']),
			'DATE_INSERT_DISPLAY' => $arLastBuyBasket['DATE_INSERT'] ?: 'Никогда',
			'AVAILABLE' => ($arProduct['AVAILABLE'] == "Y") ? 'В наличии' : 'Отсутствует',
			'DETAIL_PAGE_URL' => CIBlock::GetAdminElementEditLink($arProduct['IBLOCK_ID'], $arProduct['ID']),
			'TYPE' => $arProduct['TYPE']
		];
	}
}

if($_REQUEST['by'] == "DATE_INSERT") {
	$sort_order = $_REQUEST['order'];
	usort($arResult, function($a, $b) use ($sort_order) {
		if($sort_order == 'asc') {
			return $a['DATE_INSERT'] <=> $b['DATE_INSERT'];
		}
		return -($a['DATE_INSERT'] <=> $b['DATE_INSERT']);
	});
}


$arHeaders = array(
	array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
	array("id" => "NAME", "content" => "Наименование",  "sort" => "NAME", "default" => true, "align" => "left"),
	array("id" => "DATE_INSERT", "content" => "Дата последней продажи",  "sort" => "DATE_INSERT", "default" => true, "align" => "left"),
	array("id" => "AVAILABLE", "content" => "Наличие",  "sort" => "AVAILABLE", "default" => true, "align" => "left"),
	array("id" => "TYPE", "content" => "Тип товара",  "sort" => "TYPE", "default" => true, "align" => "left"),
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
	$row = &$lAdmin->AddRow($arResult["ID"], $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("NAME", '<a target="_blank" href="' . $arResult['DETAIL_PAGE_URL'] . '">' . $arResult['NAME'] . '</a>');
		$row->AddViewField("DATE_INSERT", $arResult['DATE_INSERT_DISPLAY']);
		$row->AddViewField("AVAILABLE", $arResult['AVAILABLE']);
		$row->AddViewField("TYPE", ($arResult['TYPE'] == 1) ? 'Товар' : 'SKU');
	}
}

$lAdmin->AddFooter(
	array(
		array(
			"title" => GetMessage("MAIN_ADMIN_LIST_SELECTED"),
			"value" => $dbResult->SelectedRowsCount()
		),
	)
);

$lAdmin->AddAdminContextMenu();
$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle("Товары, которые давно не продавались");


require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Даты отсутствия продаж",
			"Наименование товара",
		)
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Даты отсутствия продаж:</td>
		<td>
			<? echo CalendarPeriod("filter_date_insert_from", $filter_date_insert_from, "filter_date_insert_to", $filter_date_insert_to, "find_form", "Y") ?>
		</td>
	</tr>
	<tr>
		<td>Проверять наличие</td>
		<td>
			<select name="filter_only_available">
				<option <? ($filter_only_available == "Y") ? 'checked' : ''?> value="Y">Да</option>
				<option <? ($filter_only_available != "Y") ? 'checked' : ''?> value="N">Нет</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Наименование товара:</td>
		<td>
			<input type="text" name="filter_name" value="<? echo $filter_name ?>" size="10">
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
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>