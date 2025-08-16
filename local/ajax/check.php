<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');
$APPLICATION->ShowAjaxHead();

if($_REQUEST["method"] != "ajax")
    die();

switch ($_REQUEST["action"]) {
    case "check":
        $APPLICATION->IncludeComponent(
            "clanbeauty:check",
            ".default",
            array(),
            false
        );
    break;
}
