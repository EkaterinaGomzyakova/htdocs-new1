<?php
AddEventHandler("main", "OnAdminListDisplay", "MyOnAdminListDisplay");

function MyOnAdminListDisplay(&$oAdminList)
{
    global $USER;
    global $APPLICATION;
    CModule::IncludeModule("wl.snailshop");

    if (WL\SnailShop::userIsStaff()) {

        $arActions = [];
        if (strpos($oAdminList->table_id, "tbl_iblock_list") === 0) {
            $arActions = $oAdminList->arActions;
            $arActions['printSticker'] = [
                "action" => "generateStickerUrl('tbl_iblock_list')",
            ];
            $arActions['printStickerWithPrice'] = [
                "action" => "generateStickerWithPriceUrl('tbl_iblock_list')",
            ];
        }

        if(strpos($oAdminList->table_id, "tbl_iblock_element") === 0) {
            $arActions = $oAdminList->arActions;
            $arActions['printSticker'] = [
                "action" => "generateStickerUrl('tbl_iblock_element')",
            ];
            $arActions['printStickerWithPrice'] = [
                "action" => "generateStickerWithPriceUrl('tbl_iblock_element')",
            ];
        }

        if(!empty($arActions)) {
            $arActions['printSticker']["name"] = "[ClanBeauty] Печать этикеток";
            $arActions['printSticker']["value"] = "printSticker";

            $arActions['printStickerWithPrice']["name"] = "[ClanBeauty] Печать этикеток с ценой";
            $arActions['printStickerWithPrice']["value"] = "printStickerWithPrice";

            $oAdminList->AddGroupActionTable($arActions);
        }


        if(strpos($APPLICATION->GetCurPage(), "cat_store_document_edit.php") > 0) {
            $link = DeleteParam(array("mode"));
            $link = $APPLICATION->GetCurPage()."?mode=excel".($link <> ""? "&".$link:"");
            $aAdditionalMenu = array(
                "TEXT"=>"Excel",
                "TITLE"=>GetMessage("admin_lib_excel"),
                //"LINK"=>htmlspecialcharsbx($link),
                "ONCLICK"=>"location.href='".htmlspecialcharsbx($link)."'",
                "GLOBAL_ICON"=>"adm-menu-excel",
            );
            new CAdminContextMenuList([], $aAdditionalMenu);

        }
    }
}
