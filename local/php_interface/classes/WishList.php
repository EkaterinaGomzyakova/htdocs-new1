<?php

namespace WL;


use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use CIBlockElement;
use Exception;

/**
 * Класс работы с избранным
 * Class WishList
 */
class WishList
{
    /**
     * Роутер
     */
    public static function execute()
    {
        global $USER;
        $request = Application::getInstance()->getContext()->getRequest();
        $result['success'] = true;
        try {
            Loader::includeModule('iblock');
            if (!$USER->IsAuthorized()) {
                throw new Exception('Ошибка авторизации', 401);
            }
            if (!empty($request->get('action'))) {
                switch ($request->get('action')) {
                    case 'add':
                        self::add($request->get('id'));
                        break;
                    case 'remove':
                        self::remove($request->get('id'));
                        break;
                    case 'check':
                        $ids = $request->get('ids');
                        $result['wishlist_ids'] = self::checkItems($ids);
                        break;
                    case 'load':

                        break;
                }
            }
        } catch (Exception $exception) {
            $result['success'] = false;
            $result['error'] = $exception->getMessage();
            $result['error_code'] = $exception->getCode();
        }
        echo Json::encode($result);
    }

    /**
     * Список ID избранных товаров для текущего пользователя
     */
    public static function getListID($itemsID = false)
    {
        global $USER;
        $result = [];
        if($USER->IsAuthorized()){
            $filter = ['IBLOCK_ID' => FAVORITES_IBLOCK_ID, 'PROPERTY_USER_ID' => $USER->GetID()];
            if($itemsID != false){
                $filter['PROPERTY_PRODUCT_ID'] = $itemsID;
            }
            $rows = CIBlockElement::GetList([], $filter, false, false, ['ID', 'PROPERTY_PRODUCT_ID']);
            while ($row = $rows->fetch()) {
                $arProduct = CIBlockElement::GetList([], ['ID' => $row['PROPERTY_PRODUCT_ID_VALUE'], 'ACTIVE' => 'Y', 'IBLOCK_ID' => GOODS_IBLOCK_ID], false, false, ['ID'])->Fetch();
                if(is_array($arProduct) && $arProduct['ID'] > 0) {
                    $result[] = $row['PROPERTY_PRODUCT_ID_VALUE'];
                }
            }
        }

        return $result;
    }

    /**
     * Добавить в избранное
     * @param $productID - ID продукта
     * @throws Exception
     */
    private static function add($productID)
    {
        global $USER;
        if (empty($productID)) {
            throw new Exception('Не указан ID продукта');
        }

        $oldRow = self::checkItem($productID);
        if (!empty($oldRow)) {
            throw new Exception('Товар уже в избранном');
        }
        $el = new CIBlockElement();
        $r = $el->Add([
            'IBLOCK_ID' => FAVORITES_IBLOCK_ID,
            'NAME' => 'Элемент',
            'PROPERTY_VALUES' => [
                'PRODUCT_ID' => $productID,
                'USER_ID' => $USER->GetId()
            ]
        ]);
        if (!$r) {
            throw new Exception($el->LAST_ERROR);
        }
    }

    /**
     * Удалить из избранного
     * @param $productID - ID продукта
     * @throws Exception
     */
    private static function remove($productID)
    {
        if (empty($productID)) {
            throw new Exception('Не указан ID продукта');
        }
        $row = self::checkItem($productID);
        if (empty($row)) {
            throw new Exception('Товара нет в избранном');
        }
        CIBlockElement::Delete($row['ID']);
    }

    /**
     * Проверка на наличие продукта в избранном
     * @param $productID
     * @return array
     */
    public static function checkItem($productID)
    {
        global $USER;
        if($USER->IsAuthorized()){
            $result = CIBlockElement::GetList([], ['IBLOCK_ID' => FAVORITES_IBLOCK_ID, 'PROPERTY_PRODUCT_ID' => $productID, 'PROPERTY_USER_ID' => $USER->GetID()], false, false, ['ID'])->fetch();
        }else{
            $result = [];
        }
        return $result;
    }

    /**
     * Проверка нескольких товаров на наличие в избранном
     * @param array $productIDs
     * @return array
     */
    public static function checkItems($productIDs)
    {
        global $USER;
        $result = [];
        
        if($USER->IsAuthorized() && !empty($productIDs)){
            $ids = is_array($productIDs) ? $productIDs : explode(',', $productIDs);
            $filter = [
                'IBLOCK_ID' => FAVORITES_IBLOCK_ID, 
                'PROPERTY_PRODUCT_ID' => $ids, 
                'PROPERTY_USER_ID' => $USER->GetID()
            ];
            
            $rows = CIBlockElement::GetList([], $filter, false, false, ['ID', 'PROPERTY_PRODUCT_ID']);
            while ($row = $rows->fetch()) {
                $result[] = $row['PROPERTY_PRODUCT_ID_VALUE'];
            }
        }
        
        return $result;
    }
}