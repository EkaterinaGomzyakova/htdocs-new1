<?php

namespace SnailShop\Controller;


use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\Controller;
use \Bitrix\Catalog\Model\Product;
use WL\SnailShop;


class Goods extends Controller
{
    public function configureActions(): array
    {
        return [
            'flushReserve' => ['prefilters' => [
                new Csrf(),
                new Authentication()
            ]],
            'getGadgetProductInfo' => ['prefilters' => [
                new Csrf(),
                new Authentication()
            ]],
            'saveGadgetProductInfo' => ['prefilters' => [
                new Csrf(),
                new Authentication()
            ]],
            'flushAllReserves' => ['prefilters' => [
                new Csrf(),
                new Authentication()
            ]],
        ];
    }

    public function flushReserveAction(int $productId)
    {
        \CModule::IncludeModule("wl.snailshop");

        if (!SnailShop::userIsStaff()) {
            throw new Exception('Доступ запрещен');
        }

        \CModule::IncludeModule('catalog');

        $arProduct = Product::getList([
            'filter' => ['ID' => $productId],
            'select' => ['QUANTITY', 'QUANTITY_RESERVED', 'TYPE'],
        ])->fetch();
        if (!$arProduct) {
            throw new \Exception('Товар с ID=' . $productId . ' не найден');
        }


        if ($arProduct['TYPE'] == 3) { //has SKU
            $dbChildProducts = \CIBlockElement::GetList([], ['PROPERTY_CML2_LINK' => $productId], false, false, ['ID', 'IBLOCK_ID']);
            while ($arChildProduct = $dbChildProducts->Fetch()) {
                $arProduct = Product::getList([
                    'filter' => ['ID' => $arChildProduct['ID']],
                    'select' => ['ID', 'QUANTITY', 'QUANTITY_RESERVED'],
                ])->fetch();

                $quantity = (int)$arProduct['QUANTITY'];
                $quantityReserved = (int)$arProduct['QUANTITY_RESERVED'];
                Product::Update($arChildProduct['ID'], [
                    'QUANTITY' => $quantity + $quantityReserved,
                    'QUANTITY_RESERVED' => 0
                ]);
            }
        } else {
            $quantity = (int)$arProduct['QUANTITY'];
            $quantityReserved = (int)$arProduct['QUANTITY_RESERVED'];
            Product::Update($productId, [
                'QUANTITY' => $quantity + $quantityReserved,
                'QUANTITY_RESERVED' => 0
            ]);
        }
    }

    public function getGadgetProductInfoAction(int $productId)
    {
        $currentUser = \Bitrix\Main\Engine\Controller::getCurrentUser();

        if (!$currentUser->isAdmin()) {
            throw new \Exception('Функция доступна только администраторам');
        }

        if ($productId > 0) {
            $arProductInfo = [];
            $arProductInfo['BASE'] = \CCatalogProduct::GetByID($productId);

            $arIblockElement = \CIBlockElement::GetByID($productId)->fetch();
            $arProductInfo['BASE']['NAME'] = $arIblockElement['NAME'];

            $dbStoreProduct = \CCatalogStoreProduct::GetList([], ['PRODUCT_ID' => $productId], false, false);
            while ($arStoreProduct = $dbStoreProduct->Fetch()) {
                $arProductInfo['STORES'][$arStoreProduct['STORE_ID']] = $arStoreProduct;
            }

            if (empty($arProductInfo['STORES'])) {
                throw new \Exception('Не удалось получить остатки!');
            }

            return $arProductInfo;
        } else {
            throw new \Exception('Не задан ID товара');
        }
    }

    public function saveGadgetProductInfoAction(int $productId, array $amount)
    {
        if ($productId > 0 && !empty($amount)) {
            $totalQuantity = 0;
            foreach ($amount as $storeId => $quantity) {
                $totalQuantity += $quantity;
                $arStoreProductRow = \CCatalogStoreProduct::GetList([], ['STORE_ID' => $storeId, 'PRODUCT_ID' => $productId], false, false, ['ID'])->Fetch();
                if (empty($arStoreProductRow)) {
                    throw new \Exception('Не удалось записать остатки!');
                }
                \CCatalogStoreProduct::Update($arStoreProductRow['ID'], ['AMOUNT' => $quantity, 'QUANTITY_RESERVED' => 0]);
            }

            $availableFlag = ($amount > 0) ? 'Y' : 'N';
            \CCatalogProduct::Update($productId, ['QUANTITY' => $totalQuantity, 'QUANTITY_RESERVED' => 0, 'AVAILABLE' => $availableFlag]);

            return true;
        } else {
            throw new \Exception('Не задан ID товара или информация об остатках!');
        }
    }

    public function flushAllReservesAction()
    {
        $currentUser = \Bitrix\Main\Engine\Controller::getCurrentUser();

        if (!$currentUser->isAdmin()) {
            throw new \Exception('Функция доступна только администраторам!');
        }

        \CModule::IncludeModule("catalog");
        $dbProducts = \CCatalogProduct::GetList([], [">QUANTITY_RESERVED" => "0"], false, false, ["ID", "QUANTITY", "QUANTITY_RESERVED"]);
        while ($arProduct = $dbProducts->Fetch()) {
            \CCatalogProduct::Update($arProduct['ID'], ["QUANTITY" => $arProduct["QUANTITY_RESERVED"] + $arProduct["QUANTITY"], "QUANTITY_RESERVED" => "0"]);
        }

        return true;
    }
}
