<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");?>
<div id="basket_preload">
<?include_once("action_basket.php");?>
<?$arParams = unserialize(urldecode($_REQUEST["PARAMS"]), ['allowed_classes' => false]);?>

<?$APPLICATION->IncludeComponent( "bitrix:sale.basket.basket.line", "normal", $arParams, false, array("HIDE_ICONS" =>"Y") );?>
</div>