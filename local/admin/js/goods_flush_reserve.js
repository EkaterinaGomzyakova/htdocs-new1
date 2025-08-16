function goodsFlushReserve(productId) {
    BX.ajax.runAction(
        'wl:snailshop.api.Goods.flushReserve',
        {
            data: {
                productId: productId
            }
        }
    ).then(function () {
        location.reload();
    }, function (response) {
        alert(response.errors[0].message);
    });
}