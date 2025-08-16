<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) {
    die();
}

use \Bitrix\Main\Localization\Loc;
use WL\SnailShop;

CModule::IncludeModule("wl.snailshop");

if ($arResult['CALLER'] == 'order_edit') {
    $arResult['HEADERS'][] = [
        'id' => 'RESERVED',
        'content' => Loc::getMessage('PRODUCT_FIELD_RESERVE'),
        'default' => true,
    ];

    /**
     * Получаем склады
     */
    $arStores = [];

    $res = CCatalogStore::GetList(
        [],
        ['ACTIVE' => 'Y'],
        false,
        false,
        ['ID', 'TITLE', 'IS_DEFAULT']
    );
    while ($arFields = $res->Fetch()) {
        $arStores[$arFields['ID']] = $arFields;
    }

    $arIds = array_column($arResult['PRODUCTS'], 'ID');

    foreach ($arStores as $arStore) {
        $arResult['HEADERS'][] = [
            'id' => 'STORE' . $arStore['ID'],
            'content' => $arStore['TITLE'],
            'default' => true,
        ];

        foreach ($arResult['PRODUCTS'] as $p => $arProduct) {
            if ($arProduct['TYPE'] != 'S') {
                $arResult['PRODUCTS'][$p]['PRODUCT']['QUANTITY_RESERVED'] = 0;
                $arResult['PRODUCTS'][$p]['PRODUCT']['AMOUNT'][$arStore['ID']] = [
                    'IS_DEFAULT' => $arStore['IS_DEFAULT'],
                    'STORE_ID' => $arStore['ID'],
                    'QUANTITY' => 0,
                ];
            }

            if (!empty($arProduct['SKU_ITEMS'])) {
                foreach ($arProduct['SKU_ITEMS']['SKU_ELEMENTS'] as $s => $arSku) {
                    $arResult['PRODUCTS'][$p]['SKU_ITEMS']['SKU_ELEMENTS'][$s]['AMOUNT'][$arStore['ID']] = [
                        'IS_DEFAULT' => $arStore['IS_DEFAULT'],
                        'STORE_ID' => $arStore['ID'],
                        'QUANTITY' => 0,
                    ];
                }

                $arIds = array_merge($arIds, $arProduct['SKU_ITEMS']['SKU_ELEMENTS_ID']);
            }
        }
    }

    /**
     * Получаем остатки по складам
     */
    $res = CCatalogStoreProduct::GetList(
        ["STORE_ID" => "ASC"],
        ["PRODUCT_ID" => $arIds],
        false,
        false,
        ['ID', 'PRODUCT_ID', 'STORE_ID', 'AMOUNT']
    );
    while ($arFields = $res->Fetch()) {
        foreach ($arResult['PRODUCTS'] as $p => $arProduct) {
            if ($arProduct['TYPE'] != 'S') {
                if ($arProduct['ID'] == $arFields['PRODUCT_ID']) {
                    $arResult['PRODUCTS'][$p]['PRODUCT']['AMOUNT'][$arFields['STORE_ID']]['QUANTITY'] = $arFields['AMOUNT'];
                }
                if (!empty($arProduct['SKU_ITEMS'])) {
                    foreach ($arProduct['SKU_ITEMS']['SKU_ELEMENTS'] as $s => $arSku) {
                        if ($arSku['ID'] == $arFields['PRODUCT_ID']) {
                            $arResult['PRODUCTS'][$p]['SKU_ITEMS']['SKU_ELEMENTS'][$s]['AMOUNT'][$arFields['STORE_ID']]['QUANTITY'] = $arFields['AMOUNT'];
                        }
                    }
                }
            }
        }
    }

    /**
     * Получаем резервы по складам
     */
    $res = CCatalogProduct::GetList([],
        [
            'ID' => $arIds
        ],
        false,
        false,
        ['ID', 'QUANTITY_RESERVED', 'QUANTITY']
    );
    while ($arFields = $res->Fetch()) {
        foreach ($arResult['PRODUCTS'] as $p => $arProduct) {
            if ($arProduct['TYPE'] != 'S') {
                if ($arFields['ID'] == $arProduct['ID']) {
                    $arResult['PRODUCTS'][$p]['PRODUCT']['QUANTITY_RESERVED'] = $arFields['QUANTITY_RESERVED'];
                }

                if (!empty($arProduct['SKU_ITEMS'])) {
                    foreach ($arProduct['SKU_ITEMS']['SKU_ELEMENTS'] as $s => $arSku) {
                        if ($arSku['ID'] == $arFields['ID']) {
                            $arResult['PRODUCTS'][$p]['SKU_ITEMS']['SKU_ELEMENTS'][$s]['QUANTITY_RESERVED'] = $arFields['QUANTITY_RESERVED'];
                        }
                    }
                }
            }
        }
    }

    $arResult['USER_STORE_ID'] = SnailShop::getUserStoreId();
}