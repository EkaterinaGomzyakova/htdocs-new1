<?
use Bitrix\Sale;

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
$APPLICATION->SetTitle("Оплата заказа");
?>
<?
if(intval($_GET['ORDER_ID']) > 0) {
	$order = Sale\Order::load($_GET['ORDER_ID']);

	$onePayment = null;
	$paymentCollection = $order->getPaymentCollection();
	foreach($paymentCollection as $payment) {
		if(!$payment->isPaid()) {
			$onePayment = $payment;
		}
	}
	if($onePayment) {
		$service = Sale\PaySystem\Manager::getObjectById($onePayment->getPaymentSystemId());
		$context = \Bitrix\Main\Application::getInstance()->getContext();
		$service->initiatePay($onePayment, $context->getRequest());
	} else { ?>
		Заказ полностью оплачен.
	<? }
}
?>
<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");?>