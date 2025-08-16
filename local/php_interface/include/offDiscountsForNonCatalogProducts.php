<?

AddEventHandler('catalog', 'OnGetDiscountResult', ['DisableCumulativeDiscounts', 'OnGetDiscountResultHandler']);
AddEventHandler('catalog', 'OnGetDiscount', ['DisableCumulativeDiscounts', 'OnGetDiscountHandler']);

class DisableCumulativeDiscounts
{
    static private $iblockID = false;
    static private $productID = false;

    public static function OnGetDiscountResultHandler(&$arResult)
    {
        if (self::$iblockID == ADDITIONAL_CATALOG_IBLOCK_ID) {
            foreach ($arResult as $key => $arDiscount) {
                if ($arDiscount['XML_ID'] == "CUMULATIVE_DISCOUNT") {
                    unset($arResult[$key]);
                }
            }
        }

        if (self::$productID > 0) {
            $arProduct = CCatalogProduct::GetById(self::$productID);

            if ($arProduct['TYPE'] == \Bitrix\Catalog\ProductTable::TYPE_OFFER) {
                $skuProduct = \CIBlockElement::GetList(
                    [],
                    ['ID' => self::$productID, 'IBLOCK_ID' => self::$iblockID],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'PROPERTY_CML2_LINK', 'PROPERTY_GIFT_CERTIFICATE']
                )->fetch();

                $product = \CIBlockElement::GetList(
                    [],
                    ['ID' => $skuProduct['PROPERTY_CML2_LINK_VALUE'], GOODS_IBLOCK_ID],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'PROPERTY_GIFT_CERTIFICATE', 'IBLOCK_SECTION_ID', 'PROPERTY_DISABLE_CUMULATIVE_DISCOUNT', 'PROPERTY_BRAND']
                )->fetch();
            } else {
                $product = \CIBlockElement::GetList(
                    [],
                    ['ID' => self::$productID, 'IBLOCK_ID' => self::$iblockID],
                    false,
                    false,
                    ['ID', 'IBLOCK_ID', 'PROPERTY_GIFT_CERTIFICATE', 'IBLOCK_SECTION_ID', 'PROPERTY_DISABLE_CUMULATIVE_DISCOUNT', 'PROPERTY_BRAND']
                )->fetch();
            }

            if ($product['PROPERTY_GIFT_CERTIFICATE_VALUE'] > 0 || $product['PROPERTY_DISABLE_CUMULATIVE_DISCOUNT_VALUE'] == "Y" || $skuProduct['PROPERTY_GIFT_CERTIFICATE_VALUE'] > 0) {
                foreach ($arResult as $key => $arDiscount) {
                    unset($arResult[$key]);
                }
            }

            if (in_array($product['PROPERTY_BRAND_VALUE'], CUMULATIVE_DISCOUNT_EXCLUDE_BRANDS_ID_ARRAY)) {
                foreach ($arResult as $key => $arDiscount) {
                    if ($arDiscount['XML_ID'] == "CUMULATIVE_DISCOUNT") {
                        unset($arResult[$key]);
                    }
                }
            }

            // if($product['IBLOCK_SECTION_ID'] == NEW_YEAR_SECTION_ID) {
            //     foreach($arResult as $key => $arDiscount) {
            //         if($arDiscount['XML_ID'] == "CUMULATIVE_DISCOUNT") {
            //             unset($arResult[$key]);
            //         }
            //     }
            // }
        }
    }

    public static function OnGetDiscountHandler($intProductID, $intIBlockID, $arCatalogGroups, $arUserGroups, $strRenewal, $siteID, $arDiscountCoupons, $boolSKU, $boolGetIDS)
    {
        self::$iblockID = $intIBlockID;
        self::$productID = $intProductID;
        return true;
    }
}
