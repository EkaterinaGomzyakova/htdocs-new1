<?

use Bitrix\Main\Context,
    Bitrix\Currency\CurrencyManager,
    Bitrix\Sale\Order,
    Bitrix\Sale\Basket,
    Bitrix\Sale\Delivery,
    Bitrix\Sale\PaySystem,
    \Bitrix\Main\Web\Json;
?>
<? require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php"); ?>
<?

Bitrix\Main\Loader::includeModule("sale");
Bitrix\Main\Loader::includeModule("catalog");
Bitrix\Main\Loader::includeModule("wl.snailshop");

global $USER;
$request = Context::getCurrent()->getRequest();
$siteId = Context::getCurrent()->getSite();
$currencyCode = CurrencyManager::getBaseCurrency();


$name = $request["NAME"];
$phone = str_replace(['(', ')', '-'], '', $request["PHONE"]);
$email = $request["EMAIL"];
$userId = false;

$productId = 22050;

global $USER;
if (!$USER->isAuthorized()) {
    $arFilter = [
        "ACTIVE"    => "Y",
        "EMAIL"        => $email
    ];
    $arUser = CUser::GetList(($by = "id"), ($order = "desc"), $arFilter)->Fetch();
    if ($arUser) {
        $userId = $arUser['ID'];
    } else {
        $password = rand() . rand();
        $arFields = [
            'NAME' => $name,
            'EMAIL' => $email,
            'LOGIN' => $phone,
            'PERSONAL_PHONE' => $phone,
            'PASSWORD' => $password,
            'CONFIRM_PASSWORD' => $password,
            'ACTIVE' => 'Y',
        ];
        $newUserId =  $USER->Add($arFields);

        if ($newUserId) {
            $userId = $newUserId;
            $USER->Authorize($newUserId);
        } else {
            die(JSON::encode([
                'ERROR' => 'Y',
                'MESSAGE' => $USER->LAST_ERROR,
            ]));
        }
    }
} else {
    $userId = $USER->GetID();
}

try {
    $companyID = \Bitrix\Sale\Internals\CompanyTable::getList(['filter' => ['CODE' => 'PROSPEKT_61']])->Fetch()['ID'];

    $order = Order::create($siteId, $userId);
    $order->setPersonTypeId(1);
    $order->setField('CURRENCY', $currencyCode);
    $order->setField('USER_DESCRIPTION', 'Девичник');
    $order->setField('COMPANY_ID', $companyID);


    $basket = Basket::create($siteId);
    $item = $basket->createItem('catalog', $productId); // Товар Девичник из служебного каталога
    $item->setFields(array(
        'QUANTITY' => 1,
        'CURRENCY' => $currencyCode,
        'LID' => $siteId,
        'PRODUCT_PROVIDER_CLASS' => '\CCatalogProductProvider',
    ));
    $order->setBasket($basket);


    $shipmentCollection = $order->getShipmentCollection();
    $shipment = $shipmentCollection->createItem();
    $service = Delivery\Services\Manager::getById(Delivery\Services\EmptyDeliveryService::getEmptyDeliveryServiceId());
    $shipment->setFields(array(
        'DELIVERY_ID' => $service['ID'],
        'DELIVERY_NAME' => $service['NAME'],
        'COMPANY_ID' => $companyID,
    ));
    $shipmentItemCollection = $shipment->getShipmentItemCollection();
    $shipmentItem = $shipmentItemCollection->createItem($item);
    $shipmentItem->setQuantity($item->getQuantity());


    $arPrice = CCatalogProduct::GetOptimalPrice($productId, 1, $USER->GetUserGroupArray());
    $paymentCollection = $order->getPaymentCollection();
    $payment = $paymentCollection->createItem();
    $paySystemService = PaySystem\Manager::getObjectById(16); //Банковской картой на сайте
    $payment->setFields(array(
        'PAY_SYSTEM_ID' => $paySystemService->getField("PAY_SYSTEM_ID"),
        'PAY_SYSTEM_NAME' => $paySystemService->getField("NAME"),
        'SUM' => $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'],
        'COMPANY_ID' => $companyID,
    ));

    $propertyCollection = $order->getPropertyCollection();
    $nameProp = $propertyCollection->getPayerName();
    $nameProp->setValue($name);
    $phoneProp = $propertyCollection->getPhone();
    $phoneProp->setValue($phone);
    $emailProp = $propertyCollection->getUserEmail();
    $emailProp->setValue($email);

    // Сохраняем
    $order->doFinalAction(true);
    $result = $order->save();
    $orderId = $order->getId();

    $token = $publicLink = \Bitrix\Sale\Helpers\Order::getPublicLink($order);

    if ($orderId > 0) {
        die(JSON::encode([
            'SUCCESS' => 'Y',
            'ORDER_LINK' => $token
        ]));
    } else {
        die(JSON::encode([
            'ERROR' => 'Y',
            'MESSAGE' => $result->getErrors(),
        ]));
    }
} catch (Exception $e) {
    die(JSON::encode([
        'ERROR' => 'Y',
        'MESSAGE' => $e->getMessage()
    ]));
}
