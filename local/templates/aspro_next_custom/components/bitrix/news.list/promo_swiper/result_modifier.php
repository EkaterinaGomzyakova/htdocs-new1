<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */
/** @var array $arParams */

if ($arResult['ITEMS']) {
    foreach ($arResult['ITEMS'] as &$arItem) {
        $arItem = [
            'ID' => $arItem['ID'],
            'IBLOCK_ID' => $arItem['IBLOCK_ID'],
            'EDIT_LINK' => $arItem['EDIT_LINK'],
            'DELETE_LINK' => $arItem['DELETE_LINK'],
            'TITLE' => $arItem['NAME'],
            'URL' => trim($arItem['PROPERTIES']['REDIRECT']['VALUE'] ?: $arItem['DETAIL_PAGE_URL'] ?: '#'),
            'TARGET' => $arItem['PROPERTIES']['REDIRECT']['VALUE'] ? '_blank' : '_self',
            'PICTURE' => $arItem['PREVIEW_PICTURE']['SRC'],
        ];
    }
    unset($arItem);
}
