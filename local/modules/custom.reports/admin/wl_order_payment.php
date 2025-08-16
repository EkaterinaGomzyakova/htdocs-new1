<?php

use Bitrix\Main\Application;
use Bitrix\Sale\Internals\PaymentTable;
use Bitrix\Sale\Order;
use Bitrix\Main\Loader;

/** @global CMain $APPLICATION */
/** @global CUser $USER */
/** @global CDatabase $DB */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

Loader::includeModule('sale');
Loader::includeModule('currency');

$saleModulePermissions = $APPLICATION->GetGroupRight("sale");
if ($saleModulePermissions == "D")
	$APPLICATION->AuthForm("Доступ запрещен");
$bReadOnly = $saleModulePermissions < 'P';

IncludeModuleLangFile(__FILE__);
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/prolog.php");

$tableId = "b_sale_order_payment";
$curPage = Application::getInstance()->getContext()->getCurrent()->getRequest()->getRequestUri();
$lang = Application::getInstance()->getContext()->getLanguage();

$arUserGroups = $USER->GetUserGroupArray();

$arAccessibleSites = array();
$dbAccessibleSites = CSaleGroupAccessToSite::GetList(
	array(),
	array("GROUP_ID" => $arUserGroups),
	false,
	false,
	array("SITE_ID")
);
while ($arAccessibleSite = $dbAccessibleSites->Fetch()) {
	if (!in_array($arAccessibleSite["SITE_ID"], $arAccessibleSites)) {
		$arAccessibleSites[] = $arAccessibleSite["SITE_ID"];
	}
}

$sAdmin = new CAdminSorting($tableId, "ORDER_ID", "DESC");
$lAdmin = new CAdminList($tableId, $sAdmin);
$by = mb_strtoupper($sAdmin->getField());
$order = mb_strtoupper($sAdmin->getOrder());
$sort = [
	$by => $order,
];
if ($by !== 'ID') {
	$sort['ID'] = 'ASC';
}

$filterFields = [
	'filter_payment_id_from',
	'filter_payment_id_to',
	'filter_order_id_from',
	'filter_order_id_to',
	'filter_date_paid_from',
	'filter_date_paid_to',
	'filter_date_created_from',
	'filter_date_created_to',
	'filter_order_paid',
	'filter_site_id',
	'filter_pay_system_id',
	'filter_company_id',
	'filter_account_num',
	'filter_sum_from',
	'filter_sum_to',
	'filter_currency',
	'filter_pay_voucher_num',
	'filter_user_id',
	'filter_user_login',
	'filter_user_email',
	'filter_paid_by_user'
];

$currentFilter = $lAdmin->InitFilter($filterFields);
foreach ($filterFields as $fieldName) {
	if ($fieldName === 'filter_pay_system_id') {
		$currentFilter[$fieldName] ??= [];
		if (!is_array($currentFilter[$fieldName])) {
			$currentFilter[$fieldName] = [];
		}
	} else {
		$currentFilter[$fieldName] = (string)($currentFilter[$fieldName] ?? '');
	}
}

$arFilter = [];
$runtimeFields = [];

$filter_payment_id_from = (int)$currentFilter['filter_payment_id_from'];
$filter_payment_id_to = (int)$currentFilter['filter_payment_id_to'];
if ($filter_payment_id_from > 0 && $filter_payment_id_to > 0) {
	$arFilter['><ID'] = [
		$filter_payment_id_from,
		$filter_payment_id_to,
	];
}

$filter_order_id_from = (int)($currentFilter['filter_order_id_from']);
$filter_order_id_to = (int)($currentFilter['filter_order_id_to']);
if ($filter_order_id_from > 0 && $filter_order_id_to > 0) {
	$arFilter['><ORDER_ID'] = [
		$filter_order_id_from,
		$filter_order_id_to,
	];
}

$filter_sum_from = (float)($currentFilter['filter_sum_from']);
$filter_sum_to = (float)($currentFilter['filter_sum_to']);
if ($filter_sum_from > 0) {
	$arFilter['>=SUM'] = $filter_sum_from;
}
if ($filter_sum_to > 0) {
	$arFilter['<=SUM'] = $filter_sum_to;
}

if ($currentFilter['filter_order_paid'] !== '' && $currentFilter['filter_order_paid'] !== 'NOT_REF') {
	$arFilter['PAID'] = $currentFilter['filter_order_paid'];
}

if (
	!empty($currentFilter['filter_pay_system_id'])
	&& $currentFilter['filter_pay_system_id'][0] !== 'NOT_REF'
) {
	$arFilter['PAY_SYSTEM_ID'] = $currentFilter['filter_pay_system_id'];
}

if ($currentFilter['filter_paid_by_user'] == "Y") {
	$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
	$arFilter['=ORDER.PROPERTY.VALUE'] = 'Y';
} else {
	$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
	$arFilter['=ORDER.PROPERTY.VALUE'] = 'N';
}

if (!empty($currentFilter['filter_company_id'])) {
	if ($currentFilter['filter_company_id'] == "eshop") {
		$arFilter['COMPANY_ID'] = 1;
		$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
		$arFilter['=ORDER.PROPERTY.VALUE'] = 'Y';
	} elseif ($currentFilter['filter_company_id'] == 1) {
		$arFilter['COMPANY_ID'] = 1;
		$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
		$arFilter['=ORDER.PROPERTY.VALUE'] = 'N';
	} else {
		$arFilter['COMPANY_ID'] = $currentFilter['filter_company_id'];
		unset($arFilter['=ORDER.PROPERTY.CODE']);
		unset($arFilter['=ORDER.PROPERTY.VALUE']);
	}
} elseif ($currentFilter['filter_paid_by_user'] == "Y") {
	$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
	$arFilter['=ORDER.PROPERTY.VALUE'] = 'Y';
} else {
	$arFilter['=ORDER.PROPERTY.CODE'] = 'ORDER_CREATED_BY_BUYER';
	$arFilter['=ORDER.PROPERTY.VALUE'] = 'N';
}

$res = \Bitrix\Sale\Internals\CompanyTable::getList([]);
$companies = $res->fetchAll();

$companies['eshop'] = [
	"ID" => 'eshop',
	'NAME' => "Интернет-магазин"
];


CModule::IncludeModule("wl.snailshop");

if (!$USER->isAdmin() && WL\SnailShop::userIsStaff()) {
	$userShopId = WL\SnailShop::getUserShopId();

	if ($userShopId) {
		$filter_company_id = $userShopId;
		$arFilter["COMPANY_ID"] = $userShopId;

		foreach ($companies as $key => $company) {
			if ($company['ID'] != $userShopId) {
				unset($companies[$key]);
			}
		}
	}
}

if ($currentFilter['filter_account_num'] !== '') {
	$arFilter['ORDER.ACCOUNT_NUMBER'] = $currentFilter['filter_account_num'];
}

if ($currentFilter['filter_currency'] !== '' && $currentFilter['filter_currency'] !== 'NOT_REF') {
	$arFilter['CURRENCY'] = $currentFilter['filter_currency'];
}
if ($currentFilter['filter_pay_voucher_num'] !== '') {
	$arFilter['PAY_VOUCHER_NUM'] = $currentFilter['filter_pay_voucher_num'];
}
if ($currentFilter['filter_user_login'] !== '') {
	$arFilter["ORDER.USER.LOGIN"] = $currentFilter['filter_user_login'];
}
if ($currentFilter['filter_user_email'] !== '') {
	$arFilter["ORDER.USER.EMAIL"] = $currentFilter['filter_user_email'];
}

$filter_user_id = (int)$currentFilter['filter_user_id'];
if ($filter_user_id > 0) {
	$arFilter["ORDER.USER_ID"] = $filter_user_id;
}
if ($currentFilter['filter_date_paid_from'] !== '') {
	$arFilter[">=DATE_PAID"] = $currentFilter['filter_date_paid_from'];
}
if ($currentFilter['filter_date_paid_to'] !== '') {
	if ($arDate = ParseDateTime($currentFilter['filter_date_paid_to'], CSite::GetDateFormat("FULL", SITE_ID))) {
		if (mb_strlen($currentFilter['filter_date_paid_to']) < 11) {
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$currentFilter['filter_date_paid_to'] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=DATE_PAID"] = $currentFilter['filter_date_paid_to'];
	} else {
		$currentFilter['filter_date_paid_to'] = '';
	}
}

if ($currentFilter['filter_date_created_from'] !== '') {
	$arFilter[">=DATE_BILL"] = $currentFilter['filter_date_created_from'];
}
if ($currentFilter['filter_date_created_to'] !== '') {
	if ($arDate = ParseDateTime($currentFilter['filter_date_created_to'], CSite::GetDateFormat("FULL", SITE_ID))) {
		if (mb_strlen($currentFilter['filter_date_created_to']) < 11) {
			$arDate["HH"] = 23;
			$arDate["MI"] = 59;
			$arDate["SS"] = 59;
		}

		$currentFilter['filter_date_created_to'] = date($DB->DateFormatToPHP(CSite::GetDateFormat("FULL", SITE_ID)), mktime($arDate["HH"], $arDate["MI"], $arDate["SS"], $arDate["MM"], $arDate["DD"], $arDate["YYYY"]));
		$arFilter["<=DATE_BILL"] = $currentFilter['filter_date_created_to'];
	} else {
		$currentFilter['filter_date_created_to'] = '';
	}
}


if ($saleModulePermissions == "P") {
	$userCompanyList = \Bitrix\Sale\Services\Company\Manager::getUserCompanyList($USER->GetID());

	$arFilter[] = array(
		'LOGIC' => 'OR',
		'=COMPANY_ID' => $userCompanyList,
		'=ORDER.RESPONSIBLE_ID' => intval($USER->GetID()),
		'=ORDER.COMPANY_ID' => $userCompanyList,
		'=RESPONSIBLE_ID' => intval($USER->GetID())
	);
}

if (($ids = $lAdmin->GroupAction()) && !$bReadOnly) {
	$payments = array();

	$select = array(
		'ID', 'ORDER_ID'
	);

	$params = array(
		'select' => $select,
		'filter' => array_merge($arFilter, array('ID' => $ids)),
		'order'  => $sort,
		'limit'  => 1000,
		'runtime' => $runtimeFields
	);

	$dbResultList = PaymentTable::getList($params);

	$payments = $dbResultList->fetchAll();
	foreach ($payments as $item) {
		if ($item['ID'] <= 0 || $item['ORDER_ID'] <= 0)
			continue;

		$registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);

		/** @var Order $orderClass */
		$orderClass = $registry->getOrderClassName();

		/** @var \Bitrix\Sale\Order $currentOrder */
		$currentOrder = $orderClass::load($item['ORDER_ID']);

		/** @var \Bitrix\Sale\PaymentCollection $paymentCollection */
		$paymentCollection = $currentOrder->getPaymentCollection();

		/** @var \Bitrix\Sale\Payment $payment */
		$payment = $paymentCollection->getItemById($item['ID']);
		if (!$payment)
			continue;

		switch ($_REQUEST['action']) {
			case "delete":
				@set_time_limit(0);

				$result = $payment->delete();
				if ($result->isSuccess())
					$result = $currentOrder->save();

				if (!$result->isSuccess())
					$lAdmin->AddGroupError(implode('\n', $result->getErrorMessages()));

				break;
			case "paid":
			case "paid_n":
				@set_time_limit(0);
				$paid = $_REQUEST['action'] === 'paid' ? 'Y' : 'N';
				$result = $payment->setPaid($paid);
				if ($result->isSuccess())
					$result = $currentOrder->save();

				if (!$result->isSuccess())
					$lAdmin->AddGroupError(implode('\n', $result->getErrorMessages()));

				break;
		}
	}
}

$headers = array(
	array(
		"id"        => "DATE_PAID",
		"content"   => "Дата оплаты",
		"sort"      => "DATE_PAID",
		"default"   => true
	),
	array(
		"id"        => "ID",
		"content"   => "ID",
		"sort"      => "ID",
		"default"   => true
	),
	array(
		"id"        => "ORDER_ID",
		"content"   => "ID заказа",
		"sort"      => "ORDER_ID",
		"default"   => true
	),
	array(
		"id"        => "ACCOUNT_NUMBER",
		"content"   => "Номер заказа",
		"sort"      => "ORDER.ACCOUNT_NUMBER",
		"default"   => false
	),
	array(
		"id"        => "ORDER_USER_NAME",
		"content"   => "Покупатель",
		"sort"      => "ORDER_USER_NAME",
		"default"   => true
	),
	array(
		"id"        => "PAID",
		"content"   => "Оплата заказа",
		"sort"      => "PAID",
		"default"   => true
	),
	array(
		"id"        => "PAY_SYSTEM_NAME",
		"content"   => "Платежная система",
		"sort"      => "PAY_SYSTEM_NAME",
		"default"   => true
	),
	array(
		"id"        => "SUM",
		"content"   => "Сумма",
		"sort"      => "SUM",
		"default"   => true
	),
	array(
		"id"        => "COMPANY_BY",
		"content"   => "Компания",
		"sort"      => "COMPANY_BY.NAME",
		"default"   => true
	),
	array(
		"id"        => "PAY_VOUCHER_NUM",
		"content"   => "Номер платежного документа",
		"sort"      => "PAY_VOUCHER_NUM",
		"default"   => true
	),
	array(
		"id"        => "RESPONSIBLE_BY",
		"content"   => "Ответственный",
		"sort"      => "",
		"default"   => true
	),
	array(
		"id"        => "PS_STATUS",
		"content"   => "Статус ПС",
		"sort"      => "PS_STATUS",
		"default"   => false
	),
	array(
		"id"        => "PS_STATUS_CODE",
		"content"   => "Код статуса ПС",
		"sort"      => "PS_STATUS_CODE",
		"default"   => false
	),
	array(
		"id"        => "PS_STATUS_DESCRIPTION",
		"content"   => "Описание статуса ПС",
		"sort"      => "PS_STATUS_DESCRIPTION",
		"default"   => false
	),
	array(
		"id"        => "PS_STATUS_MESSAGE",
		"content"   => "Сообщение ПС",
		"sort"      => "PS_STATUS_MESSAGE",
		"default"   => false
	),
	array(
		"id"        => "PS_SUM",
		"content"   => "Сумма ПС",
		"sort"      => "PS_SUM",
		"default"   => false
	),
	array(
		"id"        => "PS_CURRENCY",
		"content"   => "Валюта ПС",
		"sort"      => "PS_CURRENCY",
		"default"   => false
	),
	array(
		"id"        => "PS_RESPONSE_DATE",
		"content"   => "Дата ответа ПС",
		"sort"      => "PS_RESPONSE_DATE",
		"default"   => false
	),
	array(
		"id"        => "PAY_VOUCHER_DATE",
		"content"   => "Дата создания платежного документа",
		"sort"      => "PAY_VOUCHER_DATE",
		"default"   => false
	),
	array(
		"id"        => "DATE_PAY_BEFORE",
		"content"   => "Оплатить до",
		"sort"      => "DATE_PAY_BEFORE",
		"default"   => false
	),
	array(
		"id"        => "DATE_BILL",
		"content"   => "Дата создания",
		"sort"      => "DATE_BILL",
		"default"   => false
	),
	array(
		"id"        => "PAY_SYSTEM_NAME",
		"content"   => "Платежная система",
		"sort"      => "PAY_SYSTEM_NAME",
		"default"   => false
	)
);


$select = array(
	'*',
	'COMPANY_BY_NAME' => 'COMPANY_BY.NAME',
	'RESPONSIBLE_BY_NAME' => 'RESPONSIBLE_BY.NAME',
	'RESPONSIBLE_BY_LAST_NAME' => 'RESPONSIBLE_BY.LAST_NAME',
	'ORDER_ACCOUNT_NUMBER' => 'ORDER.ACCOUNT_NUMBER',
	'ORDER_USER_LOGIN' => 'ORDER.USER.LOGIN',
	'ORDER_USER_NAME' => 'ORDER.USER.NAME',
	'ORDER_USER_LAST_NAME' => 'ORDER.USER.LAST_NAME',
	'ORDER_USER_ID' => 'ORDER.USER_ID',
	'ORDER_RESPONSIBLE_ID' => 'ORDER.RESPONSIBLE_ID',
);

$params = [
	'select' => $select,
	'filter' => $arFilter,
	'order'  => $sort,
	'runtime' => $runtimeFields
];

$arPaymentSumParams = $params;

$paymentSum = 0;
$Result = PaymentTable::getList($arPaymentSumParams);
while ($ob = $Result->Fetch()) {
	$paymentSum += $ob['SUM'];
}
echo '<script>document.getElementById("order_sum").innerHTML = "' . CurrencyFormat($paymentSum, "RUB") . '";</script>';

$usePageNavigation = true;
$navyParams = array();

$navyParams = CDBResult::GetNavParams(CAdminResult::GetNavSize($tableId));
if ($navyParams['SHOW_ALL']) {
	$usePageNavigation = false;
} else {
	$navyParams['PAGEN'] = (int)$navyParams['PAGEN'];
	$navyParams['SIZEN'] = (int)$navyParams['SIZEN'];
}

if ($usePageNavigation) {
	$params['limit'] = $navyParams['SIZEN'];
	$params['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}

$totalPages = 0;
$totalCount = 0;
if ($usePageNavigation) {
	$countQuery = new \Bitrix\Main\Entity\Query(PaymentTable::getEntity());
	$countQuery->addSelect(new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(1)'));
	$countQuery->setFilter($params['filter']);

	foreach ($params['runtime'] as $key => $field)
		$countQuery->registerRuntimeField($key, clone $field);

	$totalCount = $countQuery->exec()->fetch();
	unset($countQuery);
	$totalCount = (int)$totalCount['CNT'];

	if ($totalCount > 0) {
		$totalPages = ceil($totalCount / $navyParams['SIZEN']);

		if ($navyParams['PAGEN'] > $totalPages)
			$navyParams['PAGEN'] = $totalPages;

		$params['limit'] = $navyParams['SIZEN'];
		$params['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
	} else {
		$navyParams['PAGEN'] = 1;
		$params['limit'] = $navyParams['SIZEN'];
		$params['offset'] = 0;
	}
}

$dbResultList = new CAdminResult(PaymentTable::getList($params), $tableId);

if ($usePageNavigation) {
	$dbResultList->NavStart($params['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
	$dbResultList->NavRecordCount = $totalCount;
	$dbResultList->NavPageCount = $totalPages;
	$dbResultList->NavPageNomer = $navyParams['PAGEN'];
} else {
	$dbResultList->NavStart();
}

$lAdmin->NavText($dbResultList->GetNavPrint(""));

$lAdmin->AddHeaders($headers);

$visibleHeaders = $lAdmin->GetVisibleHeaderColumns();

while ($payment = $dbResultList->Fetch()) {

	if ($payment['ORDER_USER_NAME'] || $payment['ORDER_USER_LAST_NAME'])
		$userName = htmlspecialcharsbx($payment['ORDER_USER_NAME']) . " " . htmlspecialcharsbx($payment['ORDER_USER_LAST_NAME']);
	else
		$userName = htmlspecialcharsbx($payment['ORDER_USER_LOGIN']);

	$row = &$lAdmin->AddRow($payment['ID'], $payment);

	$row->AddField("ID", "<a href=\"sale_order_payment_edit.php?order_id=" . $payment['ORDER_ID'] . "&payment_id=" . $payment['ID'] . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $payment['ID'] . "</a>");
	$row->AddField("ORDER_ID", "<a href=\"sale_order_edit.php?ID=" . $payment['ORDER_ID'] . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . $payment['ORDER_ID'] . "</a>");
	$row->AddField("ACCOUNT_NUMBER", "<a href=\"sale_order_edit.php?ID=" . $payment['ORDER_ID'] . "&lang=" . $lang . GetFilterParams("filter_") . "\">" . htmlspecialcharsbx($payment['ORDER_ACCOUNT_NUMBER']) . "</a>");
	$row->AddField("SUM", \CCurrencyLang::CurrencyFormat($payment['SUM'], $payment['CURRENCY']));
	$row->AddField("PAID", ($payment['PAID'] == 'Y') ? "Да" : "Нет");
	$row->AddField("PAY_SYSTEM_NAME", "<a href='sale_pay_system_edit.php?ID=" . $payment['PAY_SYSTEM_ID'] . "&lang=" . $lang . "'>" . htmlspecialcharsbx($payment['PAY_SYSTEM_NAME']) . "</a>");
	$row->AddField("COMPANY_BY", "<a href='sale_pay_system_edit.php?ID=" . $payment['COMPANY_ID'] . "&lang=" . $lang . "'>" . htmlspecialcharsbx($payment['COMPANY_BY_NAME']) . "</a>");
	$row->AddField("ORDER_USER_NAME", "<a href='/bitrix/admin/user_edit.php?ID=" . $payment['ORDER_USER_ID'] . "&lang=" . $lang . "'>" . $userName . "</a>");
	$row->AddField("RESPONSIBLE_BY", "<a href=\"user_edit.php?ID=" . $payment['RESPONSIBLE_ID'] . "\">" . htmlspecialcharsbx($payment['RESPONSIBLE_BY_NAME']) . " " . htmlspecialcharsbx($payment['RESPONSIBLE_BY_LAST_NAME']) . "</a>");
	$arActions = array();
	$arActions[] = array("ICON" => "edit", "TEXT" => "Изменить", "ACTION" => $lAdmin->ActionRedirect("sale_order_payment_edit.php?order_id=" . $payment['ORDER_ID'] . "&payment_id=" . $payment['ID'] . "&lang=" . $lang . GetFilterParams("filter_") . ""), "DEFAULT" => true);

	if (!$bReadOnly) {
		$arActions[] = array("SEPARATOR" => true);
		$arActions[] = array("ICON" => "delete", "TEXT" => "Удалить", "ACTION" => "if(confirm('Вы уверены, что хотите удалить отмеченные записи?')) " . $lAdmin->ActionDoGroup($payment['ID'], "delete", "order_id=" . $payment['ORDER_ID']));
	}

	$row->AddActions($arActions);
}

$lAdmin->AddGroupActionTable([
	"delete" => "Удалить",
	"paid" => "Оплатить",
	"paid_n" => "Отменить оплату",
]);

$lAdmin->AddAdminContextMenu();

$lAdmin->AddFooter([
	[
		"title" => "Всего",
		"value" => $dbResultList->SelectedRowsCount(),
	],
	[
		"counter" => true,
		"title" => "Выбрано",
		"value" => "0",
	],
]);

$lAdmin->CheckListMode();

$APPLICATION->SetTitle("Оплаты по магазинам");
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<form name="find_form" method="GET" action="<?= $curPage ?>?">
	<?php
	$filterFields = array(
		"filter_order_id" => "ID заказа",
		"filter_order_paid" => "Оплачено",
		"filter_date_paid" => "Дата оплаты",
		"filter_date_created" => "Дата создания",
		"filter_site_id" => "Сайт",
		"filter_pay_system_id" => "Платежные системы",
		"filter_account_num" => "Номер заказа",
		"filter_sum" => "Стоимость заказа",
		"filter_currency" => "Валюта",
		"filter_pay_voucher_num" => "Номер платежного документа",
		"filter_user_id" => "Код покупателя",
		"filter_user_login" => "Логин покупателя",
		"filter_user_email" => "Email покупателя",
		"filter_paid_by_user" => "Оплачено на сайте",
	);

	$oFilter = new CAdminFilter(
		$tableId . "_filter",
		$filterFields
	);

	$oFilter->Begin();
	?>
	<tr>
		<td>Компания:</td>
		<td>
			<select name="filter_company_id">
				<option value="">Все</option>
				<? foreach ($companies as $company) { ?>
					<option value="<?= $company['ID'] ?>" <?= $filter_company_id == $company['ID'] ? ' selected' : '' ?>><?= $company['NAME'] ?></option>
				<? } ?>
			</select>
		</td>
	</tr>
	<tr>
		<td>ID:</td>
		<td>
			<script type="text/javascript">
				function changeFilterPaymentIdFrom() {
					if (document.find_form.filter_payment_id_to.value.length <= 0)
						document.find_form.filter_payment_id_to.value = document.find_form.filter_payment_id_from.value;
				}
			</script>
			с
			<input type="text" name="filter_payment_id_from" onchange="changeFilterPaymentIdFrom()" value="<?= htmlspecialcharsbx($currentFilter['filter_payment_id_from']); ?>" size="10">
			по
			<input type="text" name="filter_payment_id_to" value="<?= htmlspecialcharsbx($currentFilter['filter_payment_id_to']); ?>" size="10">
		</td>
	</tr>
	<tr>
		<td>ID заказа:</td>
		<td>
			<script type="text/javascript">
				function changeFilterOrderIdFrom() {
					if (document.find_form.filter_order_id_to.value.length <= 0)
						document.find_form.filter_order_id_to.value = document.find_form.filter_order_id_from.value;
				}
			</script>
			с
			<input type="text" name="filter_order_id_from" onchange="changeFilterOrderIdFrom()" value="<?= htmlspecialcharsbx($currentFilter['filter_order_id_from']); ?>" size="10">
			по
			<input type="text" name="filter_order_id_to" value="<?= htmlspecialcharsbx($currentFilter['filter_order_id_to']); ?>" size="10">
		</td>
	</tr>
	<tr>
		<td>Оплачено:</td>
		<td>
			<select name="filter_order_paid">
				<option value="NOT_REF">(Все)</option>
				<option value="Y" <?= ($currentFilter['filter_order_paid'] === 'Y' ? ' selected' : ''); ?>>Да</option>
				<option value="N" <?= ($currentFilter['filter_order_paid'] === 'N' ? ' selected' : ''); ?>>Нет</option>
			</select>
		</td>
	</tr>
	<tr>
		<td>Дата оплаты:</td>
		<td>
			<?= CalendarPeriod(
				"filter_date_paid_from",
				htmlspecialcharsbx($currentFilter['filter_date_paid_from']),
				"filter_date_paid_to",
				htmlspecialcharsbx($currentFilter['filter_date_paid_to']),
				"find_form",
				"Y"
			); ?>
		</td>
	</tr>
	<tr>
		<td>Дата создания:</td>
		<td>
			<?= CalendarPeriod(
				"filter_date_created_from",
				htmlspecialcharsbx($currentFilter['filter_date_created_from']),
				"filter_date_created_to",
				htmlspecialcharsbx($currentFilter['filter_date_created_to']),
				"find_form",
				"Y"
			); ?>
		</td>
	</tr>
	<?php
	$res = CSite::GetList();
	$siteInfo = array();
	while ($site = $res->Fetch()) {
		if ($saleModulePermissions >= "W" || in_array($site['ID'], $arAccessibleSites)) {
			$siteInfo[$site['ID']] = $site['SITE_NAME'];
		}
	}
	?>
	<tr>
		<td>Сайт:</td>
		<td>
			<select name="filter_site_id">
				<option value="NOT_REF">(Все)</option>
				<?php
				foreach ($siteInfo as $id => $siteName)
					echo '<option value="' . $id . '">' . htmlspecialcharsbx($siteName) . '</option>';
				?>
			</select>
		</td>
	</tr>
	<?php
	$params = [
		'select' => ['ID', 'NAME'],
		'filter' => ['ACTIVE' => 'Y'],
		'order' => ["SORT" => "ASC", "NAME" => "ASC"]
	];
	$res = \Bitrix\Sale\PaySystem\Manager::getList($params);
	$paySystems = $res->fetchAll();
	?>
	<tr>
		<td>Платежные системы:</td>
		<td>
			<select multiple name="filter_pay_system_id[]">
				<option value="NOT_REF">(Все)</option>
				<?php
				$ptRes = \Bitrix\Sale\Internals\PersonTypeTable::getList([
					'order' => ["SORT" => "ASC", "NAME" => "ASC"]
				]);

				$personTypes = [];
				while ($personType = $ptRes->fetch())
					$personTypes[$personType['ID']] = $personType;

				foreach ($paySystems as $paySystem) :
					$dbRestRes = \Bitrix\Sale\Services\PaySystem\Restrictions\Manager::getList([
						'select' => ['PARAMS'],
						'filter' => [
							'=CLASS_NAME' => '\\' . \Bitrix\Sale\Services\PaySystem\Restrictions\PersonType::class,
							'SERVICE_ID' => $paySystem['ID'],
						]
					]);

					$ptParams = $dbRestRes->fetch();
					$personTypeString = '';
					if (isset($ptParams['PARAMS']['PERSON_TYPE_ID']) && is_array($ptParams['PARAMS']['PERSON_TYPE_ID'])) {
						$psPt = array();
						foreach ($ptParams['PARAMS']['PERSON_TYPE_ID'] as $id)
							$psPt[] = ((mb_strlen($personTypes[$id]['NAME']) > 15) ? mb_substr($personTypes[$id]['NAME'], 0, 6) . "..." . mb_substr($personTypes[$id]['NAME'], -7) : $personTypes[$id]['NAME']) . "/" . $personTypes[$id]["LID"] . "";
						if ($psPt)
							$personTypeString = ' (' . join(', ', $psPt) . ')';
					}
				?><option title="<?= htmlspecialcharsbx($paySystem["NAME"] . $personTypeString); ?>" value="<?= htmlspecialcharsbx($paySystem["ID"]); ?>" <?= (in_array($paySystem["ID"], $currentFilter['filter_pay_system_id']) ? ' selected' : ''); ?>>[<?= htmlspecialcharsbx($paySystem["ID"]); ?>] <?= htmlspecialcharsbx($paySystem["NAME"] . $personTypeString); ?></option><?php
																																																																																												endforeach;
																																																																																													?>
			</select>
		</td>
	</tr>
	<tr>
		<td>Номер заказа:</td>
		<td>
			<input type="text" name="filter_account_num" value="<?= htmlspecialcharsbx($currentFilter['filter_account_num']); ?>">
		</td>
	</tr>
	<tr>
		<td>Стоимость заказа:</td>
		<td>
			от
			<input type="text" name="filter_sum_from" value="<?= htmlspecialcharsbx($currentFilter['filter_sum_from']); ?>" size="3">

			до
			<input type="text" name="filter_sum_to" value="<?= htmlspecialcharsbx($currentFilter['filter_sum_to']); ?>" size="3">
		</td>
	</tr>
	<tr>
		<td>Валюта:</td>
		<td>
			<?= CCurrency::SelectBox(
				"filter_currency",
				htmlspecialcharsbx($currentFilter['filter_currency']),
				"все",
				false,
				"",
				""
			); ?>
		</td>
	</tr>
	<tr>
		<td>Номер платежного документа:</td>
		<td>
			<input type="text" name="filter_pay_voucher_num" value="<?= htmlspecialcharsbx($currentFilter['filter_pay_voucher_num']); ?>">
		</td>
	</tr>
	<tr>
		<td>Код покупателя:</td>
		<td>
			<?= FindUserID("filter_user_id", $currentFilter['filter_user_id'], "", "find_form"); ?>
		</td>
	</tr>
	<tr>
		<td>Логин покупателя:</td>
		<td>
			<input type="text" name="filter_user_login" value="<?= htmlspecialcharsbx($currentFilter['filter_user_login']); ?>" size="40">
		</td>
	</tr>
	<tr>
		<td>Email покупателя:</td>
		<td>
			<input type="text" name="filter_user_email" value="<?= htmlspecialcharsbx($currentFilter['filter_user_email']); ?>" size="40">
		</td>
	</tr>
	<tr>
		<td>Оплачено на сайте:</td>
		<td>
			<? $checked = false;
			if ($currentFilter['filter_paid_by_user'] == "Y") {
				$checked = true;
			}
			?>
			<input type="checkbox" name="filter_paid_by_user" value="Y" <?= $checked ? "checked" : "" ?>size="40">
		</td>
	</tr>
	<?php
	$oFilter->Buttons([
		"table_id" => $tableId,
		"url" => $curPage,
		"form" => "find_form"
	]);

	$oFilter->End();
	?>
</form>
<?php
$lAdmin->DisplayList();

echo BeginNote();
?>
<div></div>
<span id="order_sum"><?= CurrencyFormat($paymentSum, "RUB"); ?></span>
<? echo EndNote(); ?>

<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
