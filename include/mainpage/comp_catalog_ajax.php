<?$bAjaxMode = (isset($_POST["AJAX_POST"]) && $_POST["AJAX_POST"] == "Y");
if($bAjaxMode)
{
	require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
	global $APPLICATION;
}?>
<?if((isset($arParams["IBLOCK_ID"]) && $arParams["IBLOCK_ID"]) || $bAjaxMode):?>
	<?
	$arIncludeParams = ($bAjaxMode ? $_POST["AJAX_PARAMS"] : $arParamsTmp);
	$arGlobalFilter = ($bAjaxMode ? unserialize(urldecode($_POST["GLOBAL_FILTER"]), ['allowed_classes' => false]) : array());

	if($bAjaxMode) {
		$arComponentParams = $arIncludeParams;
	} else {
		$arComponentParams = \Bitrix\Main\Web\Json::decode($arParamsTmp);
	}

	?>
	
	<?
	if($bAjaxMode && (is_array($arGlobalFilter) && $arGlobalFilter))
		$GLOBALS[$arComponentParams["FILTER_NAME"]] = $arGlobalFilter;
		$GLOBALS[$arComponentParams["FILTER_NAME"]]['!SECTION_ID'] = 167;
	?>

	<?$APPLICATION->IncludeComponent(
		"bitrix:catalog.section",
		"catalog_block_front",
		$arComponentParams,
		false, array("HIDE_ICONS"=>"Y")
	);?>
	
<?endif;?>