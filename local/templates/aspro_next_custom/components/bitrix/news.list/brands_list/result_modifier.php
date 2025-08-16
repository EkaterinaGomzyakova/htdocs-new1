<?php
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use WL\IblockUtils;

/** @var array $arResult */

$brandIblockId = IblockUtils::getIdByCode("aspro_next_brands");
$sort = ['SORT' => 'ASC', 'NAME' => 'ASC'];
$filter = [
    'IBLOCK_ID' => $brandIblockId,
    'CODE' => 'FILTER'
];
$rows = CIBlockPropertyEnum::GetList($sort, $filter);
$arResult['FILTER'] = [];
while ($enum = $rows->fetch()) {
    $strId = strtolower($enum['XML_ID']);
    $arResult['FILTER'][$enum['XML_ID']] = [
        'CAPTION' => $enum['VALUE'],
        'ICON' => $this->GetFolder() . "/image/icon-{$strId}.svg",
    ];
}

$numericSymbols = '0 - 9';
$arSymbolsEn = [$numericSymbols => 0];
foreach(range('A','Z') as $a) {
    $arSymbolsEn[$a] = 0;
}
$arSymbolsRu = [$numericSymbols => 0];
foreach(mb_str_split('АБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯ') as $a) {
    $arSymbolsRu[$a] = 0;
}

$arSectionsIDs = [];
$lastGroup = '';
foreach($arResult['ITEMS'] as &$arItem) {
    $arItem['FILTER'] = $arItem['PROPERTIES']['FILTER']['VALUE_XML_ID'] ?? [];
    if($arItem['IBLOCK_SECTION_ID'] && $arItem['PREVIEW_PICTURE']){
        $arSectionsIDs[] = $arItem['IBLOCK_SECTION_ID'];
    }
    $filter = mb_strtoupper(mb_substr($arItem['NAME'], 0, 1));
    if (is_numeric($filter)) {
        $filter = $numericSymbols;
    }
    if ($lastGroup !== $filter) {
        $arItem['START_GROUP'] = $lastGroup = $filter;
    }
    if(isset($arSymbolsEn[$filter])){
        $arSymbolsEn[$filter]++;
    }
    if(isset($arSymbolsRu[$filter])){
        $arSymbolsRu[$filter]++;
    }
    $arItem['FILTER'][] = $filter;
    $arItem['FILTER_DATA'] = '[' . implode(', ', array_map(fn($e) => "&#34;{$e}&#34;", $arItem['FILTER'])) . ']';
}
$arResult['NAV_START_EN'] = $arSymbolsEn;
$arResult['NAV_START_RU'] = $arSymbolsRu;
