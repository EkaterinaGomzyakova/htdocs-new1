<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class StickerColorsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        CModule::IncludeModule("iblock");

        if(!$this->arParams['IBLOCK_ID']) {
            return 'Iblock not found';
        }

        $dbItems = CIBlockElement::GetList([], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID']], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_BACKGROUND_COLOR', 'PROPERTY_TEXT_COLOR', 'PROPERTY_CODE', 'PROPERTY_LINK']);
        while($arItem = $dbItems->Fetch()) {
            $this->arResult['ITEMS'][$arItem['PROPERTY_CODE_VALUE']] = $arItem;
        }

        $this->includeComponentTemplate();

        return $this->arResult;
    }
}