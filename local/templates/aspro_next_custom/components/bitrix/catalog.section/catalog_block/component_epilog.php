<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $templateData */

/** @var @global CMain $APPLICATION */

use Bitrix\Main\Loader;

if (isset($templateData['TEMPLATE_LIBRARY']) && !empty($templateData['TEMPLATE_LIBRARY'])) {
    $loadCurrency = false;
    if (!empty($templateData['CURRENCIES']))
        $loadCurrency = Loader::includeModule('currency');
    CJSCore::Init($templateData['TEMPLATE_LIBRARY']);
    if ($loadCurrency) {
?>
        <script type="text/javascript">
            BX.Currency.setCurrencies(<?= $templateData['CURRENCIES']; ?>);
        </script>
<?
    }
}

if (!empty($arResult['ITEMS_ID'])) {
    $wishIDs = \WL\WishList::getListID($arResult['ITEMS_ID']);
}

$APPLICATION->AddViewContent('section_color', $arResult['UF_BACKGROUND_COLOR']);

if ($arParams['SET_TITLE'] == 'Y') {
    $APPLICATION->SetTitle($arResult['UF_H1'] ?: $arResult['NAME']);
}
?>
<script>
    $(document).ready(function() {
        <? if (!empty($wishIDs)): ?>
            <? foreach ($wishIDs as $itemID): ?>
                $('.wish_item_button .wish_item.to[data-item="<?= $itemID ?>"]').hide();
                $('.wish_item_button .wish_item.in[data-item="<?= $itemID ?>"]').show();
            <? endforeach; ?>
        <? endif; ?>
    });
</script>