<?
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/sale/include.php");

use Bitrix\Main\UserTable;

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
    "filter_status_date_from",
    "filter_status_date_to",
    'filter_source'
);

if ($lAdmin->IsDefaultFilter()) {
    $filter_date_register_from_DAYS_TO_BACK = COption::GetOptionString("sale", "order_list_date", 30);
    $filter_date_register_from = GetTime(time() - 86400 * COption::GetOptionString("sale", "order_list_date", 30));

    $filter_date_register_to = false;

    $filter_source = false;

    $set_filter = "Y";
}

$lAdmin->InitFilter($arFilterFields);

$arResult['ITEMS'] = [];

if (empty($filter_source)) {
    $arOrderFilter = [
        'PROPERTY.CODE' => 'BUYER_SOURCE',
        '!PROPERTY.VALUE' => false,
    ];
} else {
    $arOrderFilter = [
        'PROPERTY.CODE' => 'BUYER_SOURCE',
        'PROPERTY.VALUE' => $filter_source,
    ];
}

$arUserFilter = [];
if (!empty($filter_date_register_from)) {
    $arUserFilter['>=DATE_REGISTER'] = $filter_date_register_from;
}

if (!empty($filter_date_register_to)) {
    $arUserFilter['<=DATE_REGISTER'] = $filter_date_register_to . " 23:59:59";
}


$arPropVariants = [];
$arBuyerSourceProp = CSaleOrderProps::GetList([], ['CODE' => 'BUYER_SOURCE'], false, false, ['ID'])->Fetch();
$dbPropVariants = CSaleOrderPropsVariant::GetList([], ['ORDER_PROPS_ID' => $arBuyerSourceProp['ID']], false, false, ['VALUE', 'NAME']);
while ($arPropVariant = $dbPropVariants->Fetch()) {
    $arPropVariants[$arPropVariant['VALUE']] = $arPropVariant['NAME'];
}

$dbOrders = \Bitrix\Sale\Order::loadByFilter([
    'filter' => $arOrderFilter,
    'select' => ['ID'],
]);

foreach ($dbOrders as $dbOrder) {
    $buyerSource = '';
    $propertyCollection = $dbOrder->getPropertyCollection();
    foreach ($propertyCollection as $property) {
        if ($property->getField('CODE') == "BUYER_SOURCE") {
            $buyerSource = $property->getValue();
            break;
        }
    }

    $arResult['ITEMS'][$dbOrder->getUserId()] = [
        'ID' => $dbOrder->getUserId(),
        'SOURCE' => $arPropVariants[$buyerSource],
        'NOT_FOUND' => true,
    ];
}

if (!empty($arResult['ITEMS'])) {

    $dbStat = \Bitrix\Sale\Internals\BuyerStatisticTable::getList([
        'select' => ['COUNT_FULL_PAID_ORDER', 'SUM_PAID', 'LAST_ORDER_DATE', 'USER_ID'],
        'filter' => ['USER_ID' => array_keys($arResult['ITEMS'])],
    ]);
    while ($arStat = $dbStat->Fetch()) {
        $arResult['ITEMS'][$arStat['USER_ID']]['LAST_ORDER_DATE'] = $arStat['LAST_ORDER_DATE']->format('d.m.Y');
        $arResult['ITEMS'][$arStat['USER_ID']]['COUNT_FULL_PAID_ORDER'] = $arStat['COUNT_FULL_PAID_ORDER'];
        $arResult['ITEMS'][$arStat['USER_ID']]['SUM_PAID'] = $arStat['SUM_PAID'];
    }

    $arUserFilter = array_merge(['ID' => array_keys($arResult['ITEMS'])], $arUserFilter);
    $dbUser = UserTable::getList([
        'select' => ['DATE_REGISTER', 'NAME', 'LAST_NAME', 'ID'],
        'filter' => $arUserFilter,
    ]);
    while ($arUser = $dbUser->Fetch()) {
        $arResult['ITEMS'][$arUser['ID']]['NAME'] = $arUser['NAME'] . ' ' . $arUser['LAST_NAME'];
        $arResult['ITEMS'][$arUser['ID']]['DATE_REGISTER'] = $arUser['DATE_REGISTER']->format('d.m.Y');
        unset($arResult['ITEMS'][$arUser['ID']]['NOT_FOUND']);
    }

    foreach ($arResult['ITEMS'] as $key => $arItem) {
        if ($arItem['NOT_FOUND']) {
            unset($arResult['ITEMS'][$key]);
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

$arHeaders = array(
    array("id" => "ID", "content" => "ID", "sort" => "ID", "default" => true),
    array("id" => "NAME", "content" => "Имя",  "sort" => "NAME", "default" => true, "align" => "left"),
    array("id" => "SOURCE", "content" => "Источник",  "sort" => "SOURCE", "default" => true, "align" => "left"),
    array("id" => "DATE_REGISTER", "content" => "Дата регистрации",  "sort" => "DATE_REGISTER", "default" => true, "align" => "left"),
    array("id" => "LAST_ORDER_DATE", "content" => "Дата последнего заказа",  "sort" => "LAST_ORDER_DATE", "default" => true, "align" => "left"),
    array("id" => "COUNT_FULL_PAID_ORDER", "content" => "Оплачено заказов",  "sort" => "COUNT_FULL_PAID_ORDER", "default" => true, "align" => "left"),
    array("id" => "SUM_PAID", "content" => "Сумма",  "sort" => "SUM_PAID", "default" => true, "align" => "left"),
);

$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['ITEMS']);

$dbResult = new CAdminResult($dbResult, $sTableID);

$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

while ($arResult = $dbResult->GetNext()) {
    $row = &$lAdmin->AddRow($arResult["ID"], $arResult);
    if (!empty($dbResult->arResult)) {
        $row->AddViewField("NAME", '<a href="/bitrix/admin/sale_buyers_profile.php?USER_ID=' . $arResult['ID'] . '" target="_blank">' . $arResult['NAME'] . '</a>');
        $row->AddViewField("SOURCE", $arResult['SOURCE']);
        $row->AddViewField("DATE_REGISTER", $arResult['DATE_REGISTER']);
        $row->AddViewField("LAST_ORDER_DATE", $arResult['LAST_ORDER_DATE']);
        $row->AddViewField("COUNT_FULL_PAID_ORDER", $arResult['COUNT_FULL_PAID_ORDER']);
        $row->AddViewField("SUM_PAID", CurrencyFormat($arResult['SUM_PAID'], "RUB"));
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
$APPLICATION->SetTitle("Источники покупателей");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
    <?
    $oFilter = new CAdminFilter(
        $sTableID . "_filter",
        array(
            "Дата регистрации",
            "Источник",
        )
    );

    $oFilter->Begin();
    ?>
    <tr>
        <td>Дата регистрации:</td>
        <td>
            <? echo CalendarPeriod("filter_date_register_from", $filter_date_register_from, "filter_date_register_to", $filter_date_register_to, "find_form", "Y") ?>
        </td>
    </tr>
    <tr>
        <td>Источник:</td>
        <td>
            <select id="filter_source" name="filter_source">
                <option value="">Не выбрано</option>
                <? foreach ($arPropVariants as $key => $name) { ?>
                    <option value="<?= $key ?>" <? if ($filter_source == $key) { ?>selected<? } ?>><?= $name ?></option>
                <? } ?>
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