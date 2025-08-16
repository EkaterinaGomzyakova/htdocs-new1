<?
AddEventHandler("iblock", "OnAfterIBlockElementAdd", "setNoveltyFlag");

function setNoveltyFlag($arFields) {
    if($arFields['ID'] > 0 && $arFields['IBLOCK_ID'] == GOODS_IBLOCK_ID) {
        $arProperty['HIT'][] = CATALOG_NOVELTY_ACTION_VALUE_ID;
        CIBlockElement::SetPropertyValuesEx($arFields['ID'], GOODS_IBLOCK_ID, $arProperty);
        \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(GOODS_IBLOCK_ID, $arFields['ID']);
    }
}

AddEventHandler("iblock", "OnBeforeIBlockElementAdd", "trimNameAndCode");
AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", "trimNameAndCode");

function trimNameAndCode(&$arFields) {
    if(isset($arFields['NAME']) && strlen(trim($arFields['NAME'])) > 0 ) {
        $arFields['NAME'] = trim($arFields['NAME']);
    }

    if(isset($arFields['CODE']) && strlen(trim($arFields['CODE'], '_')) > 0 ) {
        $arFields['CODE'] = trim($arFields['CODE'], '_');
    }
}