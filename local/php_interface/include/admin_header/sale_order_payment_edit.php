<script>
    $('document').ready(function() {
        let params = JSON.parse($('body').attr('data-props'));
        
        if (params.userShopId) {
            $('[id^=PAYMENT_COMPANY_ID_]').val(params.userShopId).attr('required', true);

            $('[id^=PAYMENT_COMPANY_ID_] option').each(function() {
                if (this.value != params.userShopId) {
                    this.remove();
                }
            });
        }
    });
</script>