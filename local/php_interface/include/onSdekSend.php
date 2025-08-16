<?php

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
	'ipol.sdek',
	'requestSended',
	'onSdekSend'
);

/*
$orderId - ID заказа
$status - статус заявки в модуле (ERROR - ошибка, OK - отправлен)
$sdekId - идентификатор отправления в базе СДЭК
*/
function onSdekSend(string $orderId, string $status, string $sdekId)
{
	if ($status == "OK" && $order = \Bitrix\Sale\Order::load($orderId)) {
		$shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
		foreach ($shipmentCollection as $ship) {
			$ship->setFields(array(
				'TRACKING_NUMBER' => $sdekId
			));
		}
		$order->save();
	}
}