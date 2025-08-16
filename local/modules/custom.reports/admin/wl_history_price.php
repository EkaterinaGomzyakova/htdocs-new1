<?

use Bitrix\Catalog\GroupLangTable;
use WL\HL;

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

$sTableID = "report_history_price";
$oSort = new CAdminSorting($sTableID, "NAME", "asc");
$lAdmin = new CAdminList($sTableID, $oSort);

global $sort_order;
$sort_order = $_REQUEST['order'];

$arFilterFields = array(
    "filter_product_name",
    "filter_product_id"
);

if ($lAdmin->IsDefaultFilter()) {
    $filter_product_name = "";
}

$lAdmin->InitFilter($arFilterFields);

$filter = [
    'IBLOCK_ID' => [GOODS_IBLOCK_ID, SKU_IBLOCK_ID],
];
if (!empty($filter_product_name)) {
    $filter['%NAME'] = $filter_product_name;
}
if (!empty($filter_product_id)) {
    $filter['ID'] = $filter_product_id;
}

$rows = CIBlockElement::GetList([], $filter, false, false, ['ID', 'NAME']);
$arResult['PRODUCTS'] = [];
while ($row = $rows->fetch()) {
    $arResult['PRODUCTS'][$row['ID']] = $row;
}

$items = HL::table('HistoryPrice')->filter(['UF_PRODUCT_ID' => array_keys($arResult['PRODUCTS'])])->sort(['ID' => 'DESC'])->all();

$arResult['PRICE_TYPE'] = [];
$arResult['PRICE_TYPE'][0] = [
    'CATALOG_GROUP_ID' => 0,
    'NAME' => 'Закупочная цена',
];

$rows = GroupLangTable::getList(['filter' => ['LANG' => 'ru']])->fetchAll();
foreach ($rows as $row) {
    $arResult['PRICE_TYPE'][$row['CATALOG_GROUP_ID']] = $row;
}

foreach ($arResult['PRODUCTS'] as $product) {
    $report[$product['ID']] = $product;
    $report[$product['ID']]['PRICES'] = [];

    foreach ($arResult['PRICE_TYPE'] as $priceType) {
        $report[$product['ID']]['PRICES'][$priceType['CATALOG_GROUP_ID']] = [
            'NAME' => $priceType['NAME'],
            'ID' => $priceType['CATALOG_GROUP_ID'],
            'ITEMS' => []
        ];
    }
}

foreach ($items as $item) {
    if (isset($report[$item['UF_PRODUCT_ID']])) {
        $report[$item['UF_PRODUCT_ID']]['PRICES'][$item['UF_PRICE_TYPE']]['ITEMS'][] = [
            'VALUE' => $item['UF_PRICE'],
            'DATE' => $item['UF_TIMESTAMP']->format('d.m.Y H:i'),
            'PREV_VALUE' => null
        ];
    }
}

foreach ($report as $rowKey => &$reportItem) {
    if(empty($reportItem['PRICES'])){
        unset($report[$rowKey]);
    }

    foreach ($reportItem['PRICES'] as &$priceType) {
        if(empty($priceType['ITEMS'])){
            unset($priceType);
        }

        foreach ($priceType['ITEMS'] as $key => &$item) {
            $nextValue = $priceType['ITEMS'][$key + 1];
            if($nextValue){
                $item['PREV_VALUE'] = $nextValue['VALUE'];
                $item['DIFF'] = $item['VALUE'] - $item['PREV_VALUE'];
                $item['DIFF_PERCENT'] = $item['PREV_VALUE'] ? round($item['DIFF'] / $item['PREV_VALUE']  * 100) : 100;
            }
        }
        unset($item);
    }

    if(empty($reportItem['PRICES'])) {
        unset($reportItem);
    }

    unset($priceType);
}

unset($reportItem);

$arResult['REPORT'] = [];
foreach ($report as $reportItem) {
    $arResult['REPORT'][] = [
        'PRODUCT_ID' => $reportItem['ID'],
        'NAME' => $reportItem['NAME'] . ' [' . $reportItem['ID'] . ']',
        'PRICE_TYPE' => '',
        'PRICE' => '',
        'DIFF' => '',
        'DATE' => '',
    ];
    foreach ($reportItem['PRICES'] as $priceType) {
        foreach ($priceType['ITEMS'] as $item) {
            $arResult['REPORT'][] = [
                'PRODUCT_ID' => $reportItem['ID'],
                'NAME' => '',
                'PRICE_TYPE' => $priceType['NAME'],
                'PRICE' => CCurrencyLang::CurrencyFormat($item['VALUE'], 'RUB'),
                'DIFF' => $item['DIFF'],
                'DIFF_PERCENT' => $item['DIFF_PERCENT'],
                'DATE' => $item['DATE'],
            ];
        }
    }
    $arResult['REPORT'][] = [
        'PRODUCT_ID' => '',
        'NAME' => '',
        'PRICE_TYPE' => '',
        'PRICE' => '',
        'DIFF' => '',
        'DATE' => '',
    ];
}

$arHeaders = array(
    array("id" => "NAME", "content" => "Товар", "sort" => false, "default" => true, "align" => "left"),
    array("id" => "PRICE_TYPE", "content" => "Тип цены", "sort" => false, "default" => true, "align" => "left"),
    array("id" => "PRICE", "content" => "Цены", "sort" => false, "default" => true, "align" => "left"),
    array("id" => "DIFF", "content" => "Изменение цены", "sort" => false, "default" => true, "align" => "left"),
    array("id" => "DATE", "content" => "Дата", "sort" => false, "default" => true, "align" => "left"),
);


$lAdmin->AddHeaders($arHeaders);
$arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();

$count = count($arResult['REPORT']);
$dbResult = new CDBResult;
$dbResult->InitFromArray($arResult['REPORT']);

$dbResult = new CAdminResult($dbResult, $sTableID);
$dbResult->NavStart();
$lAdmin->NavText($dbResult->GetNavPrint(""));

$priceOnPage = 0.0;
$key = 0;
while ($res = $dbResult->GetNext()) {
    $row =& $lAdmin->AddRow($res["NAME"], $res);
    if($res["NAME"]){
        $row->AddViewField("NAME", "<b>".$res["NAME"]."</b>");
    }

    if($res['DIFF']){
        $row->AddViewField("DIFF", ($res['DIFF'] > 0 ? '+' : '') . CCurrencyLang::CurrencyFormat($res['DIFF'], 'RUB')." ({$res['DIFF_PERCENT']}%)");
    }
}

$lAdmin->CheckListMode();


/****************************************************************************/
/***********  MAIN PAGE  ****************************************************/
/****************************************************************************/
$APPLICATION->SetTitle("История цен");

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");
?>

<form name="find_form" method="GET" action="<? echo $APPLICATION->GetCurPage() ?>?">
    <?
    $oFilter = new CAdminFilter(
        $sTableID . "_filter",
        array(
            "Наименование товара",
            "ID товара",
        )
    );

    $oFilter->Begin();
    ?>
    <tr>
        <td>Наименование товара:</td>
        <td>
            <input type="text" name="filter_product_name" value="<?= $filter_product_name ?>" size="40" class="adm-input">
        </td>
    </tr>
    <tr>
        <td>ID товара:</td>
        <td>
            <input type="text" name="filter_product_id" value="<?= $filter_product_id ?>" size="5" class="adm-input">
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
