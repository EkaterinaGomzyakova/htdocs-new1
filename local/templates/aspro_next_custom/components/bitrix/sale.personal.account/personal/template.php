<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<? if (!empty($arResult['ACCOUNT_LIST'])): ?>
<div class="visible-xs">
    <div class="personal-account personal-page"><div class="ico"></div><div>Баланс бьюти-баллов: <strong><?= $arResult['ACCOUNT_LIST'][0]['SUM'] ?></strong></div></div>
</div>
<?endif ?>