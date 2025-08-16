<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arResult */
/** @var array $arParams */

if ($arResult['ITEMS']) {
    $arTmpItems = [];
    foreach ($arResult['ITEMS'] as $key => $arItem) {
        [$strText, $strButtons] = preg_split('~\s*<!--\s*BUTTONS\s*-->\s*~i', $arItem['PREVIEW_TEXT'] ?? '');

        $arDesktopPicture = CFile::ResizeImageGet($arItem['DETAIL_PICTURE']['ID'], ['width' => 1500, 'height' => 350], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        $arTmpItems[$arItem['TYPE_BANNER']][] = [
            'ID' => $arItem['ID'],
            'IBLOCK_ID' => $arItem['IBLOCK_ID'],
            'EDIT_LINK' => $arItem['EDIT_LINK'],
            'DELETE_LINK' => $arItem['DELETE_LINK'],
            'TITLE' => $arItem['NAME'],
            'TITLE_NOTAGS' => strip_tags($arItem['~NAME'], '<br><br/>'),
            'URL' => trim($arItem['PROPERTIES']['URL_STRING']['VALUE'] ?? ''),
            'TEXT' => $strText ?: null,
            'TEXT_POSITION' => $arItem['PROPERTIES']['TEXT_POSITION']['VALUE_XML_ID'] ?? '',
            'BUTTONS' => $strButtons,
            'PICTURES' => [
                'MOBILE' => $arItem['PREVIEW_PICTURE']['SRC'],
                'DESKTOP' => $arDesktopPicture['src'],
            ]
        ];
    }
    $arResult['ITEMS'] = $arTmpItems;
}
