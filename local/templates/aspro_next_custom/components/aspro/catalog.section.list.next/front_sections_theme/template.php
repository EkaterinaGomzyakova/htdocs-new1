<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>

<?if($arResult['SECTIONS']):?>
	<div class="sections_wrapper popular-categories-wrapper">

		<?/* --- БЛОК С ЗАГОЛОВКОМ И ССЫЛКОЙ "ВСЕ" --- */?>
		<?if($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]):?>
			<div class="top_block">
				<h3 class="title_block"><?=$arParams["TITLE_BLOCK"];?></h3><a href="<?=SITE_DIR.$arParams["ALL_URL"];?>"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
				
			</div>
		<?endif;?>

		<?/* --- ВАШ БЛОК С ВЫВОДОМ КАТЕГОРИЙ --- */?>
		<div class="popular-categories">
			<?foreach($arResult["SECTIONS"] as $arSection):?>
				<?
				$sLink = $arSection['SECTION_PAGE_URL'];
				$sImageSrc = $arSection['PICTURE']['SRC'] ?? SITE_TEMPLATE_PATH.'/images/no_photo.png';
				?>
				<a href="<?=$sLink?>" class="popular-categories__item">
					<span class="popular-categories__image-wrapper">
						<img src="<?=$sImageSrc?>" alt="<?=$arSection['PICTURE']['ALT']?>" title="<?=$arSection['PICTURE']['TITLE']?>"/>
					</span>
					<span class="popular-categories__name"><?=$arSection['NAME']?></span>
				</a>
			<?endforeach;?>
		</div>
		
	</div>
<?endif;?>