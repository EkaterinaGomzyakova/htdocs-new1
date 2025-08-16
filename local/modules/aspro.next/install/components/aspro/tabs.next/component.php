<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();?>
<?\Bitrix\Main\Loader::includeModule('iblock');
$arTabs = $arShowProp = array();

$arResult["SHOW_SLIDER_PROP"] = false;
if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
{
	$arrFilter = array();
}
else
{
	$arrFilter = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arrFilter))
		$arrFilter = array();
}

$arFilter = array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"]);
if($arParams["SECTION_ID"])
	$arFilter[]=array("SECTION_ID" => $arParams["SECTION_ID"], "INCLUDE_SUBSECTIONS" => "Y");
elseif($arParams["SECTION_CODE"])
	$arFilter[]=array("SECTION_CODE" => $arParams["SECTION_CODE"], "INCLUDE_SUBSECTIONS" => "Y");

global $arTheme, $isShowCatalogElements;
$bCatalogIndex = $isShowCatalogElements;

if($bCatalogIndex)
{
	$rsProp = CIBlockPropertyEnum::GetList(Array("sort" => "asc", "id" => "desc"), Array("ACTIVE" => "Y", "IBLOCK_ID" => $arParams["IBLOCK_ID"], "CODE" => $arParams["TABS_CODE"]));
	while($arProp = $rsProp->Fetch())
	{
		$arShowProp[$arProp["EXTERNAL_ID"]] = $arProp["VALUE"];
	}

	if($arShowProp)
	{
		if($arParams['STORES'])
		{
			foreach($arParams['STORES'] as $key => $store)
			{
				if(!$store)
					unset($arParams['STORES'][$key]);
			}
		}
		global $arRegion;
		if($arRegion)
		{
			$arParams['USE_REGION'] = 'Y';
			if($arRegion['LIST_PRICES'])
			{
				if(reset($arRegion['LIST_PRICES']) != 'component')
					$arParams['PRICE_CODE'] = array_keys($arRegion['LIST_PRICES']);
			}
			if($arRegion['LIST_STORES'])
			{
				if(reset($arRegion['LIST_STORES']) != 'component')
					$arParams['STORES'] = $arRegion['LIST_STORES'];
			}
		}
		
		foreach($arShowProp as $key => $prop)
		{
			$arItems = array();
			$arFilterProp = array("PROPERTY_".$arParams["TABS_CODE"]."_VALUE" => array($prop));

			$arItems = CNextCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" => "N", "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array_merge($arFilter, $arrFilter, $arFilterProp), false, array("nTopCount" => 1), array("ID"));
			if($arItems)
			{
				$arTabs[$key] = array(
					"TITLE" => $prop,
					"FILTER" => array_merge($arFilterProp, $arFilter)
				);
				$arResult["SHOW_SLIDER_PROP"] = true;
			}
		}
	}
	else
	{
		return;
	}
	$arParams["PROP_CODE"] = $arParams["TABS_CODE"];
	$arResult["TABS"] = $arTabs;

	$this->IncludeComponentTemplate();
}
else
	return;?>