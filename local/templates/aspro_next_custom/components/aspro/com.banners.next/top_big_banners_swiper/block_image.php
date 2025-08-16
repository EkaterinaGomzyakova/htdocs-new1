<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arItem */
if ($arItem['PICTURES']['DESKTOP'] || $arItem['PICTURES']['MOBILE']) {
    $imgDesktop = $arItem['PICTURES']['DESKTOP'] ?: $arItem['PICTURES']['MOBILE'];
    $imgMobile = $arItem['PICTURES']['MOBILE'] ?: $arItem['PICTURES']['DESKTOP'];
    if ($arItem['URL']) { ?>
        <a href="<?= $arItem['URL'] ?>" <?= $arItem['TARGET'] ? "target=\"{$arItem['TARGET']}\"" : '' ?>>
        <?php
    }
    ?>
    <div class="banner-top-slide-image banner-top-slide-image--desktop <?= $arItem['BUTTONS'] ? 'banner-top-slide-image--has-buttons' : '' ?>"
        style="background-image:url(<?= $imgDesktop ?>)"
    ></div>
    <div class="banner-top-slide-image banner-top-slide-image--mobile <?= $arItem['BUTTONS'] ? 'banner-top-slide-image--has-buttons' : '' ?>"
        style="background-image:url(<?= $imgMobile ?>)"
    ></div>
    <?php
    if ($arItem['URL']) { ?>
        </a>
        <?php
    }
}
