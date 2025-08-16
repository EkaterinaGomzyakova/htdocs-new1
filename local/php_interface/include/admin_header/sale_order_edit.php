<?
use WL\SnailShop;
?>
<script>
    $('document').ready(function() {
        var result = new BX.MaskedInput({
            mask: '99.99.9999',
            input: document.querySelector('input[name="PROPERTIES[25]"]'),
            stopChangeEvent: true
        });

        let params = JSON.parse($('body').attr('data-props'));
        if (!params.userShopId) {
            if (!confirm('Ваш пользователь - не консультант. Вы точно хотите продолжить?')) {
                location.href = '/bitrix/admin/sale_order.php';
            }
        }
    });
</script>

<? if (!SnailShop::userIsShopAdmin() && intval($_GET['ID']) > 0) {
    CModule::IncludeModule("sale");
    $arOrder = CSaleOrder::GetByID(intval($_GET['ID']));
    if ($arOrder['PAYED'] == "Y" && $arOrder['DEDUCTED'] == "Y" && $arOrder['STATUS_ID'] == "F") {
        echo "<style data-custom>";
        echo file_get_contents(__DIR__ . '/hideFinishedOrderBlocks.css');
        echo "</style>";
    }
} ?>

<? if (!SnailShop::userIsShopAdmin()) { ?>
    <script>
        setTimeout(function() {
            disablePriceEditing();

            const target = document.querySelector('[data-id="basket"] .adm-s-order-table-ddi');
            const config = {
                childList: true,
                subtree: true,
            };

            const callback = function(mutationsList, observer) {
                disablePriceEditing();
            };

            const observer = new MutationObserver(callback);
            observer.observe(target, config);
        }, 500);

        function disablePriceEditing() {
            $("[id^=sale_order_basketsale-order-basket-product] a[href*='IBLOCK_ID=2']").parents('[id^=sale_order_basketsale-order-basket-product]').find('.formated_price').attr('style', 'pointer-events:none;');

            $("[id^=sale_order_basketsale-order-basket-product] a[href*='IBLOCK_ID=2']").parents('[id^=sale_order_basketsale-order-basket-product]').find('.pencil').attr('style', 'display: none !important;');
        }
    </script>
<? } ?>