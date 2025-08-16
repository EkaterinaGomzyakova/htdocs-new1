<? CJSCore::Init(['jquery3', 'masked_input']); ?>

<link rel="stylesheet" type="text/css" href="/local/admin/custom.css?rand=<?= rand() ?>">

<script src="/bitrix/js/asd.iblock/jpicker/jpicker-1.1.6.js"></script>
<script src="/local/templates/aspro_next_custom/js/jquery.inputmask.bundle.min.js"></script>

<script>
    setTimeout(function() {
        jQuery("#order_properties_container input[name=PROPERTIES\\[3\\]]").inputmask('+79999999999');
    }, 1500);

    function generateStickerUrl(target) {
        let formData = $('[name^=form_' + target + ']').serialize();
        location.href = '/local/admin/generateStickers.php?' + formData;
    }

    function generateStickerWithPriceUrl(target) {
        let formData = $('[name^=form_' + target + ']').serialize();
        location.href = '/local/admin/generateStickersWithPrice.php?' + formData;
    }
</script>

<?
CModule::IncludeModule("wl.snailshop");
$userStoreId = WL\SnailShop::getUserStoreId();
$userShopId = WL\SnailShop::getUserShopId();
$userShopName = WL\SnailShop::getUserShopName();

$arProps = [
    'userStoreId' => $userStoreId,
    'userShopId' => $userShopId,
    'userShopName' => $userShopName,
];
$bodyJson = \Bitrix\Main\Web\Json::encode($arProps);
?>
<script>
    $('body').attr('data-props', '<?= $bodyJson ?>');

    $(document).ready(function() {
        let shopName = '<?= $userShopName ?>';
        if (shopName.length > 0) {
            $('<div class="custom-current-shop"><?= $userShopName ?></div>').insertBefore('.adm-header-search-block');
        }
    });
</script>

<? if ($APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_view.php') {
    @require_once 'include/admin_header/sale_order_view.php';
} elseif ($APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_edit.php') {
    @require_once 'include/admin_header/sale_order_edit.php';
} elseif ($APPLICATION->GetCurPage() == '/bitrix/admin/iblock_element_edit.php') {
    @require_once 'include/admin_header/iblock_element_edit.php';
} elseif ($APPLICATION->GetCurPage() == "/bitrix/admin/sale_order_create.php") {
    @require_once 'include/admin_header/sale_order_create.php';
} elseif ($APPLICATION->GetCurPage() == "/bitrix/admin/sale_order_payment_edit.php") {
    @require_once 'include/admin_header/sale_order_payment_edit.php';
} elseif ($APPLICATION->GetCurPage() == "/bitrix/admin/php_command_line.php") {
    $APPLICATION->IncludeComponent('wl:phpconsole', '');
} elseif($APPLICATION->GetCurPage() == "/bitrix/admin/cat_store_document_edit.php") {
    @require_once 'include/admin_header/cat_store_document_edit.php';
} elseif($APPLICATION->GetCurPage() == "/bitrix/admin/sale_order_shipment_edit.php") {
    @require_once 'include/admin_header/sale_order_shipment_edit.php';
}

