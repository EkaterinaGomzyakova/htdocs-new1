<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


$arComponentParameters = array(
    "GROUPS" => array(
    ),

    "PARAMETERS" => array(
        "AJAX_PREFIX" => array(
            "PARENT" => "BASE",
            "NAME" => "Идентификатор AJAX запроса",
            "TYPE" => "STRING",
            "MULTIPLE" => "N",
            "ADDITIONAL_VALUES" => "N",
            "DEFAULT" => uniqid(),
        ),
    ),
);