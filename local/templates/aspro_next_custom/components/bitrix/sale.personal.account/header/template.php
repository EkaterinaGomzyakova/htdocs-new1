<?

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
} ?>
<? if (!empty($arResult['ACCOUNT_LIST'])): ?>
    <div class="personal-account" data-toggle="tooltip" data-placement="bottom"
         title="Получайте бьюти-баллы с каждой покупки. Используйте баллы при оформлении заказа.">
        <div class="ico"></div>
        <div class="value">
            <bonus-points
                    value="<?= $arResult['loyalty']['value'] ?>"
                    is-actual="<?= $arResult['loyalty']['value'] ?>"
            ></bonus-points>
        </div>
    </div>
<? endif ?>