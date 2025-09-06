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
                        \WL\WishList::add($request->get('id'));
                        $result['count_items'] = $this->getCountItems();
                        $result['text'] = 'Товар добавлен в <a href="/personal/wishlist/" target="blank">Избранное</a>';
                        break;
                    case 'remove':
                        \WL\WishList::remove($request->get('id'));
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
            // Используем метод из класса WL\WishList для получения списка ID
            $wishlistIDs = \WL\WishList::getListID();
            return count($wishlistIDs);
        }
    }

}