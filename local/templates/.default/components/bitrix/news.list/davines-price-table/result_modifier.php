<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;

function mySumFormat($fSum, $currency)
{
    return number_format ( $fSum, 0, '.', ' ' ).' <small>' . $currency . '</small>';
}

$res = [];
$res['COL_CAPTIONS'] = [];
foreach (current($arResult['ITEMS'])['PROPERTIES'] as $propId => $prop) {
    $res['COL_CAPTIONS'][$propId] = $prop['NAME'];
}

$res['ITEMS'] = [];
foreach ($arResult['ITEMS'] as $item) {
    $props = [];

    foreach ($item['PROPERTIES'] as $propId => $prop) {
        $props['ORIGINAL'][$propId] = (int)$prop['VALUE'];
        $props['FORMAT'][$propId] = mySumFormat((int)$prop['VALUE'], 'руб.');
    }

    $res['ITEMS'][] = [
        'NAME' => $item['NAME'],
        'PRICE' => $props,
    ];
}

$arResult['REPORT'] = $res;
