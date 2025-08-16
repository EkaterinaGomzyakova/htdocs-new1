<?

\Bitrix\Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'onManagerCouponAdd',
    'onManagerCouponAddHandler'
);

function onManagerCouponAddHandler(Bitrix\Main\Event $event) {

    $couponsGet = \Bitrix\Sale\DiscountCouponsManager::get(true, [], true);

    $count = 0;
    foreach($couponsGet as $code => $arFields) {
        $count++;

        if($count > 1) {
            \Bitrix\Sale\DiscountCouponsManager::delete($code);
        }
    }
}