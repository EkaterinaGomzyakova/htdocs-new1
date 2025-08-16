<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
if(!CModule::IncludeModule("iblock")) return;

if(!defined("WIZARD_SITE_ID")) return;
if(!defined("WIZARD_SITE_DIR")) return;
if(!defined("WIZARD_SITE_PATH")) return;
if(!defined("WIZARD_TEMPLATE_ID")) return;
if(!defined("WIZARD_TEMPLATE_ABSOLUTE_PATH")) return;
if(!defined("WIZARD_THEME_ID")) return;

$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"].BX_PERSONAL_ROOT."/templates/".WIZARD_TEMPLATE_ID."/";
//$bitrixTemplateDir = $_SERVER["DOCUMENT_ROOT"]."/local/templates/".WIZARD_TEMPLATE_ID."/";

if(isset($_SESSION["NEXT_CATALOG_ID"]) && $_SESSION["NEXT_CATALOG_ID"])
	\Bitrix\Main\Config\Option::set("aspro.next", "CATALOG_IBLOCK_ID", $_SESSION["NEXT_CATALOG_ID"], WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set("aspro.next", "MAX_DEPTH_MENU", 4, WIZARD_SITE_ID);
\Bitrix\Main\Config\Option::set("aspro.next", "REGIONALITY_FILTER_ITEM", "Y", WIZARD_SITE_ID);

unset($_SESSION['CATALOG_COMPARE_LIST']['NEXT_CATALOG_ID']);
unset($_SESSION['ASPRO_BASKET_COUNTERS']);
?>