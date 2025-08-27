<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<? $this->setFrameMode( true ); ?>

<?if($arResult['SECTIONS']):?>
	<div class="sections_wrapper popular-categories-wrapper">

		<?if($arParams["TITLE_BLOCK"] || $arParams["TITLE_BLOCK_ALL"]):?>
			<div class="top_block">
				<h3 class="title_block"><?=$arParams["TITLE_BLOCK"];?></h3>
				<a href="<?=SITE_DIR.$arParams["ALL_URL"];?>"><?=$arParams["TITLE_BLOCK_ALL"] ;?></a>
			</div>
		<?endif;?>

		<div class="popular-categories">
			<?foreach($arResult["SECTIONS"] as $arSection):?>
				<?
				$sLink = $arSection['SECTION_PAGE_URL'];

				// Устанавливаем значения по умолчанию
				$sImageSrc = SITE_TEMPLATE_PATH.'/images/no_photo.png';
				$sImageAlt = $arSection['NAME'];
				$sImageTitle = $arSection['NAME'];

				// Проверяем, есть ли картинка и обрабатываем ее БЕЗОПАСНО
				if (!empty($arSection['PICTURE'])) {
					$picture = null;
					if (is_numeric($arSection['PICTURE'])) { // Если это ID
						$picture = CFile::GetFileArray($arSection['PICTURE']);
					} elseif (is_array($arSection['PICTURE'])) { // Если это уже массив
						$picture = $arSection['PICTURE'];
					}
					
					if ($picture && !empty($picture['SRC'])) {
						$sImageSrc = $picture['SRC'];
						$sImageAlt = $picture['ALT'] ?: $arSection['NAME'];
						$sImageTitle = $picture['TITLE'] ?: $arSection['NAME'];
					}
				}
				?>

				<a href="<?=$sLink?>" class="popular-categories__item">
					<span class="popular-categories__image-wrapper">
						<img src="<?=$sImageSrc?>" alt="<?=$sImageAlt?>" title="<?=$sImageTitle?>"/>
					</span>
					<span class="popular-categories__name"><?=$arSection['NAME']?></span>
				</a>
			<?endforeach;?>
		</div>
		
	</div>
<?endif;?>