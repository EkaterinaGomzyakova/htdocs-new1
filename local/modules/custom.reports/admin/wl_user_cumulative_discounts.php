<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

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

$sTableID = "tbl_user_cumulative_discounts";

$oSort = new CAdminSorting($sTableID, "USER_ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = [
	'filter_deny_sms'
];

if ($lAdmin->IsDefaultFilter()) {
	$set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arFilter = [];

if ($filter_deny_sms == "Y") {
	$arFilter["UF_DENY_SMS"] = false;
}
$arDeliveries = [];
$dbDeliveries = Bitrix\Sale\Delivery\Services\Table::getList([
	'select' => ['ID', 'NAME']
]);
while ($arDelivery = $dbDeliveries->fetch()) {
	$arDeliveries[$arDelivery['ID']] = $arDelivery;
}

$arResult = [];

global $DB;
$arSaleUsers = [];

$arSaleUsers = array_column(Bitrix\Sale\Internals\FuserTable::getList(['filter' => ['!USER_ID' => false], 'select' => ['USER_ID']])->fetchAll(), 'USER_ID');

$dbSaleUsers = $DB->Query('SELECT USER_ID FROM b_sale_fuser WHERE USER_ID IS NOT NULL');
while ($arSaleUser = $dbSaleUsers->Fetch()) {
	$arSaleUsers[] = $arSaleUser['USER_ID'];
}


$arUsers = [];
$arFilter = array_merge($arFilter, ["ACTIVE" => "Y", "=ID" => $arSaleUsers]);

$dbUsers = Bitrix\Main\UserTable::getList([
	'filter' => $arFilter,
	'select' => ['ID', 'NAME', 'LAST_NAME', 'PERSONAL_PHONE', 'UF_DENY_SMS', 'PERSONAL_BIRTHDAY'],
	'order' => ['ID' => 'DESC']
]);

while ($arUser = $dbUsers->fetch()) {
	$parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);

	$arUsers[$arUser['ID']] = [
		"USER_ID" => $arUser['ID'],
		"NAME" => $arUser['NAME'] . " " . $arUser['LAST_NAME'],
		"PHONE" => $parsedPhone->format(Format::E164),
		"PERSONAL_BIRTHDAY" => $arUser['PERSONAL_BIRTHDAY'],
		"DENY_SMS" => $arUser['UF_DENY_SMS'] ? 'Да' : 'Нет',
	];

	$tmpDiscount = end(CCatalogDiscountSave::GetDiscount(["USER_ID" => $arUser['ID'], "USER_GROUPS" => CUser::GetUserGroup($arUser['ID']), "SITE_ID" => "s1"]));
	if (isset($tmpDiscount['VALUE'])) {
		$arUsers[$arUser['ID']]['DISCOUNT'] = $tmpDiscount['VALUE'];
	} else {
		$arUsers[$arUser['ID']]['DISCOUNT'] = false;
	}

	$dbUserOrders = CSaleOrder::GetList([], ["STATUS_ID" => "F", "PAYED" => "Y", "USER_ID" => $arUser['ID']], ['SUM' => "SUM_PAID"], false, ['ID', 'SUM_PAID', 'USER_ID']);
	if ($arUserOrder = $dbUserOrders->Fetch()) {
		$arUsers[$arUser['ID']]['TOTAL_SUM'] = $arUserOrder['SUM_PAID'];
	}

	$dbLastOrder = CSaleOrder::GetList(['DATE_INSERT' => "DESC"], ["STATUS_ID" => "F", "PAYED" => "Y", "USER_ID" => $arUser['ID']], false, ["nPageSize" => 1], ["ID", "DATE_INSERT"]);
	if ($arLastOrder = $dbLastOrder->Fetch()) {
		if ($_REQUEST['set_filter'] == "Y" && isset($_REQUEST["filter_insert_date_from"]) && isset($_REQUEST["filter_insert_date_to"])) {
			if (strtotime($arLastOrder['DATE_INSERT']) > strtotime($_REQUEST['filter_insert_date_from'] . " 00:00:00") && strtotime($arLastOrder['DATE_INSERT']) < strtotime($_REQUEST['filter_insert_date_to'] . " 23:59:59")) {
				$arUsers[$arUser['ID']]['LAST_ORDER_DATE'] = $arLastOrder['DATE_INSERT'];
			} else {
				unset($arUsers[$arUser['ID']]);
				continue;
			}
		} else {
			$arUsers[$arUser['ID']]['LAST_ORDER_DATE'] = $arLastOrder['DATE_INSERT'];
		}
		$d7order = \Bitrix\Sale\Order::load($arLastOrder['ID']);
		$deliveryId = current($d7order->getDeliverySystemId());
		$arUsers[$arUser['ID']]['DELIVERY_NAME'] = $arDeliveries[$deliveryId]['NAME'];
	} else {
		unset($arUsers[$arUser['ID']]);
		continue;
	}

	$arUsers[$arUser['ID']]['BONUS_BALANCE'] = 0;
	$arUserBonusAccount = CSaleUserAccount::GetByUserID($arUser['ID'], "RUB");
	if($arUserBonusAccount) {
		$arUsers[$arUser['ID']]['BONUS_BALANCE'] = intval($arUserBonusAccount['CURRENT_BUDGET']);
	}
}


global $by, $order;
$by = (isset($by) ? $by : "DISCOUNT");
$order = (isset($order) ? $order : "asc");
usort($arUsers, function ($a, $b) {
	global $by, $order;

	if ($by == "LAST_ORDER_DATE") {
		if ($order == "asc") {
			return strtotime($a['LAST_ORDER_DATE']) <=> strtotime($b['LAST_ORDER_DATE']);
		} else {
			return - (strtotime($a['LAST_ORDER_DATE']) <=> strtotime($b['LAST_ORDER_DATE']));
		}
	} else {
		if ($order == "asc") {
			return $a[$by] <=> $b[$by];
		} else {
			return - ($a[$by] <=> $b[$by]);
		}
	}
});

$arResult['ITEMS'] = $arUsers;


$lAdmin->AddHeaders(array(
	array("id" => "USER_ID", "content" => "ID", "sort" => "USER_ID", "default" => true),
	array("id" => "NAME", "content" => "Имя",  "sort" => "NAME", "default" => true, "align" => "left"),
	array("id" => "PHONE", "content" => "Телефон",  "sort" => "PHONE", "default" => true, "align" => "left"),
	array("id" => "PERSONAL_BIRTHDAY", "content" => "Дата рождения",  "sort" => "PERSONAL_BIRTHDAY", "default" => true, "align" => "left"),
	array("id" => "DISCOUNT", "content" => "Скидка",  "sort" => "DISCOUNT", "default" => true, "align" => "left"),
	array("id" => "BONUS_BALANCE", "content" => "Бонусы",  "sort" => "BONUS_BALANCE", "default" => true, "align" => "left"),
	array("id" => "LAST_ORDER_DATE", "content" => "Последний заказ",  "sort" => "LAST_ORDER_DATE", "default" => true, "align" => "left"),
	array("id" => "DELIVERY_NAME", "content" => "Последняя служба доставки",  "sort" => "DELIVERY_NAME", "default" => true, "align" => "left"),
	array("id" => "TOTAL_SUM", "content" => "Сумма покупок",  "sort" => "TOTAL_SUM", "default" => true, "align" => "left"),
	array("id" => "DENY_SMS", "content" => "Отказ от смс",  "sort" => "DENY_SMS", "default" => true, "align" => "left"),
));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult['ITEMS']);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($arResult["USER_ID"], $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("USER_ID", $arResult['USER_ID']);
		$row->AddViewField("NAME", $arResult['NAME']);
		$row->AddViewField("PHONE", $arResult['PHONE']);
		$row->AddViewField("PERSONAL_BIRTHDAY", $arResult['PERSONAL_BIRTHDAY']);
		$row->AddViewField("DISCOUNT", $arResult['DISCOUNT'] ? $arResult['DISCOUNT'] . "%" : "0%");
		$row->AddViewField("BONUS_BALANCE", $arResult['BONUS_BALANCE']);
		$row->AddViewField("LAST_ORDER_DATE", $arResult['LAST_ORDER_DATE']);
		$row->AddViewField("TOTAL_SUM", $arResult['TOTAL_SUM']);
		$row->AddViewField("DENY_SMS", $arResult['DENY_SMS']);
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
$APPLICATION->SetTitle("Накопительные скидки покупателей");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
	<?
	$oFilter = new CAdminFilter(
		$sTableID . "_filter",
		[
			"Без отказа от смс"
		]
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Без отказа от смс:</td>
		<td>
			<input type="checkbox" name="filter_deny_sms" <? if ($filter_deny_sms == true) echo "checked"; ?> value="Y" />
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