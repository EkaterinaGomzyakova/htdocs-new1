<?php

use Bitrix\Sale;

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

CModule::IncludeModule("wl.snailshop");

if (WL\SnailShop::userIsStaff()) {
    $orderId = $_REQUEST['order_id'];
    $order = Sale\Order::load($orderId);
    $fields = $order->getAvailableFields();
    $propertyCollection = $order->getPropertyCollection()->getArray();
    $properties = [];
    foreach ($propertyCollection['properties'] as $prop) {
        $properties[$prop['CODE']] = $prop;
    }

    $paymentCollection = $order->getPaymentCollection();
    foreach ($paymentCollection as $payment) {
        $paymentName = $payment->getField('PAY_SYSTEM_NAME');
    }

?>
<textarea style="width: 100%; height: 100px !important;">Заказ №<?= $_REQUEST['order_id'] ?>
<?= $properties['FIO']['VALUE'][0] ?>

<?= $properties['PHONE']['VALUE'][0] ?>

<?= CCurrencyLang::CurrencyFormat($order->getPrice(), 'RUB'); ?>

<? if (!empty($properties['ADDRESS']['VALUE'][0])) : ?>
<?= $properties['ADDRESS']['VALUE'][0] ?>
<? endif; ?>

<?= $paymentName ?>: <? if ($order->isPaid()) : ?>оплачено<? else : ?>не оплачено<? endif; ?>

<? if (!empty($order->getField('USER_DESCRIPTION'))) : ?>
Комм. клиента: <?= $order->getField('USER_DESCRIPTION'); ?>
<? endif; ?>
<? if (!empty($order->getField('COMMENTS'))) : ?>
Комм. менеджера: <?= $order->getField('COMMENTS'); ?>
<? endif; ?>
</textarea>
<?
}
