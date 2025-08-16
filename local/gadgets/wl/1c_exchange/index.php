<?php

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$sessid = bitrix_sessid();
$orders = "/bitrix/admin/1c_exchange.php?type=sale&mode=query&sessid={$sessid}";
?>
<h2>Обмен заказами</h2>
<div class="adm-info-message" style="margin-top:0;">
    Eсли по какой-либо причине не выгрузился определённый заказ, то достаточно зайти в редактирование этого заказа и нажать "Сохранить". Тогда у заказа изменится дата редактирования и при следующем обмене он попадёт в выгрузку.
</div>
<a href="<?=$orders?>" class="adm-btn" download="order_exchange">Скачать xml</a>
<a href="<?=$orders?>" class="adm-btn" target="_blank">Посмотреть xml</a>
