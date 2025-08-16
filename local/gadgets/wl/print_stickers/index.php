<?php use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
\Bitrix\Main\Page\Asset::getInstance()->addJs('/local/gadgets/wl/generate_certificate/script.js');
$APPLICATION->SetAdditionalCSS('/local/gadgets/wl/generate_certificate/style.css');
?>

<form class="wl-gadget-generate-certificate" method="GET" action="/local/admin/generateStickersWithPrice.php">
    <div class="">
        <label class="wl-gadget-generate-certificate__field-label">
            <?= Loc::getMessage('G_WL_IDS') ?>
            <br>
            <input type="text" width="100%" name="IDS" value="" style="width: 100%; width: -webkit-fill-available;">
        </label>
        <br>
        <input type="submit" class="adm-btn-save" value="Сгенерировать"/>
    </div>
</form>
