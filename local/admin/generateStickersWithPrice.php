<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
CModule::IncludeModule("wl.snailshop");
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");
global $USER;

if (WL\SnailShop::userIsStaff()) {
    $arRawIds = $_REQUEST['ID'];

    if(!empty($_REQUEST['IDS'])) {
        $arRawIds = explode(",", $_REQUEST['IDS']);
        foreach($arRawIds as $key => $id) {
            $arRawIds[$key] = trim($id);
        }
    }

    if (!empty($arRawIds)) {
        $arIds = [];
        $arIds = str_replace('E', '', $arRawIds);
        $rsElement = CIBlockElement::GetList([], ["=ID" =>$arIds], false, false, ["ID", "IBLOCK_ID", "NAME"]);
        while($obElement = $rsElement->GetNextElement()) {
            $arElement = $obElement->GetFields();
            $arElement['PROPERTIES'] = $obElement->GetProperties();
            $arElement['PROPERTIES']['STICKER_BRAND'] = end(CIBlockFormatProperties::GetDisplayValue($arElement, $arElement['PROPERTIES']['BRAND'], false)['LINK_ELEMENT_VALUE'])['NAME'];
            $arElement['PRICES'] = CCatalogProduct::GetOptimalPrice($arElement['ID'], 1, $USER->GetUserGroupArray())['RESULT_PRICE'];


            ?>
            <div class="stickerTable">
                <div class="row name">
                    <?= $arElement['PROPERTIES']['STICKER_BRAND'] ?><br>
                    <?= $arElement['PROPERTIES']['ALT_NAME']['VALUE'] ?>
                </div>
                <div class="row description">
                    <? if($arElement['PRICES']['DISCOUNT'] > 0) {?>
                        <s><?= CurrencyFormat($arElement['PRICES']['BASE_PRICE'], "RUB");?></s> <strong><?= CurrencyFormat($arElement['PRICES']['DISCOUNT_PRICE'], "RUB");?></strong>
                        <br>
                        Скидка: -<?= $arElement['PRICES']['PERCENT']?>% (<?= CurrencyFormat($arElement['PRICES']['DISCOUNT'], 'RUB');?>)
                    <? } else {?>
                        <strong><?= CurrencyFormat($arElement['PRICES']['BASE_PRICE'], "RUB");?></strong>
                    <? }?>
                </div>
            </div>
            <?
        }
    }
}
?>
<style>
    body {
        padding: 0px;
        margin: 0px;
        display: inline-block;
        width: 100%;
        display: flex;
        flex-wrap: wrap;
    }

    .stickerTable {
        border-collapse: collapse;
        font-size: 13px;
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        width: 33%;
        height: 170px;
        page-break-inside: avoid;
        border: 1px solid black;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .stickerTable .description {
        font-size: 0.9em;
        overflow-wrap: anywhere;
        word-break: break-all;
    }

    .stickerTable .row {
        border-collapse: collapse;
        padding: 1.5px 4px;
    }

    .stickerTable .name {
        font-weight: 600;
        font-size: 1.1em;
        padding-top: 4px;
        text-align: center;
    }

    .stickerTable .description strong {
        font-size: 18px;
    }
</style>

<script>
    //window.print();
</script>