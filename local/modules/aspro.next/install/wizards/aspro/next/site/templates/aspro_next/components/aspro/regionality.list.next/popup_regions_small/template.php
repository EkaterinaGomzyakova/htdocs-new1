<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */

$this->setFrameMode(true);

use \Bitrix\Main\Localization\Loc;

if($arResult['CURRENT_REGION'])
{?>
	<?if(!$arResult['POPUP']):?>
		<div class="region_wrapper">
			<div class="city_title"><?=Loc::getMessage('CITY_TITLE');?></div>
			<div class="js_city_chooser colored" data-event="jqm" data-name="city_chooser_small" data-param-url="<?=urlencode($APPLICATION->GetCurUri());?>" data-param-form_id="city_chooser">
				<span><?=$arResult['CURRENT_REGION']['NAME'];?></span><span class="arrow"><i></i></span>
			</div>
			<?if((isset($_SESSION['GEOIP']) && !isset($_SESSION['GEOIP']['message'])) && !(isset($_COOKIE['current_region']) && $_COOKIE['current_region'])):?>
				<div class="confirm_region">
					<?
					$href = 'data-href="'.$arResult['REGIONS'][$arResult['CURRENT_REGION']['ID']]['URL'].'"';
					if($arTheme['USE_REGIONALITY']['DEPENDENT_PARAMS']['REGIONALITY_TYPE']['VALUE'] == 'SUBDOMAIN' && ($arResult['HOST'].$_SERVER['HTTP_HOST'].$arResult['URI'] == $arResult['REGIONS'][$arResult['CURRENT_REGION']['ID']]['URL']))
					$href = '';?>
					<div class="title"><?=Loc::getMessage('CITY_TITLE');?> <?=$arResult['CURRENT_REGION']['NAME'];?> ?</div>
					<div class="buttons">
						<span class="btn btn-default aprove" data-id="<?=$arResult['CURRENT_REGION']['ID'];?>" <?=$href;?>><?=Loc::getMessage('CITY_YES');?></span>
						<span class="btn btn-default white js_city_change"><?=Loc::getMessage('CITY_CHANGE');?></span>
					</div>
				</div>
			<?endif;?>
		</div>
	<?else:?>
		<div class="popup_regions">			
			<div class="items only_city">
				<?if($arResult['REGIONS']):?>
					<div class="block cities">
						<div class="items_block">
							<?foreach($arResult['REGIONS'] as $key => $arItem):?>
								<?$bCurrent = ($arResult['CURRENT_REGION']['ID'] == $arItem['ID']);?>
								<div class="item <?=($bCurrent ? 'current' : '');?>" data-id="<?=((isset($arItem['IBLOCK_SECTION_ID']) && $arItem['IBLOCK_SECTION_ID']) ? $arItem['IBLOCK_SECTION_ID'] : 0);?>">
									<?if($bCurrent):?>
										<span class="name"><?=$arItem['NAME'];?></span>
									<?else:?>
										<a href="<?=$arItem['URL'];?>" data-id="<?=$arItem['ID'];?>" class="name"><?=$arItem['NAME'];?></a>
									<?endif;?>								
								</div>
							<?endforeach;?>
						</div>
					</div>
				<?endif;?>
			</div>
			<div class="h-search autocomplete-block" id="title-search-city">
				<div class="wrapper">
					<input id="search" class="autocomplete text" type="text" placeholder="<?=Loc::getMessage('CITY_PLACEHOLDER');?>">
					<div class="search_btn"></div>
				</div>
			</div>
			<script>
				var arRegions = <?=CUtil::PhpToJsObject($arResult['JS_REGIONS']);?>
			</script>
		</div>
	<?endif;?>
<?}?>
