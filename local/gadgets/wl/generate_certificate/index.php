<?php use Bitrix\Main\Localization\Loc;
use WL\HL;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/gadgets/wl/generate_certificate/script.js');
$APPLICATION->SetAdditionalCSS('/local/gadgets/wl/generate_certificate/style.css');

$items = HL::table('Certificatedesigns')
    ->sort([
            'UF_SORT' => 'ASC', 'ID' => 'ASC'
        ]
    )->all();
$selectOptions = array_values(array_map(fn($item) => ['NAME' => $item['UF_NAME'], 'VALUE' => $item['UF_XML_ID']], $items));
?>

<form data-form-generate-ceartificate class="wl-gadget-generate-certificate">
    <div class="wl-gadget-generate-certificate__info" style="margin-bottom: 15px">
        <p class="bx-gadgets-warning-cont" style="display: none" data-error></p>
        <div style="display: none" data-info></div>
    </div>
    <div class="wl-gadget-generate-certificate__generate-block">
        <label class="wl-gadget-generate-certificate__field-label">
            <?= Loc::getMessage('G_WL_GENERATE_CERTIFIACTE_COUPON') ?>
            <input type="text" name="coupon" value="" data-field-coupon>
        </label>
    </div>
    <div class="wl-gadget-generate-certificate__generate-block js-generate" data-generate-block style="display: none;">
        <label class="wl-gadget-generate-certificate__field-label">
            <?= Loc::getMessage('G_WL_GENERATE_CERTIFIACTE_DESIGN') ?>
            <select name="design">
                <?php
                foreach ($selectOptions as $option) {
                    ?>
                    <option value="<?= $option['VALUE'] ?>"><?= $option['NAME'] ?></option>
                    <?php
                }
                ?>
            </select>
        </label>
        <input type="submit" class="adm-btn-save" value="<?= Loc::getMessage('G_WL_GENERATE_CERTIFIACTE_DOWNLOAD') ?>"/>
    </div>
</form>
