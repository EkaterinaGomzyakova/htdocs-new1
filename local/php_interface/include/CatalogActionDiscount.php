<?php
CModule::IncludeModule("sale");
CModule::IncludeModule("catalog");

AddEventHandler("catalog", "OnDiscountAdd", "CalculateDiscountPropertyWrapper");
AddEventHandler("catalog", "OnDiscountUpdate", "CalculateDiscountPropertyWrapper");
AddEventHandler("catalog", "OnDiscountDelete", "CalculateDiscountPropertyWrapper");

AddEventHandler("catalog", "OnCouponAdd", "CalculateDiscountPropertyWrapper");
AddEventHandler("catalog", "OnCouponDelete", "CalculateDiscountPropertyWrapper");
AddEventHandler("catalog", "OnCouponUpdate", "CalculateDiscountPropertyWrapper");


function CalculateDiscountPropertyWrapper($id, $arFields = [])
{
    CalculateDiscountProperty();
}

function CalculateDiscountProperty()
{
    $arCurrentCoupons = $_SESSION['CATALOG_USER_COUPONS'];
    unset($_SESSION['CATALOG_USER_COUPONS']);

    CModule::IncludeModule("iblock");
    $arFilter = [
        "IBLOCK_ID" => GOODS_IBLOCK_ID,
        "ACTIVE" => "Y",
    ];
    $dbElements = CIBlockElement::GetList([], $arFilter, false, false, ["ID"]);

    while ($arElement = $dbElements->Fetch()) {
        $dbProperties = CIBlockElement::GetProperty(GOODS_IBLOCK_ID, $arElement['ID'], [], ['CODE' => "HIT"]);
        $arProperties = [];
        while ($arProperty = $dbProperties->Fetch()) {
            if (!empty($arProperty['VALUE'])) {
                $arProperties['HIT'][] = $arProperty['VALUE'];
            }
        }

        $arPrice = CCatalogProduct::GetOptimalPrice($arElement['ID'], 1, [2], "N", [], "s1");

        $hasDiscount = false;
        if($arPrice['RESULT_PRICE']['DISCOUNT'] > 0) {
            $hasDiscount = true;
        }

        if ($hasDiscount) {
            if(!is_array($arProperties['HIT'])) {
                $arProperties['HIT'] = [];
            }
            
            if (!in_array(CATALOG_DISCOUNT_ACTION_VALUE_ID, $arProperties['HIT'])) {
                $arProperties['HIT'][] = CATALOG_DISCOUNT_ACTION_VALUE_ID;
                CIBlockElement::SetPropertyValuesEx($arElement['ID'], GOODS_IBLOCK_ID, $arProperties);
            }
        } else {
            if (!empty($arProperties['HIT']) && in_array(CATALOG_DISCOUNT_ACTION_VALUE_ID, $arProperties['HIT'])) {
                if (($key = array_search(CATALOG_DISCOUNT_ACTION_VALUE_ID, $arProperties['HIT'])) !== false) {
                    unset($arProperties['HIT'][$key]);
                    if (empty($arProperties['HIT'])) {
                        $arProperties['HIT'] = false;
                    }
                    CIBlockElement::SetPropertyValuesEx($arElement['ID'], GOODS_IBLOCK_ID, $arProperties);
                }
            }
        }
    }
    BXClearCache(true);
    Bitrix\Main\Data\Cache::clearCache(true);

    $_SESSION['CATALOG_USER_COUPONS'] = $arCurrentCoupons;
    return "CalculateDiscountProperty();";
}