<?php


namespace WL;


use Bitrix\Catalog\PriceTable;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\ArgumentException as ArgumentExceptionAlias;

class HistoryPrice
{
    /**
     * Добавление записи в историю
     * @param int $productID - ID товара
     * @param float $price - цена
     * @param int|null $priceTypeID - ID типа цен
     * @param int|null $priceID - ID записи в таблице цен
     * @throws ArgumentExceptionAlias
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function add(int $productID, float $price = 0, ?int $priceTypeID = null, ?int $priceID = null)
    {
        $item = HL::table('HistoryPrice')
            ->filter(['UF_PRODUCT_ID' => $productID, 'UF_PRICE_TYPE' => $priceTypeID])
            ->sort(['ID' => 'DESC'])
            ->get();

        if(empty($item) || $item['UF_PRICE'] != $price){
            HL::table('HistoryPrice')->add([
                'UF_PRODUCT_ID' => $productID,
                'UF_PRICE' => $price,
                'UF_PRICE_ID' => $priceID,
                'UF_PRICE_TYPE' => $priceTypeID,
                'UF_TIMESTAMP' => date('d.m.Y H:i'),
            ]);
        }
    }

    /**
     * Функция обновления истории цен
     */
    public static function updateAllPrice(){
        $prices = PriceTable::getList()->fetchAll();
        foreach ($prices as $price){
            self::add($price['PRODUCT_ID'], $price['PRICE'], $price['CATALOG_GROUP_ID'], $price['ID']);
        }
    }

    /**
     * Функция обновления истории закупочных цен
     */
    public static function updatePurchaseAllPrice(){
        $products = ProductTable::getList()->fetchAll();
        foreach ($products as $product){
            self::add($product['ID'], $product['PURCHASING_PRICE'] ?? 0, 0);
        }
    }
}