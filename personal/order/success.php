<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Title");
?>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_payment/tinkoff/result.php");
?>

<script>
    setTimeout(function() {
        location.href = "/";
    }, 2000);
</script>


<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>