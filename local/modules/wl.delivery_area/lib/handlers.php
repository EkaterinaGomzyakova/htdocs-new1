<?php

namespace WLDeliveryArea;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Main\Page\Asset;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\ResultError;

class Handlers
{
    public static function OnSaleComponentOrderResultPrepared()
    {
        global $APPLICATION;
        $APPLICATION->IncludeComponent('wl:delivery_area', '', [
            'SHOW_TEMPLATE' => false
        ], false);
    }

    public static function OnSaleOrderBeforeSaved(Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $deliveryIds = $order->getDeliverySystemId();
        $propertyCollection = $order->getPropertyCollection()->getArray();
        $properties = [];
        foreach ($propertyCollection['properties'] as $prop) {
            $properties[$prop['CODE']] = $prop;
        }
        foreach ($deliveryIds as $deliveryId) {
            if ($deliveryId > 0) {
                $service = Manager::getById($deliveryId);
                if ($service['CLASS_NAME'] == '\Sale\Handlers\Delivery\AreaDeliveryHandler') {
                    if (empty($properties['DELIVERY_AREA']['VALUE'][0]) || empty($properties[Option::get('wl.delivery_area', 'property_address_delivery', 'WL_DELIVERY_ADDRESS')]['VALUE'][0])) {
                        return new EventResult(EventResult::ERROR, new ResultError('Необходимо указать адрес доставки'), 'sale');
                    }
                }
            }
        }
    }

    public static function onSaleDeliveryHandlersClassNamesBuildList(){
        global $APPLICATION;
        if ($APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_edit.php'
            || $APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_shipment_edit.php'
            || $APPLICATION->GetCurPage() == '/bitrix/admin/sale_order_create.php'
        ) {
            Asset::getInstance()->addString('<link rel="stylesheet" type="text/css" href="/local/modules/wl.delivery_area/assets/css/admin_edit.css">', true);
            Asset::getInstance()->addJs('https://api-maps.yandex.ru/2.1/?apikey=' . Option::get('wl.delivery_area', 'yandex_api_key') . '&lang=ru_RU&suggest_apikey=' . Option::get('wl.delivery_area', 'yandex_suggests_api_key'));
            Asset::getInstance()->addJs('/local/modules/wl.delivery_area/assets/js/admin_edit.js');
        }
    }
}