<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (is_array($arResult['INDEX'])) {
    foreach ($arResult['INDEX'] as $fileKey => $arIndex) {
        ?>
        <ul class="adm-detail-files-content__index_list">
            <li class="adm-detail-files-content__index_item">
                <a href="#" data-filekey="<?= $fileKey ?>" title="<?= $arIndex['DESCRIPTION'] ?>"><?= $arIndex['TITLE'] ?></a>
            </li>
        </ul>
        <?php
    }
}
