<?php


namespace WL\Handlers;


use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Event;
use CEventLog;
use Exception;
use WL\HistoryPrice;

class Catalog
{
    /**
     * Событие изменения цены продукта
     * @param \Bitrix\Main\Event $event
     */
    public static function onPriceUpdate(Event $event)
    {
        $id = $event->getParameter('id');
        try {
            $price = PriceTable::getList(['filter' => ['ID' => $id['ID']]])->fetch();
            if(!empty($price)) {
                HistoryPrice::add($price['PRODUCT_ID'], $price['PRICE'], $price['CATALOG_GROUP_ID'], $id['ID']);
            }
        } catch (Exception $exception) {
            CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => 'WL',
                'MODULE_ID' => '',
                'ITEM_ID' => $id['ID'],
                'DESCRIPTION' => $exception->getMessage(),
            ]);
        }
    }

    public static function onProductUpdate(Event $event)
    {
        $id = $event->getParameter('id');
        try {
            $product = ProductTable::getList([
                'filter' => ['ID' => $id]
            ])->fetch();
            if ($product) {
                HistoryPrice::add($product['ID'], $product['PURCHASING_PRICE'] ?? 0, 0);
            }
        } catch (Exception $exception) {
            CEventLog::Add([
                'SEVERITY' => 'ERROR',
                'AUDIT_TYPE_ID' => 'WL',
                'MODULE_ID' => '',
                'ITEM_ID' => $id['ID'],
                'DESCRIPTION' => $exception->getMessage(),
            ]);
        }
    }

    public static function sendOnSubscribeSubmit(&$event, &$lid, &$arFields, &$message_id, &$files) {
        \CModule::IncludeModule('catalog');
        \CModule::IncludeModule('wl.snailshop');

        if (
            !isset($arFields['EVENT_NAME']) || $arFields['EVENT_NAME'] != 'CATALOG_PRODUCT_SUBSCRIBE_NOTIFY' ||
            !isset($arFields['PRODUCT_ID']) || $arFields['PRODUCT_ID'] <= 0
        ) {
            return true;
        }

        $arSelect = ['ID', 'AMOUNT'];
        $arFilter = [
            'PRODUCT_ID' => $arFields['PRODUCT_ID'],
            'STORE_ID' => \WL\Snailshop::getDefaultStore()
        ];

        $arStoreProduct = \CCatalogStoreProduct::GetList([], $arFilter, false, false, $arSelect)->Fetch();

        if (!empty($arStoreProduct) && $arStoreProduct["AMOUNT"] <= 0) {
            return false;
        }

        return true;
    }

    public static function StopPriceRename($ID, &$arFields)
    {
    return false;// Предотвращаем изменение кода типа цен
    }

}
