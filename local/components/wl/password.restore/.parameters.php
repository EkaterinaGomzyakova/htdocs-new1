<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arGroups = array();
$rsGroups = CGroup::GetList($by="c_sort", $order="asc", Array("ACTIVE" => "Y"));
while ($arGroup = $rsGroups->Fetch())
{
    $arGroups[$arGroup["ID"]] = $arGroup["NAME"];
}

$arComponentParameters = array(
    "GROUPS" => array(
    ),

    "PARAMETERS" => array(
        "GROUP" => array(
            "PARENT" => "BASE",
            "NAME" => "Группа подтвержденных аккаунтов",
            "TYPE" => "LIST",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "VALUES" => $arGroups,
        ),
    ),
);