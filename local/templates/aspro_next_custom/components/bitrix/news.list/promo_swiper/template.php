<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die();

$this->setFrameMode(true);

/** @var array $arParams */
/** @var array $arResult */

if ($arResult['ITEMS']) { ?>
<div class="promo-section">
    <div class="maxwidth-theme">
        <div class="top_block">
            <h3 class="title_block">Акции и промокоды</h3>
            <a href="<?= $arParams['CATALOG'] ?>">Все</a>
        </div>
    </div>
    <div class="maxwidth-theme promo-main-swiper-container">
        <div class="swiper promo-main-swiper">
            <div class="promo-main-swiper-wrapper swiper-wrapper">
                <?php
                foreach ($arResult['ITEMS'] as $arItem) {
                    ?>
                    <div class="swiper-slide promo-main-slide" id="<?= $this->GetEditAreaId($arItem['ID']) ?>">
                        <?php
                        $this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_EDIT'));
                        $this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem['IBLOCK_ID'], 'ELEMENT_DELETE'), ['CONFIRM' => GetMessage(Loc::getMessage('PROMO_SWIPER_DELETE_ENTRY_CONFIRM'))]);
                        if ($arItem['PICTURE']) {
                            if ($arItem['URL']) { ?>
                                <a href="<?= $arItem['URL'] ?>" target="<?= $arItem['TARGET'] ?>">
                                    <?php
                            }
                            ?>
                                <img class="promo-main-slide-image promo-main-slide-image" loading="lazy"
                                    src="<?= $arItem['PICTURE'] ?>" alt="<?= $arItem['NAME'] ?>">
                                <?php
                                if ($arItem['URL']) { ?>
                                </a>
                                <?php
                                }
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="promo-main-swiper__pagination swiper-pagination"></div>
            <div class="promo-main-swiper__button-prev swiper-button-prev"></div>
            <div class="promo-main-swiper__button-next swiper-button-next"></div>
        </div>
    </div>
</div>
    <?php
}
