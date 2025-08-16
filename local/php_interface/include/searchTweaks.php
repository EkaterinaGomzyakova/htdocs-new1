<?php
AddEventHandler("search", "BeforeIndex", ["SearchTweaks", "BeforeIndexHandler"]);
class SearchTweaks
{
    public static function BeforeIndexHandler($arFields)
    {
        if ($arFields["MODULE_ID"] == "iblock" && $arFields["PARAM2"] == GOODS_IBLOCK_ID) {
            $dbSections = CIBlockElement::GetElementGroups($arFields['ITEM_ID']);
            while ($arSection = $dbSections->Fetch()) {
                if ($arSection['CODE'] == 'probniki') {
                    unset($arFields["BODY"]);
                    unset($arFields["TITLE"]);
                    return $arFields;
                }
            }

            $arItem = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ID' => $arFields['ITEM_ID']], false, false, ['ID', 'IBLOCK_ID', 'PROPERTY_VOLUME', 'PROPERTY_BRAND', 'PROPERTY_ALT_NAME'])->Fetch();
            if (!empty($arItem['PROPERTY_VOLUME_VALUE'])) {
                $arFields['TITLE'] .= ' ' . $arItem['PROPERTY_VOLUME_VALUE'];
            }
            
            if (!empty($arItem['PROPERTY_ALT_NAME_VALUE'])) {
                $arFields['TITLE'] .= ' ' . $arItem['PROPERTY_ALT_NAME_VALUE'];
                $arFields['BODY'] .= "\n " . $arItem['PROPERTY_ALT_NAME_VALUE'];
            }

            if(!empty($arItem['PROPERTY_BRAND_VALUE'])) {
                $arBrand = CIBlockElement::GetList([], ['ID' => $arItem['PROPERTY_BRAND_VALUE'], 'IBLOCK_ID' => BRANDS_IBLOCK_ID], false, false, ['ID', 'IBLOCK_ID', 'PREVIEW_TEXT'])->Fetch();
                $arFields['BODY'] .= "\n " . $arBrand['PREVIEW_TEXT'];
            }
            return $arFields;
        }
    }
}
