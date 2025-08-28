<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

// Проверяем, есть ли вообще элементы
if (!$arResult["ITEMS"]) {
	return;
}

// --- ГЛАВНАЯ ЛОГИКА ---
// "Вынимаем" самый первый элемент из массива. Он и будет нашим главным.
// Массив $arResult["ITEMS"] при этом уменьшится, и в нем останутся только элементы для нижней сетки.
$arFirstItem = array_shift($arResult["ITEMS"]);
?>

<div class="journal-block">
	<!-- Блок с заголовком -->
	<div class="top_block">
		<h3 class="title_block"><?= $arParams["TITLE_BLOCK"] ?: "Журнал Clanbeauty" ?></h3>
		<a href="<?= SITE_DIR . ($arParams["ALL_URL"] ?: "articles/") ?>">Все</a>
	</div>

	<!-- БОЛЬШОЙ ЭЛЕМЕНТ (первый по сортировке) -->
	<? if ($arFirstItem) : ?>
		<?
		$this->AddEditAction($arFirstItem['ID'], $arFirstItem['EDIT_LINK'], CIBlock::GetArrayByID($arFirstItem["IBLOCK_ID"], "ELEMENT_EDIT"));
		$this->AddDeleteAction($arFirstItem['ID'], $arFirstItem['DELETE_LINK'], CIBlock::GetArrayByID($arFirstItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

		$pic = $arFirstItem["PREVIEW_PICTURE"] ?: $arFirstItem["DETAIL_PICTURE"];
		$img = CFile::ResizeImageGet($pic, array("width" => 1200, "height" => 600), BX_RESIZE_IMAGE_EXACT, true);
		?>
		<div class="journal-featured-item" id="<?= $this->GetEditAreaId($arFirstItem['ID']); ?>">
			<a href="<?= $arFirstItem["DETAIL_PAGE_URL"] ?>" class="journal-featured-item__link">
				<? if ($img["src"]) : ?>
					<img src="<?= $img["src"] ?>" class="journal-featured-item__image" alt="<?= $arFirstItem["NAME"] ?>" loading="lazy" />
				<? endif; ?>
				<div class="journal-featured-item__overlay">
					<div class="journal-featured-item__title"><?= $arFirstItem["NAME"] ?></div>
					<div class="journal-featured-item__button-wrapper">
						<span class="btn btn-default">Читать ~5 минут</span>
					</div>
				</div>
			</a>
		</div>
	<? endif; ?>

	<!-- СЕТКА ИЗ ОСТАЛЬНЫХ ЭЛЕМЕНТОВ -->
	<? if ($arResult["ITEMS"]) : ?>
		<div class="journal-grid">
			<? foreach ($arResult["ITEMS"] as $arItem) : ?>
				<?
				$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
				$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')));

				$pic = $arItem["PREVIEW_PICTURE"] ?: $arItem["DETAIL_PICTURE"];
				$img = CFile::ResizeImageGet($pic, array("width" => 600, "height" => 400), BX_RESIZE_IMAGE_EXACT, true);
				?>
				<div class="journal-grid-item" id="<?= $this->GetEditAreaId($arItem['ID']); ?>">
					<a href="<?= $arItem["DETAIL_PAGE_URL"] ?>" class="journal-grid-item__link">
						<? if ($img["src"]) : ?>
							<div class="journal-grid-item__image-wrapper">
								<img src="<?= $img["src"] ?>" class="journal-grid-item__image" alt="<?= $arItem["NAME"] ?>" loading="lazy" />
							</div>
						<? endif; ?>
						<div class="journal-grid-item__title"><?= $arItem["NAME"] ?></div>
					</a>
				</div>
			<? endforeach; ?>
		</div>
	<? endif; ?>

</div>