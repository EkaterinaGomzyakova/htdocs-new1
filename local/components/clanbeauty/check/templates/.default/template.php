<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true)
    die(); ?>

<? if ($USER->getId() == 1) { ?>
    <p class="time"><small>Время исполнения: <?= round($arResult['TIME_ELAPSED'], 2) ?></small></p>
<? } ?>
<ul class="check-wrapper">
    <? foreach ($arResult['TEST'] as $arTest) { ?>
        <li>
            <?
            $checkClass = "";
            if (!empty($arTest["VALUE"]["isSuccess"])) {
                $checkClass = "check-success";
            } else {
                $checkClass = "check-error";
            }
            ?>
            <div class="<?= $checkClass ?>">
                <div class="check-heading">
                    <div>
                        <i class="icon"></i>
                        <span class="check-title"><?= $arTest['NAME'] ?></span>
                    </div>
                    <a href="#" data-method="<?= $arTest['VALUE']['methodName'] ?>">Повторить</a>
                </div>
                <? if ($arTest["VALUE"]["items"]) { ?>
                    <div class="check-errors">
                        <? $i = 0; ?>
                        <? foreach ($arTest['VALUE']["items"] as $error) { ?>
                            <?
                            $i++;
                            if ($i > COUNT_PROBLEMS)
                                break;
                            ?>
                            <a href="<?= $error['url'] ?>" target="_blank"><?= $error['name'] ?></a><br>
                        <? } ?>
                        <div class="show-items">показаны первые
                            <?= (count($arTest['VALUE']["items"]) < COUNT_PROBLEMS) ? count($arTest['VALUE']["items"]) : COUNT_PROBLEMS ?>
                            проблем. Всего <?= count($arTest['VALUE']["items"]); ?> шт.
                        </div>
                    </div>
                <? } ?>
            </div>
            <? if ($USER->getId() == 1) { ?>
                <div class="time">Время исполнения: <?= round($arTest['TIME'], 2) ?></div>
            <? } ?>
        </li>
    <? } ?>
</ul>