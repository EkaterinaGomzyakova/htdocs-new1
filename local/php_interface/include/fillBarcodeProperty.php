<?
CModule::IncludeModule("catalog");

//регистрация событий: добавление, изменения, удаление штрихкода...
AddEventHandler("catalog", "OnCatalogStoreBarCodeAdd", array("BarcodeProcess", "updateBarcode"));
AddEventHandler("catalog", "OnCatalogStoreBarCodeUpdate", array("BarcodeProcess", "updateBarcode"));
AddEventHandler("catalog", "OnBeforeCatalogStoreBarCodeDelete", array("BarcodeProcess", "DeleteBarCode"));

class BarcodeProcess
{
    public static function updateBarcode($lastId, $arFields)
    {
        if (CModule::IncludeModule("iblock") && CModule::IncludeModule("catalog")) {
            CIBlockElement::SetPropertyValuesEx($arFields['PRODUCT_ID'], GOODS_IBLOCK_ID, array('BARCODE' => $arFields['BARCODE']));
        }
    }

    public static function DeleteBarCode($id)
    {
        $dbBarCode = CCatalogStoreBarCode::getList(array(), array("ID" => $id), false, false, ["ID"]);
        if ($arBarCode = $dbBarCode->GetNext()) {
            CIBlockElement::SetPropertyValuesEx($arBarCode['PRODUCT_ID'], GOODS_IBLOCK_ID, array('BARCODE' => ''));
        }
    }
}
