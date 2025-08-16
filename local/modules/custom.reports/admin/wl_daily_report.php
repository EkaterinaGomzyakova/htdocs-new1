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
	"filter_insert_date_from",
	"filter_insert_date_to",
	"filter_user_id"
);

$lAdmin->InitFilter($arFilterFields);

$arFilter = [];

if (strlen($filter_insert_date_from) > 0) {
	$arFilter[">DATE_INSERT"] = Trim($filter_insert_date_from);
} else {
	$arFilter[">DATE_INSERT"] = date('d.m.Y');
}


if (strlen($filter_insert_date_to) > 0) {
	$arFilter["<DATE_INSERT"] = Trim($filter_insert_date_to) . ' 23:59:59';
}

if (strlen($filter_user_id) > 0) {
	$arFilter["USER_ID"] = $filter_user_id;
}

$arResult = [];

$arFilter['CANCELED'] = "N";
$arFilter['PAYED'] = "Y";


$dbOrders = \Bitrix\Sale\Order::getList(['filter' => $arFilter, 'limit' => 100]);
while ($arOrder = $dbOrders->Fetch()) {
	$arResult[$arOrder['ID']]['USER_ID'] = $arOrder['USER_ID'];

	$arUser = Bitrix\Main\UserTable::getList([
		'filter' => ['ID' => $arOrder['USER_ID']],
		'select' => ['ID', 'NAME', 'LAST_NAME'],
	])->fetch();

	$userName = implode(' ', [$arUser['NAME'], $arUser['LAST_NAME']]);
	$arResult[$arOrder['ID']]['USER_LINK'] = '<a target="blank" href="/bitrix/admin/user_edit.php?ID=' . $arOrder['USER_ID'] .'">' . $userName . "</a>";

	$arResult[$arOrder['ID']]['ORDER_SUM'] = $arOrder['PRICE'];
	$arResult[$arOrder['ID']]['ORDER_DATE'] = $arOrder['DATE_INSERT']->format('d.m.y');

	$d7order = \Bitrix\Sale\Order::load($arOrder['ID']);
	$arDiscountNames = [];
	$discounts = $d7order->getDiscount()->getApplyResult();
	foreach ($discounts['DISCOUNT_LIST'] as $discount) {
		$arDiscountNames[] = $discount['NAME'];
	}
	if ($arDiscountNames) {
		$arResult[$arOrder['ID']]['ORDER_DISCOUNTS'] = implode("\n", $arDiscountNames);
	}

	$allOrderSum = 0.0;
	$allOrderProductCount = 0;
	$dbAllUserOrders = \Bitrix\Sale\Order::loadByFilter(['filter' => ['USER_ID' => $arOrder['USER_ID'], 'PAYED' => 'Y']]);
	foreach($dbAllUserOrders as $order) {
		$allOrderSum += $order->getPrice();

		$basket = $order->getBasket();
		foreach($basket as $basketItem) {
			$allOrderProductCount += $basketItem->getQuantity();
		}
	}
	$arResult[$arOrder['ID']]['MEDIAN_CHECK'] = $allOrderSum / $allOrderProductCount;

	$arResult[$arOrder['ID']]['CUMULATIVE_DISCOUNT'] = end(CCatalogDiscountSave::GetDiscount(["USER_ID" => $arOrder['USER_ID'], "USER_GROUPS" => $USER->GetUserGroupArray(), "SITE_ID" => "s1"]))['VALUE'];

}

$dbIsNewUserProp = \Bitrix\sale\Internals\OrderPropsValueTable::getList(['filter' => ['ORDER_ID' => array_keys($arResult), 'CODE' => 'IS_FIRST_ORDER']]);
while ($arProp = $dbIsNewUserProp->fetch()) {
	if ($arProp['VALUE'] == "Y") {
		$arResult[$arProp['ORDER_ID']]['IS_NEW_USER'] = 'Да';
	} else {
		$arResult[$arProp['ORDER_ID']]['IS_NEW_USER'] = 'Нет';
	}

	if(strlen($filter_is_new_user) > 0) {
		if($arProp['VALUE'] != $filter_is_new_user) {
			unset($arResult[$arProp['ORDER_ID']]);
		}
	}
}


$dbCouponList = \Bitrix\Sale\Internals\OrderCouponsTable::getList([
	'select' => ['COUPON', 'ORDER_ID'],
	'filter' => ['!COUPON' => false, 'ORDER_ID' => array_keys($arResult)],
]);
while ($arCoupon = $dbCouponList->Fetch()) {
	$arResult[$arCoupon['ORDER_ID']]['COUPONS'][] = $arCoupon['COUPON'];
}

foreach ($arResult as $key => $arOrder) {
	if ($arOrder['COUPONS']) {
		$arResult[$key]['COUPONS'] = implode(', ', $arOrder['COUPONS']);
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
	array("id" => "ORDER_DATE", "content" => "Дата заказа", "sort" => "ORDER_COUNT", "default" => true, "align" => "left"),
	array("id" => "USER_LINK", "content" => "Пользователь", "sort" => "USER_LINK", "default" => true),
	array("id" => "ORDER_SUM", "content" => "Сумма", "sort" => "ORDER_SUM", "default" => true, "align" => "left"),
	array("id" => "MEDIAN_CHECK", "content" => "Средний чек", "sort" => "MEDIAN_CHECK", "default" => true, "align" => "left"),
	array("id" => "CUMULATIVE_DISCOUNT", "content" => "Накопительная скидка", "sort" => "CUMULATIVE_DISCOUNT", "default" => true, "align" => "left"),
	array("id" => "COUPONS", "content" => "Купоны", "sort" => "COUPONS", "default" => true, "align" => "left"),
	array("id" => "ORDER_DISCOUNTS", "content" => "Скидки", "sort" => "ORDER_DISCOUNTS", "default" => true, "align" => "left"),
	array("id" => "IS_NEW_USER", "content" => "Новый покупатель", "sort" => "IS_NEW_USER", "default" => true, "align" => "left"),
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
	$row = &$lAdmin->AddRow($arResult["ORDER_DATE"], $arResult);
	$row->AddViewField("USER_LINK", html_entity_decode($arResult["USER_LINK"]));
	$row->AddViewField("ORDER_SUM", CurrencyFormat($arResult["ORDER_SUM"], "RUB"));
	$row->AddViewField("MEDIAN_CHECK", CurrencyFormat($arResult["MEDIAN_CHECK"], "RUB"));
	$row->AddViewField("CUMULATIVE_DISCOUNT", $arResult['CUMULATIVE_DISCOUNT'] . '%');
	$row->AddViewField("COUPONS", $arResult["COUPONS"]);
	$row->AddViewField("ORDER_DISCOUNTS", $arResult["ORDER_DISCOUNTS"]);
	$row->AddViewField("IS_NEW_USER", $arResult["IS_NEW_USER"]);
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
$APPLICATION->SetTitle("Ежедневный отчет");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		array(
			"Дата создания заказа",
			"ID пользователя",
			"Новый покупатель"
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
		<td>ID пользователя:</td>
		<td>
			
			<input type="text" value="<?= $filter_user_id?>" name="filter_user_id"> 
		</td>
	</tr>
	<tr>
		<td>Новый покупатель</td>
		<td>
			<select name="filter_is_new_user" value="<?= $filter_is_new_user?>">
				<option value="">Не указано</option>
				<option value="Y">Да</option>
				<option value="N">Нет</option>
			</select>
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