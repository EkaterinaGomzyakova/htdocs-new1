<?php

namespace Clanbeauty;

use CModule;
use CBitrixComponent;

class RouleteForPublicEvents extends CBitrixComponent implements \Bitrix\Main\Engine\Contract\Controllerable
{
    public $arResult = [];

    public function executeComponent()
    {
        if ($this->isPrizesEnough()) {
            $this->prepareData();
            $this->includeComponentTemplate();
        } else {
            global $APPLICATION;
            $APPLICATION->RestartBuffer();
            $this->includeComponentTemplate('empty');
            die();
        }
    }

    protected function listKeysSignedParameters()
    {
        return [
            'IBLOCK_ID',
        ];
    }

    private function prepareData()
    {
        $arAvailablePrizes = $this->getPrizes();

        $this->arResult['ORDER_ID'] =  $this->arResult['USER_NAME'];
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

    private function isPrizesEnough()
    {
        $isPrizesEnough = false;

        CModule::IncludeModule("iblock");
        $dbPrizes = \CIBlockElement::GetList([], ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'ACTIVE_DATE' => 'Y']);
        while ($obPrize = $dbPrizes->GetNextElement()) {
            $arPrize = [];
            $arPrize['PROPERTIES'] = $obPrize->GetProperties();

            if (!is_array($arPrize['PROPERTIES']['WINNERS']['VALUE'])) {
                $arPrize['PROPERTIES']['WINNERS']['VALUE'] = [];
            }

            if (count($arPrize['PROPERTIES']['WINNERS']['VALUE']) < $arPrize['PROPERTIES']['COUNT_PRODUCTS']['VALUE']) {
                $isPrizesEnough = true;
            }
        }

        return $isPrizesEnough;
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

    public function getRouleteResultAction($userName)
    {
        $arMessage = [];
        if ($this->isPrizesEnough()) {
            $this->arResult['USER_NAME'] = $userName;

            $this->prepareData();

            if (!empty($this->arResult['ORDER_ID'])) {
                $arPrize = $this->chooseAndSavePrize();

                if (!empty($arPrize)) {
                    $arMessage['id'] = $arPrize['ID'];
                    $arMessage['orderId'] = $this->arResult['ORDER_ID'];
                    $arMessage['status'] = "win";
                    $arMessage['message'] = $this->arResult['USER_NAME'] . ", Вы выиграли \"" . $arPrize['NAME'] . "\"";
                    $arMessage['prizesLeft'] = 1;
                }
            }
        } else {
            $arMessage['status'] = "roulete-is-over";
            $arMessage['message'] = "К сожалению, розыгрыш закончился";
        }

        \Bitrix\Main\Diag\Debug::dumpToFile($arMessage, '', 'roulete__retail_log.txt');

        return $arMessage;
    }
}
