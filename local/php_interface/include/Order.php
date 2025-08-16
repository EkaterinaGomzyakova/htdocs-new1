<?php

namespace WL;

use Bitrix\Main\Application;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ArgumentOutOfRangeException;
use Bitrix\Main\ArgumentTypeException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\NotImplementedException;
use Bitrix\Main\NotSupportedException;
use Bitrix\Main\ObjectNotFoundException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Internals\DiscountCouponTable;
use Bitrix\Sale\ShipmentCollection;
use setasign\Fpdi\PdfParser\CrossReference\CrossReferenceException;
use setasign\Fpdi\PdfParser\Filter\FilterException;
use setasign\Fpdi\PdfParser\PdfParserException;
use setasign\Fpdi\PdfParser\Type\PdfTypeException;
use setasign\Fpdi\PdfReader\PdfReaderException;
use SnailShop\Controller\GenerateCertificate;
use Throwable;
use WL\OnecLoyalty\Actions\AccrueBonusAction;
use WL\OnecLoyalty\Tools\Log;

class Order
{

    private static $orderChanged = false;

    private static $orderDeliveryBonusesEnrolled = false;

    /**
     * Установить склад отгрузки для товаров.
     * После выполнения метода следует вызывать $shipmentCollection->save();
     *
     * @param ShipmentCollection $shipmentCollection
     * @param int $storeId
     * @return false|void
     * @throws LoaderException
     */
    public static function setShipmentItemStore(\Bitrix\Sale\ShipmentCollection $shipmentCollection, int $storeId)
    {
        if (empty($shipmentCollection) || !$storeId) {
            return false;
        }

        Loader::includeModule('sale');

        foreach ($shipmentCollection->getNotSystemItems() as $shipment) {
            $shipment->setStoreId($storeId);

            $shipmentItemCollection = $shipment->getShipmentItemCollection();

            foreach ($shipmentItemCollection as $shipmentItem) {
                $storeItemCollection = $shipmentItem->getShipmentItemStoreCollection();
                if ($storeItemCollection->isEmpty()) {
                    $basketItem = $shipmentItem->getBasketItem();

                    $storeItem = $storeItemCollection->createItem($basketItem);
                    $storeItem->setFields([
                        'STORE_ID' => $storeId,
                        'QUANTITY' => $shipmentItem->getField('QUANTITY'),
                    ]);
                }
            }
        }
    }

    /**
     * Начисление бонусов за заказ
     *
     * @param \Bitrix\Sale\Order $order
     * @throws ArgumentException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws LoaderException
     * @throws NotImplementedException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function earnPoints(\Bitrix\Sale\Order $order)
    {
        Loader::includeModule('sale');
        if (!$order->isPaid()) {
            return;
        }
        if (!self::$orderChanged) {
            $propertyCollection = $order->getPropertyCollection();

            foreach ($propertyCollection as $prop) {
                if ($prop->getField('CODE') == 'BONUSES_EARNED') {
                    $bonusEarned = $prop->getValue();
                    if (empty($bonusEarned)) {
                        $basket = $order->getBasket();
                        $sum = $basket->getBasePrice();
                        $points = ceil($sum / 100 * COUNT_BONUS_POINTS);
                        $prop->setValue($points);
                        \CSaleUserAccount::UpdateAccount($order->getField('USER_ID'), $points, 'RUB', 'Бонусы за заказ №' . $order->getId(), $order->getId(), '');
                        self::$orderChanged = true;
                        $order->save();

                        if (Loader::includeModule('wl.onec_loyalty')) {
                            try {
                                (new AccrueBonusAction())->run(
                                    userId: $order->getUserId(),
                                    sum   : $points
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

    /**
     * Получение ID свойства, определяющего выдачу сертификата для печати
     *
     * @param int $iblockId
     * @return int|null
     */
    private static function getProductPropPdfId(int $iblockId): ?int
    {
        if ($arFields = \CIBlockPropertyEnum::GetList(
                arFilter: [
                    'IBLOCK_ID' => $iblockId,
                    'CODE' => 'GIFT_CERTIFICATE_PRINT',
                    'XML_ID' => 'pdf'
                ]
            )->Fetch()
        ) {
            return (int) $arFields['ID'];
        }
        return null;
    }

    /**
     * Отправка купона по заказу
     *
     * @param \Bitrix\Sale\Order $order заказ
     * @param bool $byEmail отправить по Email (если возможно)
     * @param bool $bySms отправить через СМС (если возможно)
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws ArgumentNullException
     * @throws ArgumentOutOfRangeException
     * @throws ArgumentTypeException
     * @throws LoaderException
     * @throws NotImplementedException
     * @throws NotSupportedException
     * @throws ObjectNotFoundException
     * @throws CrossReferenceException
     * @throws FilterException
     * @throws PdfParserException
     * @throws PdfTypeException
     * @throws PdfReaderException
     */
    public static function sendCoupon(\Bitrix\Sale\Order $order, bool $byEmail = true, bool $bySms = true): void
    {
        if (!$order->isPaid()) {
            return;
        }

        static $smsSent = false;
        static $emailSent = false;

        $basketItems = $order->getBasket()->getBasketItems();

        // Список продуктов
        $products = [];
        $dbProducts = \CIBlockElement::GetList(
            arFilter: [
                'ID' => array_map(fn($obItem) => $obItem->getProductId(), $basketItems)
            ],
            arSelectFields: [
                'ID',
                'IBLOCK_ID',
                'PROPERTY_GIFT_CERTIFICATE',
                'PROPERTY_DESIGN',
                'DETAIL_PICTURE',
                'PREVIEW_PICTURE',
                'PROPERTY_GIFT_CERTIFICATE_PRINT'
            ]
        );
        while ($arFields = $dbProducts->fetch()) {
            $arFields['DETAIL_PICTURE'] = $arFields['DETAIL_PICTURE']
                ? (int) $arFields['DETAIL_PICTURE']
                : null;
            $arFields['PREVIEW_PICTURE'] = $arFields['PREVIEW_PICTURE']
                ? (\CFile::GetFileArray($arFields['PREVIEW_PICTURE'])['SRC'] ?? '')
                : '';
            $products[$arFields['ID']] = $arFields;
        }

        $user = \Bitrix\Main\UserTable::getList([
            'filter' => ['ID' => $order->getUserId()],
            'select' => ['ID', 'EMAIL', 'PERSONAL_PHONE']
        ])->fetch();

        $smsTemplate = false;
        if ($bySms && !empty($user['PERSONAL_PHONE']) && file_exists($_SERVER['DOCUMENT_ROOT'] . '/include/sms_templates/certificate.php')) {
            Loader::includeModule('rarus.sms4b');
            $smsTemplate = file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/include/sms_templates/certificate.php');
            $sms4b = new \Csms4b();
            $smsTo = $user['PERSONAL_PHONE'];
            $smsSender = 'clanbeauty';
        }

        /** @var \Bitrix\Sale\BasketItem $basketItem */
        foreach ($basketItems as $basketItem) {
            $product = $products[$basketItem->getProductId()];

            $propPdfId ??= self::getProductPropPdfId($product['IBLOCK_ID']);

            if ($product['PROPERTY_GIFT_CERTIFICATE_VALUE'] > 0) {
                $basketPropertyCollection = $basketItem->getPropertyCollection();
                $props = $basketPropertyCollection->getPropertyValues();
                $quantity = (int) $basketItem->getQuantity();
                $couponSum = number_format($basketItem->getBasePrice(), 0, '.', ' ');

                $certificateTemplateData = GenerateCertificate::getCertificateTemplateData(
                    designXmlId: $product['PROPERTY_DESIGN_VALUE'] ?: null,
                    defaultPictureId: $product['DETAIL_PICTURE']
                );

                $coupons = [];
                if (!isset($props['COUPON'])) {
                    for ($n = 0; $n < $quantity; $n++) {
                        $coupon = DiscountCouponTable::generateCoupon(true);
                        $couponFields = [
                            'DISCOUNT_ID' => $product['PROPERTY_GIFT_CERTIFICATE_VALUE'],
                            'COUPON' => $coupon,
                            'TYPE' => DiscountCouponTable::TYPE_ONE_ORDER,
                            'ACTIVE_FROM' => '',
                            'ACTIVE_TO' => '',
                            'MAX_USE' => 1,
                            'USER_ID' => '',
                            'DESCRIPTION' => 'К заказу ' . $order->getId(),
                        ];
                        $addDb = DiscountCouponTable::add($couponFields);

                        if ($addDb->isSuccess()) {
                            $coupons[] = $coupon;
                        } else {
                            AddMessage2Log(["Ошибка добавления купона", $couponFields]);
                            throw new \SystemException('Не удалось получить код сертификата. Сожалеем, что так получилось. Обратитесь в магазин, указав номер заказа '  . $order->getId() . '.');
                        }
                    }
                    
                    if (count($coupons)) {
                        $basketPropertyCollection->setProperty([
                            [
                                'NAME' => 'Купон',
                                'CODE' => 'COUPON',
                                'VALUE' => implode(' ', $coupons),
                                'SORT' => 100,
                            ],
                        ]);
                        $basketPropertyCollection->save();
                    }
                } else {
                    $coupons = explode(' ', $props['COUPON']['VALUE']);
                }

                if (count($coupons)) {
                    if (!$emailSent && $byEmail && $propPdfId && $product['PROPERTY_GIFT_CERTIFICATE_PRINT_ENUM_ID'] == $propPdfId && $user['EMAIL']) {
                        $certificateTemplateData['TEXT']['SUM']['VALUE'] = $couponSum;
                        foreach ($coupons as $coupon) {
                            $certificateTemplateData['TEXT']['COUPON']['VALUE'] = $coupon;
                            $couponFile = GenerateCertificate::makePdf($certificateTemplateData);
                            $sendFields = [
                                "EVENT_NAME" => "CLAN_SEND_COUPON",
                                "LID" => "s1",
                                "C_FIELDS" => [
                                    "USER_ID" => $user['ID'],
                                    "EMAIL" => $user['EMAIL'],
                                    "COUPON" => $coupon,
                                    "COUPONSUM" => $couponSum,
                                    "COUPON_IMG" => 'https://' . Option::get('main', 'server_name') . $product['PREVIEW_PICTURE'],
                                ],
                                "FILE" => [
                                    Application::getDocumentRoot() . $couponFile['link']
                                ],
                            ];
                            \Bitrix\Main\Mail\Event::send($sendFields);
                        }
                        $emailSent = true;
                    }

                    if (!$smsSent && $smsTemplate) {
                        try {
                            foreach ($coupons as $coupon) {
                                $message = str_replace(['#COUPON#', '#COUPONSUM#'], [$coupon, $couponSum], $smsTemplate);
                                $sms4b->sendSingleSms($message, $smsTo, $smsSender);
                            }
                            $smsSent = true;
                        } catch (\Rarus\Sms4b\Exceptions\Sms4bException $e) {
                            AddMessage2Log($e->getMessage());
                        }
                    }
                }
            }
        }
    }

    /**
     * @param \Bitrix\Sale\Order $order
     * @return void
     */
    public static function AddBonusesForOrderDelivery(\Bitrix\Sale\Order $order)
    {
        if (!$order->isPaid() || self::$orderDeliveryBonusesEnrolled) {
            return;
        }

        $bonusPointsFromOrderDeliveryAmount = \Bitrix\Main\Config\Option::get('wl.snailshop', 'bonus_points_from_order_delivery_amount');
        $bonusPointsFromOrderDeliveryAmountMinOrderSum = \Bitrix\Main\Config\Option::get('wl.snailshop', 'bonus_points_from_order_delivery_amount_min_order_sum');
        $bonusPointsFromOrderDeliveryAmountDateBeginning = strtotime(\Bitrix\Main\Config\Option::get('wl.snailshop', 'bonus_points_from_order_delivery_amount_date_beginning'));
        $bonusPointsFromOrderDeliveryAmountDateEnd = strtotime(\Bitrix\Main\Config\Option::get('wl.snailshop', 'bonus_points_from_order_delivery_amount_date_end'));

        $today = strtotime(date("d.m.y"));

        if ($bonusPointsFromOrderDeliveryAmount == "Y" && $today >= $bonusPointsFromOrderDeliveryAmountDateBeginning && $today <= $bonusPointsFromOrderDeliveryAmountDateEnd) {


            $orderPrice = $order->getPrice();
            $orderDeliveryPrice = $order->getDeliveryPrice();
            $orderPriceWithoutDelivery = $orderPrice - $orderDeliveryPrice;

            if ($orderPriceWithoutDelivery >= $bonusPointsFromOrderDeliveryAmountMinOrderSum && $order->getSumPaid() == $order->getPrice() && $orderDeliveryPrice != 0) {
                $descriptionForUpdateAccount = "Начислять бонусные баллы равные сумме доставки заказа";

                \CSaleUserAccount::UpdateAccount(
                    $order->getUserId(),
                    $orderDeliveryPrice,
                    "RUB",
                    $descriptionForUpdateAccount,
                    $order->getId()
                );

                if (Loader::includeModule('wl.onec_loyalty')) {
                    try {
                        (new AccrueBonusAction())->run(
                            userId: $order->getUserId(),
                            sum   : $orderDeliveryPrice
                        );
                    } catch (Throwable $exception) {
                        Log::getInstance()->exception($exception);
                    }
                }

                self::$orderDeliveryBonusesEnrolled = true;
            }
        }
    }
}
