<?php

use Bitrix\Main\Localization\Loc;
use WL\HL;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/gadgets/wl/rest_correction/script.js');
$APPLICATION->SetAdditionalCSS('/local/gadgets/wl/rest_correction/style.css');

$arStores = [];
$dbStores = CCatalogStore::GetList(['ID' => 'ASC'], [], false, false, ['ID', 'TITLE']);
while ($arStore = $dbStores->Fetch()) {
    $arStores[$arStore['ID']] = $arStore;
}
?>

<form data-form-rest-correction class="wl-gadget-rest-correction">
    <div class="wl-gadget-rest-correction">
        <p class="wl-gadget-rest-correction-error" style="display: none" data-error></p>
        <p class="wl-gadget-rest-correction-info" style="display: none" data-info>Остатки обновлены</p>
    </div>
    <div class="wl-gadget-rest-correction__block">
        <?= Loc::getMessage('G_WL_REST_CORRECTION_PRODUCT_ID'); ?>
        <input type="text" required name="productId" value="" data-field-product-id>
    </div>
    <div class="wl-gadget-rest-correction__block" data-product-info style="display: none;">
        <p><strong data-product-name></strong></p>
        <div data-fields-stores></div>
        <p><?= Loc::getMessage('G_WL_REST_CORRECTION_TOTAL'); ?><span data-total-quantity></span></p>
    </div>

    <input type="button" name="read" value="<?= Loc::getMessage('G_WL_REST_CORRECTION_READ') ?>" />
    <input type="button" name="save" class="adm-btn-save" style="display: none;" value="<?= Loc::getMessage('G_WL_REST_CORRECTION_SAVE') ?>" />

    <p><?= Loc::getMessage('G_WL_REST_CORRECTION_RESERVE') ?></p>
</form>