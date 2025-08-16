<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arItem */
?>
<div class="banner-top-slide-text banner-top-slide-text--<?= $arItem['TEXT_POSITION'] ?> <?= $arItem['BUTTONS'] ? 'banner-top-slide-text--has-buttons' : '' ?>">
    <?php
    if ($arItem['TITLE'] && !$arItem['HIDE_TITLE']) { ?>
        <div class="banner-top-slide-text__title">
            <span>
                <?php
                if ($arItem['URL']) { ?>
                    <a href="<?= $arItem['URL'] ?>" <?= $arItem['TARGET'] ? "target=\"{$arItem['TARGET']}\"" : '' ?>>
                <?php
                }
                echo $arItem['TITLE_NOTAGS'];
                if ($arItem['URL']) { ?>
                    </a>
                <?php
                }
                ?>
            </span>
        </div>
        <?php
    }
    if ($arItem['TEXT']) { ?>
        <div class="banner-top-slide-text__content"><?= $arItem['TEXT'] ?></div>
        <?php
    }
    if ($arItem['BUTTONS']) { ?>
        <div class="banner-top-slide-text__buttons"><?= $arItem['BUTTONS'] ?></div>
        <?php
    }
    ?>
</div>
