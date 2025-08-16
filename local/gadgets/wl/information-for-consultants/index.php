<?php

use Bitrix\Main\Localization\Loc;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}
?>

<?
global $USER;
global $DB;

$arUserGroups = CUser::GetUserGroup($USER->GetID());

$resultGroups = CGroup::GetList($by = "id", $order = "asc", []);
$arFullUserGroups = [];
while ($bdGroups = $resultGroups->Fetch()) {
    foreach ($arUserGroups as $key => $group) {
        if ($group == $bdGroups["ID"]) {
            $arFullUserGroups[$key]["ID"] = $bdGroups["ID"];
            $arFullUserGroups[$key]["STRING_ID"] = $bdGroups["STRING_ID"];
        }
    }
}

$showGadgetInfo = false;

foreach ($arFullUserGroups as $group) {
    if (($group["STRING_ID"] == "admins") || ($group["STRING_ID"] == "sellers") || ($group["STRING_ID"] == "sale_administrator")) {
        $showGadgetInfo = true;
    }
}
if ($showGadgetInfo) {
    $arMonths = [
        '01' => Loc::getMessage('January'),
        '02' => Loc::getMessage('February'),
        '03' => Loc::getMessage('March'),
        '04' => Loc::getMessage('April'),
        '05' => Loc::getMessage('May'),
        '06' => Loc::getMessage('June'),
        '07' => Loc::getMessage('July'),
        '08' => Loc::getMessage('August'),
        '09' => Loc::getMessage('September'),
        '10' => Loc::getMessage('October'),
        '11' => Loc::getMessage('November'),
        '12' => Loc::getMessage('December'),
    ];

    $dateFrom = DateTime::createFromFormat('d.m.Y', date('d.m.Y', strtotime('-1 month')))->format('01.m.Y');

    $giftCertificateIds = [
        GIFT_CERTIFICATE_PRODUCT_ID,
    ];

    $dbCertificateProducts = CIBLockElement::GetList(
        [],
        [
            'IBLOCK_ID' => GOODS_IBLOCK_ID,
            "!PROPERTY_GIFT_CERTIFICATE" => false,
        ],
        false,
        false,
        ['ID', 'PROPERTY_GIFT_CERTIFICATE']
    );
    while ($arCertificateProduct = $dbCertificateProducts->Fetch()) {
        $giftCertificateIds[] = $arCertificateProduct['ID'];
    }

    $dbRes = \Bitrix\Sale\Order::getList(
        [
            'filter' => [
                ">=DATE_STATUS" => $dateFrom,
                "RESPONSIBLE_ID" => $USER->GetID(),
                "STATUS_ID" => "F",
                "CANCELED" => "N",
                "PAYED" => "Y",
                "!PAY_SYSTEM_ID" => PAY_SYSTEMS_ID_EXCLUDE_FROM_SALARY,
                'PROPERTY.CODE' => 'ORDER_CREATED_BY_BUYER',
                'PROPERTY.VALUE' => 'N',
            ],
            'order' => ['DATE_STATUS' => 'DESC'],
            'select' => ['ID', 'DATE_STATUS']
        ]
    );

    $arOrdersSortedByMonth = [];
    $allIdOrders = [];
    $productCount = [];

    while ($arOrder = $dbRes->fetch()) {

        $totalSum = 0.00;
        $order = Bitrix\Sale\Order::load($arOrder['ID']);


        $allIdOrders[] = $arOrder["ID"];
        $month = date('m', strtotime($arOrder["DATE_STATUS"]));

        $shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
        foreach ($shipmentCollection as $shipment) {
            $systemItemCollection = $shipment->getShipmentItemCollection();
            $itemCollection = $systemItemCollection->getSellableItems();

            foreach ($itemCollection as $shipmentItem) {
                $basketItem = $shipmentItem->getBasketItem();
                $productCount[$month] += $basketItem->getQuantity();

                if (in_array($basketItem->getProductId(), $giftCertificateIds) || CIBlockElement::GetIBlockByID(
                    $basketItem->getProductId()
                ) == ADDITIONAL_CATALOG_IBLOCK_ID) {
                    continue;
                }
                $totalSum += $basketItem->getQuantity() * $basketItem->getPrice();
            }
            $arOrder["TOTAL_SUM_ITEM"] = $totalSum;
        }

        $arOrdersSortedByMonth[$month][] = $arOrder;
    }

    $res = CSaleBasket::GetList([], ["ORDER_ID" => $allIdOrders], false, false, ['ORDER_ID', 'QUANTITY']);
    $productsBasket = [];
    while ($arItem = $res->Fetch()) {
        $productsBasket[$arItem["ORDER_ID"]] += $arItem["QUANTITY"];
    }
?>

    <table class="bx-gadgets-table">
        <tr>
            <th></th>
            <th><?= Loc::getMessage('average_check') ?></th>
            <th><?= Loc::getMessage('average_quantity_products') ?></th>
            <th><?= Loc::getMessage('check_count') ?></th>
            <th><?= Loc::getMessage('product_count') ?></th>
            <th><?= Loc::getMessage('sum_paid_without_certificate') ?></th>
        </tr>

        <? foreach ($arOrdersSortedByMonth as $key => $orderSortedByMonth) {
            $sumPrice = 0.00;
            $quantityProducts = 0;
            $sumPaidWithoutCertificate = 0.00;
            foreach ($orderSortedByMonth as $order) {
                foreach ($productsBasket as $keyBasket => $item) {
                    if ($keyBasket == $order["ID"]) {
                        $quantityProducts += $item;
                    }
                }

                $sumPrice += $order["PRICE"];
                $sumPaidWithoutCertificate += $order["TOTAL_SUM_ITEM"];
            }
            $averageCheck = round($sumPaidWithoutCertificate / count($orderSortedByMonth), 2);
            $averageQuantityProducts = round($quantityProducts / count($orderSortedByMonth), 2);

            $selectMonths = "";
            foreach ($arMonths as $keyMonth => $month) {
                if ($key == $keyMonth) {
                    $selectMonths = $month;
                }
            } ?>

            <tr>
                <td><strong><?= $selectMonths ?></strong></td>
                <td align="center"><?= CurrencyFormat($averageCheck, 'RUB'); ?></td>
                <td align="center"><?= $averageQuantityProducts ?></td>
                <td align="center"><?= count($orderSortedByMonth) ?></td>
                <td align="center"><?= $productCount[$key] ?></td>
                <td align="center"><?= CurrencyFormat($sumPaidWithoutCertificate, 'RUB'); ?></td>
            </tr>
        <?php
        }
        ?>
    </table>
<?php
}
?>