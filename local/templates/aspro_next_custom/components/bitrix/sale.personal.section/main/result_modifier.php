<?php

use Bitrix\Main\Config\Option;

$referalActive = Option::get('wl.snailshop', 'referal_is_active');
$referalXmlIDBasketRule = Option::get('wl.snailshop', 'referal_xml_id_basket_rule');
$referalMinimumOrderAmount = Option::get('wl.snailshop', 'referal_minimum_order_amount');
$referalNumberPoints = Option::get('wl.snailshop', 'referal_number_points');

global $USER;
$coupon = 'REF-' . $USER->GetID();
$existCoupon = Bitrix\Sale\DiscountCouponsManager::isExist($coupon);

if (($referalActive === "Y") && $existCoupon) {
    $arResult["REFERAL_COUPON"] = $coupon;
}