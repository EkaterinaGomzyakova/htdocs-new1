<script>
    document.addEventListener("DOMContentLoaded", function() {
        const targetNode = document.querySelector("[id^=sale_shipment_basketsale-order-basket-product]");

        targetNode.querySelectorAll('.BARCODE input[type=text]').forEach((input) => {
            input.addEventListener('input', checkContainsCyrillic);
        });

        const config = {
            attributes: false,
            childList: true,
            subtree: true
        };
        const observer = new MutationObserver(() => {
            targetNode.querySelectorAll('.BARCODE input[type=text]').forEach((input) => {
                input.removeEventListener('input', checkContainsCyrillic);
                input.addEventListener('input', checkContainsCyrillic);
            });
        });
        observer.observe(targetNode, config);

        function checkContainsCyrillic(event) {
            const input = event.target;
            const cyrillicPattern = /^[\u0400-\u04FF]+$/;
            if (cyrillicPattern.test(event.data)) { //check printed symbol
                alert('Переключите раскладку на английскую клавиатуру');
                input.value = '';
                input.blur();
                event.preventDefault();
                return false;
            }
        }
    });
</script>