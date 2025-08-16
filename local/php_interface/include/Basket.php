<?php

namespace WL;

use Bitrix\Sale;

class Basket
{
    public static function countProductInBaskets($productID)
    {
        $countProduct = Sale\Internals\BasketTable::getList(
            [
                'filter' => [
                    'PRODUCT_ID' => $productID,
                    'ORDER_ID' => null,
                ],
                'select' => ['BASKET_COUNT'],
                'runtime' => [
                    new \Bitrix\Main\Entity\ExpressionField('BASKET_COUNT', 'COUNT(*)'),
                ],
            ]
        )->fetch();
        return $countProduct['BASKET_COUNT'];
    }
}