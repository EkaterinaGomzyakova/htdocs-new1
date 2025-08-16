<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $arResult */
?>
<table>
    <tr>
        <td>Услуги</td>
        <?php foreach ($arResult['REPORT']['COL_CAPTIONS'] as $caption) { ?>
            <td><?= $caption ?></td>
        <?php } ?>
    </tr>
    <?php foreach ($arResult['REPORT']['ITEMS'] as $item) { ?>
        <tr>
            <td><?= $item['NAME'] ?></td>
            <?php foreach ($item['PRICE']['FORMAT'] as $price) { ?>
                <td><nobr><?= $price ?></nobr></td>
            <?php } ?>
        </tr>
    <?php } ?>
</table>
