<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arParams */
/** @var array $arResult */
/** @global \CMain $APPLICATION */
/** @global \CUser $USER */
/** @global \CDatabase $DB */
/** @var \CBitrixComponentTemplate $this */
/** @var string $templateName */
/** @var string $templateFile */
/** @var string $templateFolder */
/** @var string $componentPath */
/** @var array $templateData */
/** @var \CBitrixComponent $component */
$this->setFrameMode(true);?>
<? if ($arResult['ITEMS']) { ?>
    <style>
        <? foreach($arResult['ITEMS'] as $code => $arItem) { ?>
            .stickers .sticker_<?= strtolower($code); ?> {
                background-color: <?= $arItem['PROPERTY_BACKGROUND_COLOR_VALUE']?>;
                color: <?= $arItem['PROPERTY_TEXT_COLOR_VALUE']?>;
            }
        <? } ?>
    </style>
<? } ?>