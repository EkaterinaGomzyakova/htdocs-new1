<?php

namespace WL;

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Page\Asset;
use Bitrix\Main\UserTable;
use \Bitrix\Main\UI\Extension;

use CUtil;

class MainHandlers
{
    public static function OnAdminContextMenuShow(&$items): void
    {
        global $APPLICATION;

        $strCurPage = $APPLICATION->GetCurPage();
        $request = Application::getInstance()->getContext()->getRequest();

        if ($strCurPage == '/bitrix/admin/sale_order_view.php' || $strCurPage == '/bitrix/admin/sale_order_edit.php') {
            CUtil::InitJSCore(array('window'));
            Asset::getInstance()->addJs("/local/admin/js/sale_order.js");
            self::showMsgCourier($items);
            self::showConsentProcessingPersonalData($items);
            self::showRouleteButton($items);
        }

        if (
            $strCurPage == '/bitrix/admin/iblock_element_edit.php'
            && in_array($request->get('IBLOCK_ID'), [
                \WL\Iblock::getIblockIDByCode('cosmetics', 'catalog'),
                \WL\Iblock::getIblockIDByCode('cosmetics_sku', 'catalog')
            ])
            && $request->get('ID') > 0
        ) {
            Asset::getInstance()->addJs("/local/admin/js/goods_flush_reserve.js");
            $items[] = [
                'TEXT' => 'Сбросить резерв',
                'LINK' => '#',
                'LINK_PARAM' => 'onclick="goodsFlushReserve(' . $request->get('ID') . ')"',
            ];
        }
        if (
            $strCurPage == '/bitrix/admin/sale_order_view.php' ||
            $strCurPage == '/bitrix/admin/sale_order_create.php' ||
            $strCurPage == '/bitrix/admin/sale_order_edit.php'
        ) {
            $orderId = $_REQUEST['ID'];
            if (!empty($orderId)) {
                $order = \Bitrix\Sale\Order::load($orderId);
                $price = $order->getPrice();
                $priceLeft = $price - $order->getSumPaid();
            } else {
                $price = 0;
                $priceLeft = 0;
            }

            $params = json_encode(['price' => $price, 'priceLeft' => $priceLeft]);
            Extension::load('wl.modalChangeMoney');

            $items[] = array(
                'TEXT' => "Сдача",
                'LINK' => "#",
                'TITLE' => "рассчитать сдачу",
                'LINK_PARAM' => "onclick='showCalculator(" . $params . ")'"
            );
        }
    }



    public static function showMsgCourier(&$items)
    {
        $orderID = $_REQUEST['ID'];
        $items[] = array(
            "TEXT" => "Сообщение курьеру",
            "LINK" => "javascript:showCourierMsgDialog($orderID)",
            "TITLE" => "Сообщение курьеру",
        );
    }

    public static function showConsentProcessingPersonalData(&$items): void
    {
        if ($_REQUEST['ID'] > 0) {
            $orderID = $_REQUEST['ID'];
            Loader::includeModule('sale');
            $order = \Bitrix\Sale\Order::load($orderID);
            $user = UserTable::getList([
                'filter' => ['ID' => $order->getField('USER_ID')],
                'select' => ['ID', 'UF_CONSENT_PROCESSING']
            ])->fetch();

            if (!empty($user) && empty($user['UF_CONSENT_PROCESSING'])) {
                $userID = $user["ID"];
                $items[] = array(
                    "TEXT" => "Согласие на обработку",
                    "LINK" => "javascript:showConsentProcesssingPersonalDataDialog($userID)",
                    "TITLE" => "Согласие на персданные",
                    "LINK_PARAM" => 'style="color: #bf4444;"'
                );
            }
        }
    }

    public static function showRouleteButton(&$items): void
    {
        global $USER;
        $orderId = $_REQUEST['ID'];
        $order = \Bitrix\Sale\Order::load($orderId);
        $isOrderPayed = $order->getField('PAYED');
        $userId = $order->getUserId();

        $isRouleteActiveByOption = false;
        $rouleteActiveToDate = \COption::GetOptionString("wl.snailshop", "roulete_date_active_to", "01.01.2022");
        if (strtotime(date('d.m.Y h:i:s')) < strtotime($rouleteActiveToDate . " 23:59:59")) {
            $isRouleteActiveByOption = true;
        }

        if (($isRouleteActiveByOption || $USER->isAdmin()) && $isOrderPayed) {
            $items[] = array(
                "TEXT" => "Рулетка",
                "LINK" => "/roulete-retail/?USER_ID=" . $userId,
                "TITLE" => "Рулетка",
            );
        }
    }
}