<?

use WL\SnailShop;
?>
<script>
    $('document').ready(function() {
        $('.delivery-status').parents('.adm-bus-table-container').first().hide();
        $('.payment-status').parents('.adm-bus-table-container').first().hide();

        new BX.MaskedInput({
            mask: '99.99.9999',
            input: document.querySelector('input[name="PROPERTIES[25]"]'),
            stopChangeEvent: true
        });

        let params = JSON.parse($('body').attr('data-props'));

        if (params.userShopId) {
            $('[id^=SHIPMENT_COMPANY_ID_]').val(params.userShopId).attr('required', true);
            $('[id^=SHIPMENT_COMPANY_ID_] option').each(function() {
                if (this.value != params.userShopId) {
                    this.remove();
                }
            });

            $('[id^=PAYMENT_COMPANY_ID_]').val(params.userShopId).attr('required', true);
            $('[id^=PAYMENT_COMPANY_ID_] option').each(function() {
                if (this.value != params.userShopId) {
                    this.remove();
                }
            });

            $('#ORDER_COMPANY_ID').val(params.userShopId).attr('required', true);
            $('#ORDER_COMPANY_ID option').each(function() {
                if (this.value != params.userShopId) {
                    this.remove();
                }
            });
        } else {
            if (!confirm('Ваш пользователь - не консультант. Вы точно хотите продолжить?')) {
                location.href = '/bitrix/admin/sale_order.php';
            }
        }

        // let requiredRadio = document.querySelector('#order_properties_container .fwb').nextElementSibling.querySelector('input[type=radio]');

        // requiredRadio.required = true;

        // document.querySelectorAll('.adm-detail-content-btns input').addEventListener('click', function(event){
        //     if(!requiredRadio.value) {
        //         event.preventDefault();
        //         alert('Выберите источник покупателя');
        //         return false;
        //     }
        // });
    });
</script>

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