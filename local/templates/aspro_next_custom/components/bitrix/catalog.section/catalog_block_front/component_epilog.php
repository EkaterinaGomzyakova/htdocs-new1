<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
/** @var array $templateData */

/** @var @global CMain $APPLICATION */

if (!empty($arResult['ITEMS_ID'])) {
    $wishIDs = \WL\WishList::getListID($arResult['ITEMS_ID']);
} ?>
<script>
    $(document).ready(function() {
        <? if (!empty($wishIDs)) : ?>
            <? foreach ($wishIDs as $itemID) : ?>
                $('.wish_item_button .wish_item.to[data-item="<?= $itemID ?>"]').hide();
                $('.wish_item_button .wish_item.in[data-item="<?= $itemID ?>"]').show();
            <? endforeach; ?>
        <? endif; ?>
    });
</script>