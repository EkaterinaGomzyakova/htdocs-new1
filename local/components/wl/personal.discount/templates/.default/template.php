<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
?>

<? if (!empty($arResult['RANGES'])) { ?>
    <div class="cumulative-discount">
        <h3>Скидка по накопительной программе</h3>
        <p>Накапливайте скидку за каждый выполненный заказ.</p>
        <table style="width: auto;" class="table table-striped">
            <? foreach ($arResult['RANGES'] as $arRange) { ?>
                <tr>
                    <td>От <strong><?= CurrencyFormat($arRange['RANGE_FROM'], "RUB") ?></strong> — скидка <strong><?= $arRange['VALUE'] ?>%</strong>.</td>
                </tr>
            <? } ?>
        </table>

        <p>Сумма выполненных заказов:
            <strong><?= CurrencyFormat($arResult['USER_DISCOUNT']['SUMM'], "RUB") ?></strong> <? if (!$arResult['userHasMaxDiscount']) { ?>До следующего уровня скидки нужно заказов на сумму
            <strong><?= CurrencyFormat($arResult['sumToNextLevel'], "RUB") ?></strong><? } ?>
        </p>
        <? if ($arResult['userHasMaxDiscount']) { ?>
            <p><strong>Поздравляем, у вас максимальная скидка: <strong class="discount-highlight"><?= $arResult['USER_DISCOUNT']['VALUE'] ?>%</strong></strong></p>
        <? } else { ?>
            <p>Ваша персональная скидка: <strong class="discount-highlight"><?= $arResult['USER_DISCOUNT']['VALUE'] ?>%</strong></p>
        <? } ?>
    </div>
<? } ?>