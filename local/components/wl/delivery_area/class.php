<?php

use Bitrix\Main\Config\Option;
use Bitrix\Main\Page\Asset;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class DeliveryAreaComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        Asset::getInstance()->addJs('https://api-maps.yandex.ru/2.1/?apikey=' . Option::get('wl.delivery_area', 'yandex_api_key') . '&lang=ru_RU&suggest_apikey=' . Option::get('wl.delivery_area', 'yandex_suggests_api_key'));
        $this->includeComponentTemplate();
    }
}