<?

use WL\SnailShop;
?>
<? if (intval($_GET['ID']) > 0) {

    CModule::IncludeModule("sale");
    $arOrder = CSaleOrder::GetByID(intval($_GET['ID']));
    if (!SnailShop::userIsShopAdmin()) {

        if ($arOrder['PAYED'] == "Y" && $arOrder['DEDUCTED'] == "Y" && $arOrder['STATUS_ID'] == "F") {
            echo "<style data-custom>";
            echo file_get_contents(__DIR__ . '/hideFinishedOrderBlocks.css');
            echo "</style>";
        }
    } ?>

    <? if ($arOrder['STATUS_ID'] == "F" && $arOrder['DEDUCTED'] == "N" && $arOrder['PAYED'] == "Y") { ?>
        <script>
            let dialog = new BX.CDialog({
                title: 'Возможны ошибки с отгрузкой',
                content: 'Заказ выполнен, но Отгрузка не была отгружена, убедитесь что с ней все в порядке: количество достаточно на складе и установлены коды маркировки',
                'width': 400,
                'height': 70
            });

            document.addEventListener('DOMContentLoaded', function() {
                dialog.Show();
            });
        </script>
<? }
} ?>