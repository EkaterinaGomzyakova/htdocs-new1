<?
$productId = intval($arResult['ITEMS'][0]['PROPERTIES']['OBJECT']['VALUE']);
if($productId > 0) {
    $arProduct = CIBlockElement::GetById($productId)->Fetch();
    $arResult['PRODUCT']['NAME'] = $arProduct['NAME'];
    $arResult['PRODUCT']['DESCRIPTION'] = $arProduct['DETAIL_TEXT'];
    $arImage = CFile::GetByID($arProduct['DETAIL_PICTURE'])->Fetch();
    $arResult['PRODUCT']['IMAGE_URL'] = "/upload/" . $arImage['SUBDIR'] . "/" . $arImage['FILE_NAME'];
    $arResult['PRODUCT']['PRICE'] = CCatalogProduct::GetOptimalPrice($productId)['RESULT_PRICE']['DISCOUNT_PRICE'];
    $arResult['PRODUCT']['IS_AVALIABLE'] = CCatalogProduct::GetById($productId)['AVAILABLE'];
}
foreach($arResult['ITEMS'] as $key => $arItem) {
    if(!empty($arItem['PROPERTIES']['ADDITIONAL']['VALUE'])) {
        $arAdditionals = unserialize($arItem['PROPERTIES']['ADDITIONAL']['~VALUE']);
        $arResult['ITEMS'][$key]['AGE'] = $arAdditionals['AGE'];
        $arResult['ITEMS'][$key]['SKIN_TYPE'] = $arAdditionals['SKIN_TYPE'];
    }
}