<?php

namespace Clanbeauty;

use Bitrix\Main\Loader;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Order;
use CBitrixComponent;
use CCatalogProduct;
use CFile;
use CIBlockElement;

class ShoppingListComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        global $USER;
        Loader::includeModule('iblock');
        Loader::includeModule('sale');
        Loader::includeModule('product');

        if (!$USER->IsAuthorized()) {
            ShowError('Для доступа к списку покупок требуется авторизация');
        }

        $orders = Order::getList([
            'filter' => ['USER_ID' => $USER->GetID(), 'STATUS_ID' => 'F'],
            'select' => ['ID', 'DATE_INSERT'],
            'order' => ['ID' => 'DESC']
        ])->fetchAll();

        $ordersID = array_column($orders, 'ID');
        $basketItems = Basket::getList([
            'select' => ['PRODUCT_ID', 'ORDER_ID'],
            'filter' => ['ORDER_ID' => $ordersID],
            'order' => ['ID' => 'DESC']
        ])->fetchAll();

        $this->arResult['PRODUCTS_ID'] = array_unique(array_column($basketItems, 'PRODUCT_ID'));

        if (empty($this->arResult['PRODUCTS_ID'])) {
            $this->arResult['PRODUCTS_ID'] = false;
        }

        $this->includeComponentTemplate();
    }
}
