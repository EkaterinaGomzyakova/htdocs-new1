<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arDescription = [
    "NAME" => Loc::getMessage('G_WL_GENERATE_CERTIFICATE_NAME'),
    "DESCRIPTION" => Loc::getMessage('G_WL_GENERATE_CERTIFICATE_DESCRIPTION'),
    "ICON" => "",
    "GROUP" => ["ID" => "other"],
    "AI_ONLY" => true
];

