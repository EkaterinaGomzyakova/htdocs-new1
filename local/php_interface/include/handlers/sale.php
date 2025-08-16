<?php

namespace WL\Handlers;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Event;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Cookie;
use Bitrix\Sale as BitrixSale;
use CSaleDiscount;
use CSaleUserAccount;
use CUser;
use Exception;
use Throwable;
use WL\OnecLoyalty\Actions\AccrueBonusAction;
use WL\OnecLoyalty\Tools\Log;
use WL\Order;
use WL\SnailShop;
use Bitrix\Main\UserTable;

class Sale
{
    protected static $handlerDisallow = false;

    protected static $shipmentNeedToBeDeducted;


    /**
     * Событие перед удалением заказа
     * @param Event $event
     * @return void|\Bitrix\Main\EventResult
     * @throws Exception
     */
    public static function OnSaleBeforeOrderDelete(Event $event) {
        $order = $event->getParameter("ENTITY");
        $orderId = $order->getId();
        
        $dbChecks = \Bitrix\Sale\Cashbox\Internals\CashboxCheckTable::getList([
            'select' => ['ID'],
            'filter' => ['ORDER_ID' => $orderId],
            'count_total' => true,
        ]);

        if ($dbChecks->getCount() > 0) {
            return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError('У заказа есть чеки, удаление невозможно'));
        }
    }    
    
    /**
     * Происходит в самом начале процесса сохранения.
     * @param Event $event
     * @return void|\Bitrix\Main\EventResult
     * @throws Exception
     */
    public static function OnSaleOrderBeforeSaved(Event $event)
    {
        if (self::$handlerDisallow) {
            return;
        }

        global $USER;
        Loader::includeModule('wl.snailshop');

        /** @var BitrixSale\Order $order */
        $order = $event->getParameter("ENTITY");
        $request = Context::getCurrent()->getRequest();
        $userStoreId = SnailShop::getUserStoreId();
        $paymentCollection = $order->getPaymentCollection();
        $shipmentCollection = $order->getShipmentCollection();
        $propertyCollection = $order->getPropertyCollection();
        $userId = $order->getField('USER_ID');

        $user = UserTable::query()
            ->setFilter(['ID' => $userId])
            ->setSelect(['ID', 'PERSONAL_PHONE'])
            ->fetchObject();

        //Не заполнено свойство Источник
        if ($request->isAdminSection()) {
            foreach ($propertyCollection as $property) {
                if ($property->getField('CODE') == "BUYER_SOURCE") {
                    if (!$property->getValue()) {
                        return new \Bitrix\Main\EventResult(\Bitrix\Main\EventResult::ERROR, new \Bitrix\Sale\ResultError('Не заполнено свойство Источник'));
                    }
                }
            }
        }

        foreach ($propertyCollection as $property) {
            if ($property->getField('CODE') === "DELIVERY_FULL_PRICE") {
                $deliveryFullPrice = 0.0;
                foreach($shipmentCollection->getNotSystemItems() as $shipment) {
                    $deliveryFullPrice = $shipment->getField('DISCOUNT_PRICE');
                }

                $property->setValue($deliveryFullPrice);
            }
        }

        //Свойство $order IS_NEW недоступно в этом событии
        if ($order->getId() == 0) {
            if (!$request->isAdminSection()) {
                $order->setField('RESPONSIBLE_ID', 1);
                
                foreach ($propertyCollection as $property) {
                    if ($property->getField('CODE') == "BUYER_SOURCE") {
                        $property->setValue('exist');
                    }

                    if ($property->getField('CODE') === "CARD_NUMBER") {
                        $phone = preg_replace('/[^0-9]/', '', $user->getPersonalPhone());
                        $property->setValue($phone);
                    }
                }
            } elseif ($request->isAdminSection() && $userStoreId > 0) {
                if (!$order->getField('RESPONSIBLE_ID')) {
                    $order->setField('RESPONSIBLE_ID', $USER->getId());
                }
            }

            $dbBuyerStatistic = \Bitrix\Sale\Internals\BuyerStatisticTable::getList([
                'select' => ['USER_ID'],
                'count_total' => true,
                'filter' => ['USER_ID' => $userId],
                'limit' => 1
            ]);

            $userTotalOrderCount = $dbBuyerStatistic->getCount();

            if ($userTotalOrderCount == 0) {
                foreach ($propertyCollection as $property) {
                    if ($property->getField('CODE') == "IS_FIRST_ORDER") {
                        $property->setValue('Y');
                    }
                }
            }
        }

        if (($order->getField('RESPONSIBLE_ID') == 1 || empty($order->getField('RESPONSIBLE_ID'))) && SnailShop::userIsStaff()) {
            if ($USER->getId() > 0) {
                $order->setField('RESPONSIBLE_ID', $USER->getId());
            }
        }


        $paidSum = $paymentCollection->getPaidSum();
        if ($paidSum > 0) {
            $order->setFieldNoDemand('SUM_PAID', $paidSum);

            if ($paidSum == $paymentCollection->getSum()) {
                $order->setFieldNoDemand('PAYED', 'Y');
            }
        }

        if ($order->getField('CANCELED') == "Y") {
            if (!in_array($order->getField('STATUS_ID'), ["CE", "CA"])) { //If Status not Cancelled nor AutoCancelled
                $order->setField('STATUS_ID', "CA"); //Set status Cancelled
            }
        }

        $event->addResult(
            new \Bitrix\Main\EventResult(
                \Bitrix\Main\EventResult::SUCCESS,
                $order
            )
        );
    }

    /**
     * Происходит после сохранения заказа
     * @param Event $event
     * @return void|\Bitrix\Main\EventResult
     * @throws Exception
     */
    public static function OnSaleOrderSaved(Event $event)
    {
        if (self::$handlerDisallow) {
            return;
        }
        self::$handlerDisallow = true;

        Loader::includeModule('wl.snailshop');

        $order = $event->getParameter("ENTITY");
        $paymentCollection = $order->getPaymentCollection();
        $propertyCollection = $order->getPropertyCollection();
        $shipmentCollection = $order->getShipmentCollection();

        $isNew = $event->getParameter("IS_NEW");
        $userId = $order->getField('USER_ID');
        $request = Context::getCurrent()->getRequest();
        $userStoreId = SnailShop::getUserStoreId();
        $defaultStore = SnailShop::getDefaultStore();

        if ($isNew) {
            if (!$request->isAdminSection()) {

                $obUser = new CUser();
                $obUser->Update($userId, ['UF_CONSENT_PROCESSING' => 1]);

                foreach ($propertyCollection as $property) {
                    if ($property->getField('CODE') == "ORDER_CREATED_BY_BUYER") {
                        $property->setValue('Y');
                    }

                    if ($property->getField('CODE') == 'UTM' && $request->getCookie('utm_source')) {
                        $property->setValue($request->getCookie('utm_source'));
                        Context::getCurrent()->getResponse()->addCookie(new Cookie('utm_source', '', time() - 86400));
                    }
                }

                $order->setField('COMPANY_ID', $defaultStore['ID']);

                Order::setShipmentItemStore($shipmentCollection, $defaultStore['ID']);
                $shipmentCollection->save();
            } elseif ($request->isAdminSection() && $userStoreId > 0) {

                Order::setShipmentItemStore($shipmentCollection, $userStoreId);
                $shipmentCollection->save();

                if ($order->getField('STATUS_ID') == "F" && !isset(self::$shipmentNeedToBeDeducted)) {
                    self::$shipmentNeedToBeDeducted = true;
                }
            }

            if ($order->getField('STATUS_ID') == "F") {
                foreach ($paymentCollection as $payment) {
                    $payment->setPaid("Y");
                }
            }
        }

        $propertyCollection->save();
    }

    /**
     * Происходит после оплаты заказа
     * @param Event $event
     * @return void
     * @throws Exception
     */
    public static function OnSaleOrderPaid(Event $event)
    {
        /** @var BitrixSale\Order $order */
        $order = $event->getParameter("ENTITY");
        Loader::includeModule('sale');

        Order::earnPoints($order);
        Order::sendCoupon($order);
        Order::AddBonusesForOrderDelivery($order);

        if (self::$shipmentNeedToBeDeducted) {
            $shipmentCollection = $order->getShipmentCollection();
            foreach ($shipmentCollection->getNotSystemItems() as $shipment) {
                $shipment->setField('DEDUCTED', 'Y');
            }
            self::$handlerDisallow = true;
            self::$shipmentNeedToBeDeducted = false;
            $order->save();
        }
    }

    public static function OnOrderAddHandler($orderID, $arFields) {
        self::OnSaleStatusOrder($orderID, $arFields['STATUS_ID']);
    }

    public static function OnSaleStatusOrder($orderID, $statusID)
    {
        if ($statusID == "F") {
            $referalActive = Option::get('wl.snailshop', 'referal_is_active');
            $referalXmlIDBasketRule = Option::get('wl.snailshop', 'referal_xml_id_basket_rule');

            if ($referalActive === "Y" && !empty($referalXmlIDBasketRule)) {
                $dbResultBasketRules = CSaleDiscount::GetList(
                    ["SORT" => "ASC"],
                    [
                        "XML_ID" => $referalXmlIDBasketRule,
                        "ACTIVE" => "Y",
                    ],
                    false,
                    false,
                    ["ID"]
                );

                if ($arResultBasketRule = $dbResultBasketRules->Fetch()) {
                    $idReferalBasketRule = $arResultBasketRule["ID"];
                }

                if ($idReferalBasketRule > 0) {
                    $order = BitrixSale\Order::load($orderID);
                    $orderPaidSum = $order->getSumPaid();

                    $discountData = $order->getDiscount()->getApplyResult();
                    $referalNumberPoints = Option::get('wl.snailshop', 'referal_number_points');
                    $referalMinimumOrderAmount = Option::get('wl.snailshop', 'referal_minimum_order_amount');

                    if ($orderPaidSum >= $referalMinimumOrderAmount) {
                        foreach ($discountData['COUPON_LIST'] as $coupon) {
                            if ($coupon['DATA']['DISCOUNT_ID'] == $idReferalBasketRule) {
                                $descriptionForUpdateAccount = 'Начисление бонусных по купону ' . $coupon['DATA']['COUPON'];
                                $couponPrefix = '';
                                $userId = 0;
                                [$couponPrefix, $userId] = explode("-", $coupon['DATA']['COUPON']);
                                if ($couponPrefix == "REF" && intval($userId) > 0 && $userId != $order->getUserId()) {
                                    CSaleUserAccount::UpdateAccount(
                                        $userId,
                                        $referalNumberPoints,
                                        "RUB",
                                        $descriptionForUpdateAccount,
                                        $orderID
                                    );

                                    if (Loader::includeModule('wl.onec_loyalty')) {
                                        try {
                                            (new AccrueBonusAction())->run(
                                                userId: $userId,
                                                sum   : $referalNumberPoints
                                            );
                                        } catch (Throwable $exception) {
                                            Log::getInstance()->exception($exception);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
