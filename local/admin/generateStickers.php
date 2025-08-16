<?include($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<?
CModule::IncludeModule("wl.snailshop");
CModule::IncludeModule("iblock");

if (WL\SnailShop::userIsStaff()) {
    $arRawIds = $_REQUEST['ID'];

    if (!empty($arRawIds)) {
        $arIds = [];
        $arIds = str_replace('E', '', $arRawIds);
        $rsElement = CIBlockElement::GetList([], ["=ID" =>$arIds], false, false, ["ID", "IBLOCK_ID", "NAME"]);
        while($obElement = $rsElement->GetNextElement()) {
            $arElement = $obElement->GetFields();
            $arElement['PROPERTIES'] = $obElement->GetProperties();
            
            $arElement['PROPERTIES']['STICKER_DETAIL'] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement['PROPERTIES']['STICKER_DETAIL'], false);
            $arElement['PROPERTIES']['STICKER_MANUFACTURER'] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement['PROPERTIES']['STICKER_MANUFACTURER'], false);
            $arElement['PROPERTIES']['STICKER_IMPORTER'] = CIBlockFormatProperties::GetDisplayValue($arElement, $arElement['PROPERTIES']['STICKER_IMPORTER'], false);

			$maxLength = 700;
			if(mb_strlen($arElement['PROPERTIES']['STICKER_DETAIL']['DISPLAY_VALUE']) > $maxLength) {
				$arElement['PROPERTIES']['STICKER_DETAIL']['DISPLAY_VALUE'] = mb_substr($arElement['PROPERTIES']['STICKER_DETAIL']['DISPLAY_VALUE'], 0, $maxLength) . '..';
			}
            ?>
            <div class="stickerTable">
                <div class="row name">
                    <?= $arElement['NAME'] ?>
                </div>
                <div class="row description">
                    <?= $arElement['PROPERTIES']['STICKER_DETAIL']['DISPLAY_VALUE'] ?>
                </div>
                <div class="row manufacturer">
                    Производство: <?= $arElement['PROPERTIES']['STICKER_MANUFACTURER']['DISPLAY_VALUE'] ?>
                </div>
                <div class="row importer-row">
                    <div class="importer">Импортер: <?= $arElement['PROPERTIES']['STICKER_IMPORTER']['DISPLAY_VALUE'] ?></div>
                    <div class="eac"><img src="/local/templates/aspro_next_custom/images/eac-icon.svg" /></div>
                </div>
                <div class="row seller">
                    Продавец: <?=$_SERVER['HTTP_HOST']?>
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
    }

    .stickerTable {
        border-collapse: collapse;
        font-size: 7.5px;
        font-family: 'Helvetica Neue', 'Helvetica', Arial, sans-serif;
        width: 100%;
        height: 100%;
        page-break-after: always;
        page-break-inside: avoid;
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

    .stickerTable .manufacturer {
        font-size: 0.9em;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        display: -webkit-box;
    }

    .stickerTable .importer-row {
        display: flex;
    }

    .stickerTable .importer {
        flex-grow: 1;
    }

    .stickerTable .eac img {
        width: 15px;
    }
</style>
<script>
    window.print();
</script>