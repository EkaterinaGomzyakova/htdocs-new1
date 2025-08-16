<?
$arResult['ROULETE_PRIZES'] = [];
$roulete_iblock_id = WL\Iblock::getIblockIDByCode("roulete");
$dbWinnerProducts = CIBlockElement::GetList([], ['IBLOCK_ID' => $roulete_iblock_id], false, false, ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_PICTURE', 'PROPERTY_WINNERS']);
while ($arProduct = $dbWinnerProducts->Fetch()) {
    if ($arProduct['PROPERTY_WINNERS_VALUE'] == $arResult['ID']) {
        $arResult['ROULETE_PRIZES'][] = $arProduct;
    }
}
