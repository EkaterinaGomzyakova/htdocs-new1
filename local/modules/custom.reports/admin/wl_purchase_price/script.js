document.addEventListener('DOMContentLoaded', function () {
    $('#report_ajax').on('click', '.js-price-save', function (){
        let $block = $(this).closest('.js-price-block');

        BX.ajax.runAction(
            'wl:snailshop.api.Price.updatePrice',
            {
                data: {
                    value: $block.find('.js-price').val(),
                    product_id: $block.data('product-id')
                }
            }
        ).then(function () {
            $block.removeClass('changed');
            $block.addClass('success');
        }, function(response) {
            alert(response.errors[0].message);
        });
       return false;
    });

    $('#report_ajax').on('input', '.js-price', function (e) {
        $(this).closest('.js-price-block').addClass('changed');

    });

    $('#report_ajax').on('keydown', '.js-price', function (e) {
        if (e.keyCode === 13) {
            $(this).closest('.js-price-block').find('.js-price-save').click();
        }
    });

})