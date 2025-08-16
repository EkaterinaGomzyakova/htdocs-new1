<?

use Bitrix\Main; 
use Bitrix\Sale;

Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderSaved',
    ['FixShipmentsClass', 'fixShipments']
);

class FixShipmentsClass
{
    protected static $isShipmentAlreadyFixed = false;

    public static function fixShipments(Main\Event $event)
    {
        $order = $event->getParameter("ENTITY");
        $isNew = $event->getParameter("IS_NEW");

        if(!$isNew && !self::$isShipmentAlreadyFixed)
        {
            $shipmentCollection = $order->getShipmentCollection()->getNotSystemItems();
            $basket = Sale\Basket::loadItemsForOrder($order);
            $basketItems = $basket->getBasketItems();

            $basketCount = 0;
            foreach($basketItems as $item) {
                $basketCount += $item->getField("QUANTITY");
            }
        
            $shipmentCount = 0;
            foreach($shipmentCollection as $shipment) {      
                $shipmentItemCollection = $shipment->getShipmentItemCollection();
                foreach($shipmentItemCollection as $item) {
                    $shipmentCount += $item->getField("QUANTITY");
                }
            }
        
            if($shipmentCount == $basketCount) {
                return;
            }

            $isDeducted = false;
            $firstShipment = false;
            $shipmentItemCollection = false;
            foreach ($shipmentCollection as $shipment) {
                $firstShipment = $shipment;
                $isDeducted = ($firstShipment->getField('DEDUCTED') == "Y");
                if($isDeducted) {
                    $firstShipment->setField('DEDUCTED', 'N');
                }
                self::$isShipmentAlreadyFixed = true;

                $shipmentItemCollection = $firstShipment->getShipmentItemCollection();
                foreach($shipmentItemCollection as $shipmentItem) {
                    $shipmentItem->delete();
                }
            }

            if($shipmentItemCollection) {
                \CModule::IncludeModule('wl.snailshop');

                $userStoreId = WL\SnailShop::getUserStoreId();
                $orderBasket = $basket->getOrderableItems();
                
                foreach($orderBasket as $basketItem) {
                    $shipmentItem = $shipmentItemCollection->createItem($basketItem);
                    $shipmentItem->setQuantity($basketItem->getQuantity());

                    $storeItemCollection = $shipmentItem->getShipmentItemStoreCollection();
                    $storeItem = $storeItemCollection->createItem($basketItem);
                    $quantity = $basketItem->getField('QUANTITY');
                    $storeItem->setFields(['STORE_ID' => $userStoreId, 'QUANTITY' => $quantity]);
                }
            }
            
            if($firstShipment && $isDeducted) {
                $firstShipment->setField('DEDUCTED', 'Y');
            }
            
            self::$isShipmentAlreadyFixed = true;
            $order->save();
        }
    }
}
