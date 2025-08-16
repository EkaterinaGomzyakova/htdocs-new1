<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/** @var array $arResult */

$daily = $arResult['DAILY'] == 'Y';
?>
<div class="maxwidth-theme flashsale_container flashsale_outer"
     onclick="window.location.href = '/offers/discount/'"
     data-flashsale-end="<?= $arResult['DT_END_STR'] ?>"
     data-flashsale-ticker="<?= $daily ? 'minutes' : 'seconds' ?>">
    <div class="flashsale_title">
        <h2>
            <?= $arResult['TITLE'] ?>
        </h2>
    </div>
    <div class="flashsale_timer_outer">
        <div class="flashsale_timer_container">
            <div class="flashsale_timer_digits flashsale_timer1_digits">
                <?= $arResult['VAL1'] ?>
            </div>
            <div class="flashsale_timer_description">
                <?= $daily ? Loc::getMessage('FLASHSALE_DAYS') : Loc::getMessage('FLASHSALE_HOURS') ?>
            </div>
        </div>
        <div class="flashsale_timer_container">
            <div class="flashsale_timer_digits flashsale_timer2_digits">
                <?= $arResult['VAL2'] ?>
            </div>
            <div class="flashsale_timer_description">
                <?= $daily ? Loc::getMessage('FLASHSALE_HOURS') : Loc::getMessage('FLASHSALE_MINUTES') ?>
            </div>
        </div>
        <div class="flashsale_timer_container">
            <div class="flashsale_timer_digits flashsale_timer3_digits">
                <?= $arResult['VAL3'] ?>
            </div>
            <div class="flashsale_timer_description">
                <?= $daily ? Loc::getMessage('FLASHSALE_MINUTES') : Loc::getMessage('FLASHSALE_SECONDS') ?>
            </div>
        </div>
    </div>
    <div class="flashsale_footer">
        <p>
            <?= $arResult['FOOTER'] ?>
        </p>
    </div>
</div>
