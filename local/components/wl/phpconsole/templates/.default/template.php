<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\UI\Extension;

$APPLICATION->SetAdditionalCSS("/local/php_interface/include/admin_header/php_console.css");
$APPLICATION->SetAdditionalCSS("/bitrix/css/main/font-awesome.css");
Extension::load("ui.forms");

$MESS = Loc::LoadLanguageFile($_SERVER["DOCUMENT_ROOT"] . $this->GetFolder() . "/script.php");
Asset::getInstance()->AddString("<script>BX.message(" . CUtil::PhpToJSObject($MESS) . ")</script>");
