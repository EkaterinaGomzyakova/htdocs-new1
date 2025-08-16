<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>

<? if (!empty($arResult['TARGETS'])) { ?>
    <div class="col-xs-12">
        <div class="alert alert-warning">
            <p><strong><?= GetMessage("ADD_TO_BASKET")?></strong></p>
            <ul>
            <? foreach ($arResult['TARGETS'] as $arTarget) { ?>
                <li><?= GetMessage($arTarget['CODE'], ['#PRICE#' => CurrencyFormat($arTarget['PRICE'], "RUB")]);?></li>
            <? } ?>
            </ul>
        </div>
    </div>
<? } ?>