<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use CIBlockElement;
use Exception;

/**
 * Класс работы с избранным
 * Class WishList
 */
class WishList extends CBitrixComponent
{
    /**
     * Роутер
     */
    public function executeComponent()
    {
        global $APPLICATION;
        global $USER;
        $request = Application::getInstance()->getContext()->getRequest();
        if(!empty($request->get('action')) && $request->get('component') == 'wishlist'){
            $result['success'] = true;
            try {
                Loader::includeModule('iblock');
                if (!$USER->IsAuthorized()) {
                    throw new Exception('Ошибка авторизации', 401);
                }
                switch ($request->get('action')) {
                    case 'add':
                        self::add($request->get('id'));
                        $result['count_items'] = $this->getCountItems();
                        $result['text'] = 'Товар добавлен в <a href="/personal/wishlist/" target="blank">Избранное</a>';
                        break;
                    case 'remove':
                        self::remove($request->get('id'));
                        $result['count_items'] = $this->getCountItems();
                        $result['text'] = 'Товар удален из <a href="/personal/wishlist/" target="blank">Избранного</a>';
                        break;
                    case 'check':
                        $ids = $request->get('ids');
                        $result['wishlist_ids'] = \WL\WishList::checkItems($ids);
                        break;
                }
            } catch (Exception $exception) {
                $result['success'] = false;
                $result['error'] = $exception->getMessage();
                $result['error_code'] = $exception->getCode();
            }

            if($request->get('mode') == 'ajax'){
                $APPLICATION->RestartBuffer();
                echo Json::encode($result);
                die();
            }
        }

        $this->arResult['COUNT_ITEMS'] = $this->getCountItems();
        $this->includeComponentTemplate();
    }

    function getCountItems(){
        global $USER;
        if(!$USER->isAuthorized()) {
            return 0;
        } else {
            $rows = CIBlockElement::GetList([], ['IBLOCK_ID' => FAVORITES_IBLOCK_ID, 'PROPERTY_USER_ID' => $USER->GetID()], false, false, ['ID', 'PROPERTY_PRODUCT_ID']);

            $arProducts = [];
            while($arFav = $rows->Fetch()) {
                $arProduct = CIBlockElement::GetList([], ['IBLOCK_ID' => GOODS_IBLOCK_ID, 'ACTIVE' => 'Y', 'ID' => $arFav['PROPERTY_PRODUCT_ID_VALUE']], false, false, ['ID'])->Fetch();

                if(is_array($arProduct) && $arProduct['ID'] > 0) {
                    $arProducts[] = $arProduct;
                }
            }

            return count($arProducts);
        }
    }

    /**
     * Список ID избранных товаров для текущего пользователя
     */
    public static function getListID($itemsID){
        global $USER;
        $result = [];
        $rows = CIBlockElement::GetList([], ['IBLOCK_ID' => FAVORITES_IBLOCK_ID, 'PROPERTY_PRODUCT_ID' => $itemsID, 'PROPERTY_USER_ID' => $USER->GetID()], false, false, ['ID', 'PROPERTY_PRODUCT_ID']);
        while ($row = $rows->fetch()){
            $result[] = $row['PROPERTY_PRODUCT_ID_VALUE'];
        }
        return $result;
    }

    /**
     * Добавить в избранное
     * @param $productID - ID продукта
     * @throws Exception
     */
    function add($productID)
    {
        global $USER;
        if (empty($productID)) {
            throw new Exception('Не указан ID продукта');
        }

        $oldRow = $this->checkItem($productID);
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
        if(!$r){
            throw new Exception($el->LAST_ERROR);
        }
    }

    /**
     * Удалить из избранного
     * @param $productID - ID продукта
     * @throws Exception
     */
    function remove($productID)
    {
        if (empty($productID)) {
            throw new Exception('Не указан ID продукта');
        }
        $row = $this->checkItem($productID);
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
    function checkItem($productID)
    {
        global $USER;
        return CIBlockElement::GetList([], ['IBLOCK_ID' => FAVORITES_IBLOCK_ID, 'PROPERTY_PRODUCT_ID' => $productID, 'PROPERTY_USER_ID' => $USER->GetID()], false, false, ['ID'])->fetch();
    }
}