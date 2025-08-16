<?
foreach($arResult['ITEMS'] as $key => $arItem) {
    $arResult['ITEMS'][$key]['full-text'] = htmlentities($arItem['ELEMENT']['DETAIL_TEXT']);;
}