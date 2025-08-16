<?php

namespace Clanbeauty;

use CModule;
use CBitrixComponent;
use Bitrix\Main\Grid\Declension;

class Roulete extends CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable
{
    private $sumFrom = 0.00;
    public $arResult = [];

    public function executeComponent()
    {
        if ($this->isRouleteActive()) {
            $this->prepareData();

            if (!empty($this->arResult['ORDER_ID']) && count($this->arResult['PRIZES']['ITEMS'])) {
                $this->includeComponentTemplate();
            }
        }
    }

    protected function listKeysSignedParameters()
    {
        return [
            'IBLOCK_ID',
            'USER_ID'
        ];
    }

    private function prepareData()
    {
        $arUser = \CUser::GetById($this->arParams['USER_ID'])->Fetch();
        $this->arResult['USER_NAME'] = implode(' ', [$arUser['NAME'], $arUser['LAST_NAME']]);
        $dateFrom = \COption::GetOptionString("wl.snailshop", "roulete_order_date_payed", "01.01.3000");
        $this->sumFrom = \COption::GetOptionString("wl.snailshop", "roulete_order_sum", 2_000);

        $arOrdersByUser = $this->getUserOrders($this->arParams['USER_ID'], $dateFrom);
        $arAvailablePrizes = $this->getPrizes();
        $arAllPrizes = $this->getAllPrizes();

        $arTotalWinnersOrdersList = [];
        foreach ($arAllPrizes as $arPrize) {
            if (empty($arPrize['PROPERTIES']['WINNERS']['VALUE'])) {
                continue;
            }

            if (!is_array($arPrize['PROPERTIES']['WINNERS']['VALUE'])) {
                $arPrize['PROPERTIES']['WINNERS']['VALUE'] = [$arPrize['PROPERTIES']['WINNERS']['VALUE']];
            }
            $arTotalWinnersOrdersList = array_merge($arTotalWinnersOrdersList, $arPrize['PROPERTIES']['WINNERS']['VALUE']);
        }

        if (!empty($arOrdersByUser)) {
            $availableOrders = $this->getAvailableOrder($arOrdersByUser, $arTotalWinnersOrdersList);
        } else {
            return false;
        }

        if (count($availableOrders) > 0) {
            $this->arResult['ORDER_ID'] = $availableOrders[0];
            $this->arResult['ORDERS_TOTAL_COUNT'] = count($availableOrders);

            $jsChartData = [];
            foreach ($arAvailablePrizes as $arPrize) {
                $color = $arPrize['PROPERTIES']['COLOR']['VALUE'] ? "#" . $arPrize['PROPERTIES']['COLOR']['VALUE'] : "#F1D302";
                $textColor = $arPrize['PROPERTIES']['TEXT_COLOR']['VALUE'] ? "#" . $arPrize['PROPERTIES']['TEXT_COLOR']['VALUE'] : "#000";
                $image = \CFile::ResizeImageGet($arPrize['PREVIEW_PICTURE'], ['width' => 200, 'height' => 200], \BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
                $jsChartData[] = [
                    'name' => $arPrize['NAME'],
                    'id' => $arPrize['ID'],
                    'color' => $color,
                    'text_color' => $textColor,
                    'size' => 100,
                    'image' => $image['src']
                ];
            }

            $this->arResult['PRIZES']['ITEMS'] = $arAvailablePrizes;
            $this->arResult['PRIZES']['JS_DATA'] = $jsChartData;
        }
    }

    private function isRouleteActive()
    {
        global $USER;
        CModule::IncludeModule("wl.snailshop");
        $isRouleteForAdminsOnly = \COption::GetOptionString("wl.snailshop", "roulete_active_to_admins_only", "01.01.2022");
        if($isRouleteForAdminsOnly == "Y" && !\WL\SnailShop::userIsStaff()) {
            return false;
        }

        $isRouleteActiveByDate = false;
        $rouleteActiveFromDate = \COption::GetOptionString("wl.snailshop", "roulete_date_active_from", "01.01.2022");
        $rouleteActiveToDate = \COption::GetOptionString("wl.snailshop", "roulete_date_active_to", "01.01.2022");
        $today = strtotime(date('d.m.Y'));

        if($today <= strtotime($rouleteActiveToDate) && $today >= strtotime($rouleteActiveFromDate)) {
            $isRouleteActiveByDate = true;
        }

        $isPrizesEnough = false;

        if ($isRouleteActiveByDate) {
            CModule::IncludeModule("iblock");
            $dbPrizes = \CIBlockElement::GetList([], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y']);
            while ($obPrize = $dbPrizes->GetNextElement()) {
                $arPrize = [];
                $arPrize['PROPERTIES'] = $obPrize->GetProperties();

                if(!is_array($arPrize['PROPERTIES']['WINNERS']['VALUE'])) {
                    $arPrize['PROPERTIES']['WINNERS']['VALUE'] = [];
                }

                if (count($arPrize['PROPERTIES']['WINNERS']['VALUE']) < $arPrize['PROPERTIES']['COUNT_PRODUCTS']['VALUE']) {
                    $isPrizesEnough = true;
                }
            }
        }
        // dump([$isRouleteActiveByDate, $this->arParams['USER_ID'] > 0, $isPrizesEnough]);

        return ($isRouleteActiveByDate && $this->arParams['USER_ID'] > 0 && $isPrizesEnough);
    }

    private function getPrizes()
    {
        CModule::IncludeModule("iblock");

        $arPrizes = [];
        $dbPrizes = \CIBlockElement::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y']);
        while ($obPrize = $dbPrizes->GetNextElement()) {
            $arPrize = $obPrize->GetFields();
            $arPrize['PROPERTIES'] = $obPrize->GetProperties();
            $arPrizes[$arPrize['ID']] = $arPrize;
        }

        return $arPrizes;
    }

    private function getAllPrizes() {
        CModule::IncludeModule("iblock");

        $arPrizes = [];
        $dbPrizes = \CIBlockElement::GetList(['SORT' => 'ASC'], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID']]);
        while ($obPrize = $dbPrizes->GetNextElement()) {
            $arPrize = $obPrize->GetFields();
            $arPrize['PROPERTIES'] = $obPrize->GetProperties();
            $arPrizes[$arPrize['ID']] = $arPrize;
        }

        return $arPrizes;
    }

    private function getUserOrders($userId, $dateFrom)
    {
        CModule::IncludeModule("sale");

        $arUserOrders = [];
        $dbOrders = \CSaleOrder::GetList([], ['USER_ID' => $userId, '>=DATE_PAYED' => $dateFrom, 'PAYED' => 'Y', '>=SUM_PAID' => $this->sumFrom], false, false, ['ID']);
        while ($arOrder = $dbOrders->Fetch()) {
            $arUserOrders[] = $arOrder['ID'];
        }

        return $arUserOrders;
    }

    private function getAvailableOrder($arOrdersByUsers, $arTotalWinnersOrdersList)
    {
        $arAvaliableUserOrder = [];
        $arPaySystems = [];
        $dbPaySystems = \Bitrix\Sale\PaySystem\Manager::getList(array(
            'filter'  => ['ACTIVE' => 'Y'],
            'select' => ['ID', 'XML_ID'],
        ));
        while($arPaySystem = $dbPaySystems->fetch()) {
            $arPaySystems[$arPaySystem['ID']] = $arPaySystem;
        }

        foreach ($arOrdersByUsers as $userOrderId) {
            $order = \Bitrix\Sale\Order::load($userOrderId);
            $orderSum = 0.0;
            $paymentCollection = $order->getPaymentCollection();
            foreach ($paymentCollection as $payment) {
                if(!$payment->isPaid()) {
                    continue;
                }

                $paysystemId = $payment->getField('PAY_SYSTEM_ID');
                if($arPaySystems[$paysystemId]['XML_ID'] == "certificate" && $payment->getSum() < 3500) {
                    continue;
                }

                $orderSum += $payment->getSum();
            }

            // = (Количество возможных игр по конкретному заказу) - (количество сыгранных игр по конкретному заказу)
            $gamesCountAvailable = floor($orderSum / $this->sumFrom) - count(array_keys($arTotalWinnersOrdersList, $userOrderId));

            if ($gamesCountAvailable > 0) {
                $arAvaliableUserOrder = array_merge($arAvaliableUserOrder, array_fill(0, $gamesCountAvailable, $userOrderId));
            }
        }

        return $arAvaliableUserOrder;
    }

    private function chooseAndSavePrize()
    {
        $chosenPrize = [];
        $probabilitySum = 0;
        foreach ($this->arResult['PRIZES']['ITEMS'] as $key => $arPrize) {
            $this->arResult['PRIZES']['ITEMS'][$key]['PROBABILITY_FROM'] = $probabilitySum;
            $probabilitySum += intval($arPrize['PROPERTIES']['PROBABILITY']['VALUE']);
            $this->arResult['PRIZES']['ITEMS'][$key]['PROBABILITY_TO'] = $probabilitySum;
        }

        $prizeFound = false;
        while (!$prizeFound) {
            $rand = mt_rand(1, $probabilitySum);
            foreach ($this->arResult['PRIZES']['ITEMS'] as $arPrize) {
                if ($rand > $arPrize['PROBABILITY_FROM'] && $rand <= $arPrize['PROBABILITY_TO'] && intval($arPrize['PROPERTIES']['COUNT_PRODUCTS']['VALUE']) > 0) {
                    $chosenPrize = $arPrize;
                    $prizeFound = true;
                }
            }
        }

        if (intval($chosenPrize['ID']) > 0) {
            $arWinnerOrders = [];
            foreach ($chosenPrize['PROPERTIES']['WINNERS']['VALUE'] as $key => $value) {
                $arWinnerOrders[] = [
                    'VALUE' => $value,
                    'DESCRIPTION' => $chosenPrize['PROPERTIES']['WINNERS']['DESCRIPTION'][$key]
                ];
            }
            $arWinnerOrders[] = [
                'VALUE' => $this->arResult['ORDER_ID'],
                'DESCRIPTION' => date('d.m.Y H:i:s')
            ];

            \CIBlockElement::SetPropertyValuesEx(
                $chosenPrize['ID'],
                $this->arParams['IBLOCK_ID'],
                [
                    'WINNERS' => $arWinnerOrders,
                    'COUNT_PRODUCTS' => intval($chosenPrize['PROPERTIES']['COUNT_PRODUCTS']['VALUE']) - 1
                ]
            );

            return $chosenPrize;
        }

        return false;
    }

    /**
     * Actions
     */

    public function configureActions()
    {
        return [];
    }

    public function getRouleteResultAction()
    {
        $arMessage = [];
        if ($this->isRouleteActive()) {

            $this->prepareData();

            $ordersLeft = $this->arResult['ORDERS_TOTAL_COUNT'] - 1;

            if (!empty($this->arResult['ORDER_ID'])) {
                $arPrize = $this->chooseAndSavePrize();

                if (!empty($arPrize)) {
                    $arMessage['id'] = $arPrize['ID'];
                    $arMessage['orderId'] = $this->arResult['ORDER_ID'];
                    $arMessage['status'] = "win";
                    $arMessage['ordersLeft'] = $ordersLeft;
                    $arMessage['message'] = "Поздравляем!<br>Вы выиграли \"" . $arPrize['NAME'] . "\"";

                    if ($arMessage['ordersLeft'] > 0) {
                        $countDeclension = new Declension('раз', 'раза', 'раз');
                        $arMessage['ordersLeftText'] = "<small>Вы можете крутить еще " . $ordersLeft . " " . $countDeclension->get($ordersLeft) . ". Приз добавится к вашим выигранным призам!</small>";
                    }
                }
            } else {
                $arMessage['status'] = "no-orders";
                $arMessage['ordersLeft'] = $ordersLeft;
                $arMessage['message'] = "У вас не осталось заказов для игры";
            }
        } else {
            $arMessage['status'] = "roulete-is-over";
            $arMessage['message'] = "К сожалению, розыгрыш закончился";
        }

        \Bitrix\Main\Diag\Debug::dumpToFile($arMessage, '', 'roulete_log.txt');

        return $arMessage;
    }
}
