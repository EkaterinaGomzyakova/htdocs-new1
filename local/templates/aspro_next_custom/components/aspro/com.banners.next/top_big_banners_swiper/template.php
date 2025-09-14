<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */

if ($arResult['ITEMS']) { ?>
    <div class="swiper banner-top-swiper">
        <div class="banner-top-swiper-wrapper swiper-wrapper">
            <?php
            foreach ($arResult["ITEMS"][$arParams["BANNER_TYPE_THEME"]] as $arItem) {
                $blockText = '';
                // Всегда подключаем текст, чтобы кнопка "За покупками" отображалась
                ob_start();
                include('block_text.php');
                $blockText = ob_get_clean();
                
                ob_start();
                include('block_image.php');
                $blockImage = ob_get_clean();
                $content = match ($arItem['TEXT_POSITION']) {
                    'left', 'right', '' => $blockImage . $blockText,
                    'image' => $blockImage . $blockText, // Добавляем текст даже для позиции 'image'
                    'center' => $blockText,
                    default => $blockText,
                };
                ?>
                <div class="swiper-slide banner-top-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                    <?php
                    $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                    $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage('CT_BNL_ELEMENT_DELETE_CONFIRM')]);
                    ?>
                    <div class="banner-top-slide__content banner-top-slide__content--<?= $arItem['TEXT_POSITION'] ?>">
                       <?= $content ?>
                    </div>
                </div>
                <?php
            }
            ?>
        </div>
        <div class="banner-top-swiper__pagination swiper-pagination"></div>
        <div class="banner-top-swiper__button-prev swiper-button-prev"></div>
        <div class="banner-top-swiper__button-next swiper-button-next"></div>
    </div>
    <?php
}
