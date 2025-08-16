<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? $this->setFrameMode(true); ?>
<?
// get element
$arItemFilter = CNext::GetCurrentElementFilter($arResult["VARIABLES"], $arParams);
$arElement = CNextCache::CIblockElement_GetList(array("CACHE" => array("TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]), "MULTI" => "N")), $arItemFilter, false, false, array("ID", 'PREVIEW_TEXT', "IBLOCK_SECTION_ID", 'PREVIEW_PICTURE', 'DETAIL_PICTURE'));
?>
<? if (!$arElement && $arParams['SET_STATUS_404'] !== 'Y') { ?>
	<div class="alert alert-warning"><?= GetMessage("ELEMENT_NOTFOUND") ?></div>
<? } elseif (!$arElement && $arParams['SET_STATUS_404'] === 'Y') { ?>
	<? CNext::goto404Page(); ?>
<? } else { ?>
	<?
	$brandId = 0;
	$arBrand = CIBlockElement::GetList([], ['IBLOCK_ID' => BRANDS_IBLOCK_ID, 'CODE' => $arResult['VARIABLES']['ELEMENT_CODE']], false, false, ['ID'])->Fetch();
	?>
	<?php
	$APPLICATION->IncludeComponent('wl:section.list.filter.by.property', '', [
		'PROPERTY_CODE' => 'BRAND',
		'PROPERTY_VALUE' => $arBrand['ID'],
		'CATALOG_IBLOCK_ID' => GOODS_IBLOCK_ID,
		'CACHE_TIME' => $arParams['CACHE_TIME'],
		'FILTER_NAME' => 'arBrandFilter',
		'SORT' => ['SORT' => 'ASC'],
	], false);
	?>
	<? include_once(__DIR__ . "/sort.php"); ?>
	<? $sViewElementTemplate = ($arParams["ELEMENT_TYPE_VIEW"] == "FROM_MODULE" ? $arTheme["NEWS_PAGE_DETAIL"]["VALUE"] : $arParams["ELEMENT_TYPE_VIEW"]); ?>
	<? @include_once('page_blocks/' . $sViewElementTemplate . '.php'); ?>
	<?
	if (is_array($arElement["IBLOCK_SECTION_ID"]) && count($arElement["IBLOCK_SECTION_ID"]) > 1) {
		CNext::CheckAdditionalChainInMultiLevel($arResult, $arParams, $arElement);
	}
	?>
<? } ?>
<div style="clear:both"></div>
<hr class="bottoms" />
<div class="row max-width-100">
	<div class="col-md-6 share">
	</div>
	<div class="col-md-6">
		<a class="back-url url-block" href="<?= $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'] ?>"><i class="fa fa-angle-left"></i><span><?= GetMessage('BACK_LINK') ?></span></a>
	</div>
</div>