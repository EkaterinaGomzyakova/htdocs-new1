<?php

namespace Sale\Handlers\Delivery;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;
use Bitrix\Sale\Delivery\CalculationResult;
use Bitrix\Sale\Delivery\Services\Base;
use Bitrix\Sale\Shipment;

Loader::includeModule('wl.delivery_area');

class AreaDeliveryHandler extends Base
{
    public static function getClassTitle()
    {
        return 'WL. Доставка курьером';
    }

    public static function getClassDescription()
    {
        return 'WL. Доставка курьером';
    }

    public static function whetherAdminExtraServicesShow()
    {
        return true;
    }

    public function isCalculatePriceImmediately()
    {
        return true;
    }

    protected function calculateConcrete(Shipment $shipment)
    {
        global $APPLICATION;
        $result = new CalculationResult();
        $order = $shipment->getOrder();
        $propertyCollection = $order->getPropertyCollection()->getArray();
        $properties = [];
        foreach ($propertyCollection['properties'] as $prop) {
            $properties[$prop['CODE']] = $prop;
        }
        if(!Context::getCurrent()->getRequest()->isAdminSection()){
            ob_start();
            $APPLICATION->IncludeComponent('wl:delivery_area', '', [
                'PROPERTY_DELIVERY_ZONE' => $properties['DELIVERY_AREA'],
                'PROPERTY_DELIVERY_ADDRESS' => $properties[Option::get('wl.delivery_area', 'property_address_delivery', 'WL_DELIVERY_ADDRESS')],
                'SHOW_TEMPLATE' => true
            ], false);
            $html = ob_get_contents();
            ob_clean();
            $result->setDescription($html);
        }

        if(empty($properties['DELIVERY_AREA']['VALUE'][0])){
            $result->setDeliveryPrice(Option::get('wl.delivery_area', 'price_in_area'));
        }else{
            if($properties['DELIVERY_AREA']['VALUE'][0] == 'lipetsk'){
                $result->setDeliveryPrice(Option::get('wl.delivery_area', 'price_in_area'));
            }else{
                $result->setDeliveryPrice(Option::get('wl.delivery_area', 'price_out_area'));
            }
        }

        return $result;
    }
}