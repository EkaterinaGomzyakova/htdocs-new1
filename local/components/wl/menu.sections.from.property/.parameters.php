<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arComponentParameters = array(
    "GROUPS" => array(),

    "PARAMETERS" => array(
        "IBLOCK_ID" => array(
            "PARENT" => "BASE",
            "NAME" => "ID инфоблока для выбора свойства",
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => '',
        ),
        "PROPERTY_CODE" => array(
            "PARENT" => "BASE",
            "NAME" => "Символьный код свойства",
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => '',
        ),
        "URL_TEMPLATE" => array(
            "PARENT" => "BASE",
            "NAME" => "Шаблон URL для построения ссылки",
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => '',
        ),
    ),
);
