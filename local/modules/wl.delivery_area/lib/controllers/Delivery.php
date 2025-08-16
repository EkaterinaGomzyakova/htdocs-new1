<?php


namespace WLDeliveryArea\Controller;


use Bitrix\Main\Config\Option;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use Bitrix\Sale\Delivery\Services\Manager;
use Bitrix\Sale\Order;


class Delivery extends Controller
{

    function getMapAction(int $order_id, int $delivery_id)
    {
        Loader::includeModule('sale');
        $result = [];

        $delivery = Manager::getById($delivery_id);
        $result['status'] = false;
        $result['address'] = null;
        $result['zone'] = 'other';
        $result['properties_id'] = [
            'WL_DELIVERY_ADDRESS' => 0,
            'DELIVERY_AREA' => 0,
        ];
        if ($delivery['CLASS_NAME'] == '\Sale\Handlers\Delivery\AreaDeliveryHandler') {
            if($order_id > 0){
                $order = Order::load($order_id);
                $propertyCollection = $order->getPropertyCollection()->getArray();
                $properties = [];
                foreach ($propertyCollection['properties'] as $prop) {
                    $properties[$prop['CODE']] = $prop;
                }
                $result['address'] = $properties['WL_DELIVERY_ADDRESS']['VALUE'][0];
                if(empty($result['address'])){
                    $result['address'] = $properties['ADDRESS']['VALUE'][0];
                }
                $result['zone'] = $properties['DELIVERY_AREA']['VALUE'][0];
            }else{
                $rows = \Bitrix\Sale\Property::getList(['filter' => ['CODE' => array_keys($result['properties_id'])]]);
                while ($property = $rows->fetch())
                {
                    $result['properties_id'][$property['CODE']] = $property['ID'];
                }
            }



            $result['coordinates'] = Json::decode(Option::get('wl.delivery_area', 'yandex_coordinates'));
            $result['status'] = true;
        }
        return $result;
    }

    function updateOrderAction(int $order_id, string $address, string $zone){
        $order = Order::load($order_id);
        $propertyCollection = $order->getPropertyCollection();
        foreach ($propertyCollection as $property){
            $arProperty = $property->toArray();
            if($arProperty['CODE'] == 'WL_DELIVERY_ADDRESS'){
                $property->setValue($address);
            }
            if($arProperty['CODE'] == 'DELIVERY_AREA'){
                $property->setValue($zone);
            }
        }
        $order->save();
        return [];
    }
}