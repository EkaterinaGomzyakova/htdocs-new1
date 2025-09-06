<?php

namespace Clanbeauty;

use CIBlockElement;
use CIBlockSection;
use Bitrix\Main\LoaderException;

class IblockHandlers
{
    /**
     * @param $arFields
     *
     * @return void
     * @throws LoaderException
     */
    public static function OnAfterIBlockElementUpdate($arFields): void
    {
        switch ($arFields['IBLOCK_ID']) {
            case GOODS_IBLOCK_ID:
                CatalogHelpers::onSave($arFields['ID']);
                break;
            case SKU_IBLOCK_ID:
                CatalogHelpers::onSaveSku($arFields['ID']);
                break;
        }
    }

    /**
     * @param $arFields
     *
     * @return void
     * @throws LoaderException
     */
    public static function OnAfterIBlockElementAdd($arFields): void
    {
        switch ($arFields['IBLOCK_ID']) {
            case GOODS_IBLOCK_ID:
                CatalogHelpers::onSave($arFields['ID']);
                break;
            case SKU_IBLOCK_ID:
                CatalogHelpers::onSaveSku($arFields['ID']);
                break;
        }
    }

    /**
     * @param array $arFields
     *
     * @return void
     */
    public static function OnBeforeIBlockElementUpdate(array &$arFields): void
    {
        switch ($arFields['IBLOCK_ID']) {
            case GOODS_IBLOCK_ID:
                $originElement = CIBlockElement::GetList(
                    [],
                    ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ID' => $arFields['ID']],
                    false,
                    false,
                    ['ID', 'CODE']
                )->Fetch();
                if ($originElement['CODE'] === GIFT_CODE) {
                    self::preventDeactivation($arFields);
                }
                break;
            case SKU_IBLOCK_ID:
                $originElement = CIBlockElement::GetList(
                    [],
                    ['IBLOCK_ID' => SKU_IBLOCK_ID, 'ID' => $arFields['ID']],
                    false,
                    false,
                    ['ID', 'PROPERTY_GIFT_CERTIFICATE']
                )->Fetch();
                if (!empty($originElement['PROPERTY_GIFT_CERTIFICATE_VALUE'])) {
                    self::preventDeactivation($arFields);
                }
                break;
        }
    }

    /**
     * @param array $arParams
     *
     * @return void
     */
    public static function OnBeforeIBlockSectionUpdate(array &$arParams): void
    {
        switch ($arParams['IBLOCK_ID']) {
            case GOODS_IBLOCK_ID:
                $originElement = CIBlockSection::GetList(
                    [],
                    ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ID' => $arParams['ID']],
                    false,
                    ['ID', 'CODE']
                )->Fetch();
                if ($originElement['CODE'] === GIFT_SECTION_CODE) {
                    self::preventDeactivation($arParams);
                }
                break;
        }
    }

    public static function PreventImageDeletion(&$arFields)
    {
        if (empty($arFields["ID"])) {
            return true;
        }

        // Получаем текущий элемент
        $res = CIBlockElement::GetByID($arFields["ID"]);
        if ($currentElement = $res->GetNext()) {
            // Проверяем поле PREVIEW_PICTURE
            if (isset($arFields["PREVIEW_PICTURE"])) {
                $currentPreview = $currentElement["PREVIEW_PICTURE"];
                $newPreview = $arFields["PREVIEW_PICTURE"];

                // Проверка на удаление или пустое значение
                if (
                    (is_array($newPreview) && isset($newPreview["del"]) && $newPreview["del"] == "Y")
                    || (empty($newPreview) && !is_array($newPreview))
                ) {
                    // Восстанавливаем исходное значение
                    $arFields["PREVIEW_PICTURE"] = $currentPreview;
                }
            }

            // Проверяем поле DETAIL_PICTURE
            if (isset($arFields["DETAIL_PICTURE"])) {
                $currentDetail = $currentElement["DETAIL_PICTURE"];
                $newDetail = $arFields["DETAIL_PICTURE"];

                // Проверка на удаление или пустое значение
                if (
                    (is_array($newDetail) && isset($newDetail["del"]) && $newDetail["del"] == "Y")
                    || (empty($newDetail) && !is_array($newDetail))
                ) {
                    // Восстанавливаем исходное значение
                    $arFields["DETAIL_PICTURE"] = $currentDetail;
                }
            }
        }

        return true;
    }


    /**
     * Запрет деактивации
     *
     * @param array $arFields
     *
     * @return void
     */
    private static function preventDeactivation(array &$arFields): void
    {
        $arFields['ACTIVE'] = 'Y';
    }
}