<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Internals\StatusTable;
use Bitrix\Sale;
use \Bitrix\Sale\Exchange\Integration\Admin;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");
CModule::IncludeModule("wl.snailshop");

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

$sTableID = "tbl_roulete_winners";

$oSort = new CAdminSorting($sTableID, "USER_ID", "DESC");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
global $sort_by;
$sort_order = 'desc';
$sort_by = 'ORDER_ID';

if (!empty($_REQUEST['order']) && !empty($_REQUEST['by'])) {
	$sort_order = $_REQUEST['order'];
	$sort_by = $_REQUEST['by'];
}

$arResult = [];

$arWinners = [];
$dbPrizes = CIBlockElement::GetList([], ['IBLOCK_ID' => \WL\IblockUtils::getIdByCode('roulete')], false, false, ['ID', 'IBLOCK_ID', 'NAME']);
while ($obPrize = $dbPrizes->GetNextElement()) {
	$arPrize = $obPrize->GetFields();
	$arPrize['PROPERTIES'] = $obPrize->GetProperties();

	$arWinner = [];
	foreach ($arPrize['PROPERTIES']['WINNERS']['VALUE'] as $key => $orderId) {
		$arWinner['ORDER_ID'] = $orderId;
		$arWinner['DATE'] = $arPrize['PROPERTIES']['WINNERS']['DESCRIPTION'][$key];

		if (intval($orderId) > 0) {
			$arWinner['ORDER_ID_LINK'] = '/bitrix/admin/sale_order_view.php?ID=' . $orderId;

			$order = Bitrix\Sale\Order::load($orderId);
			if ($order) {
				$userId = $order->getUserId();
				$arUser = Bitrix\Main\UserTable::getList([
					'filter' => ['ID' => $userId],
					'select' => ['ID', 'NAME', 'LAST_NAME']
				])->fetch();

				$arWinner['USER_NAME'] = $arUser['NAME'] . " " . $arUser['LAST_NAME'];
				$arWinner['USER_NAME_LINK'] = '/bitrix/admin/user_edit.php?lang=ru&ID=' . $arUser['ID'];

				$arWinner['PRIZE'] = $arPrize['NAME'];
				$arWinner['PRIZE_LINK'] = CIBlock::GetAdminElementEditLink($arPrize['IBLOCK_ID'], $arPrize['ID']);

				$arWinners[] = $arWinner;
			} else {
				die('Заказ с номером ' . $orderId . ' не найден. Нужно разобраться куда он делся.');
			}
		} else {
			$arWinner['PRIZE'] = $arPrize['NAME'];
			$arWinner['PRIZE_LINK'] = CIBlock::GetAdminElementEditLink($arPrize['IBLOCK_ID'], $arPrize['ID']);

			$arWinners[] = $arWinner;
		}
	}
}

if (!empty($sort_by) && !empty($sort_order)) {
	usort($arWinners, function ($a, $b) {
		global $sort_by;
		global $sort_order;

		if ($sort_by == 'DATE') {
			$date1 = strtotime($a[$sort_by]);
			$date2 = strtotime($b[$sort_by]);

			switch ($sort_order) {
				case 'asc':
					return $date1 <=> $date2;
					break;
				case 'desc':
					return - ($date1 <=> $date2);
					break;
			}
		} else {
			switch ($sort_order) {
				case 'asc':
					return $a[$sort_by] <=> $b[$sort_by];
					break;
				case 'desc':
					return - ($a[$sort_by] <=> $b[$sort_by]);
					break;
			}
		}
	});
}

$arResult['ITEMS'] = $arWinners;


$lAdmin->AddHeaders(array(
	array("id" => "NUMBER", "content" => "№", "sort" => "NUMBER", "default" => true),
	array("id" => "DATE", "content" => "Дата выигрыша", "sort" => "DATE", "default" => true),
	array("id" => "ORDER_ID", "content" => "ID заказа",  "sort" => "ORDER_ID", "default" => true, "align" => "left"),
	array("id" => "USER_NAME", "content" => "Пользователь",  "sort" => "USER_NAME", "default" => true, "align" => "left"),
	array("id" => "PRIZE", "content" => "Приз",  "sort" => "PRIZE", "default" => true, "align" => "left"),
));
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult['ITEMS']);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

$i = 1;

while ($arResult = $dbResult->GetNext()) {
	$row = &$lAdmin->AddRow($i, $arResult);
	if (!empty($dbResult->arResult)) {
		$row->AddViewField("NUMBER", $i);
		$row->AddViewField("DATE", $arResult['DATE']);

		if ($arResult['USER_NAME_LINK']) {
			$row->AddViewField("ORDER_ID", '<a target="_blank" href="' . $arResult['ORDER_ID_LINK'] . '">' . $arResult['ORDER_ID'] . '</a>');
			$row->AddViewField("USER_NAME", '<a target="_blank" href="' . $arResult['USER_NAME_LINK'] . '">' . $arResult['USER_NAME'] . '</a>');
		} else {
			$row->AddViewField("ORDER_ID", '');
			$row->AddViewField("USER_NAME", $arResult['ORDER_ID']);
		}
		$row->AddViewField("PRIZE", '<a target="_blank" href="' . $arResult['PRIZE_LINK'] . '">' . $arResult['PRIZE'] . '</a>');
	}
	$i++;
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
$APPLICATION->SetTitle("[Рулетка] Список победителей");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>
<?
$lAdmin->DisplayList();
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php"); ?>