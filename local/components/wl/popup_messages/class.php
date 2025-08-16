<?php

use Bitrix\Main\Engine\Contract\Controllerable;
use Bitrix\Main\Errorable;
use Bitrix\Main\ErrorCollection;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class PopUpMessagesComponent extends CBitrixComponent implements Controllerable, Errorable
{
    const COOKIE_NAME = 'viewed_popup_msg';
    private $errorCollection;

    public function configureActions()
    {
        return [
            'markRead' => [
                'prefilters' => [],
            ]
        ];
    }

    public function onPrepareComponentParams($arParams)
    {
        $this->errorCollection = new ErrorCollection();
        return $arParams;
    }

    public function getErrors()
    {
        return $this->errorCollection->toArray();
    }

    public function getErrorByCode($code)
    {
        return $this->errorCollection->getErrorByCode($code);
    }

    public function executeComponent()
    {
        $this->arResult['VIEWED_POPUP_ID'] = $this->getViewedMsg();

        $arSelect = ['ID', 'IBLOCK_ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'PROPERTY_BUTTON'];
        $arSort = ['SORT' => 'ASC', 'ID' => 'ASC'];
        $arFilter = [
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            '!ID' => $this->arResult['VIEWED_POPUP_ID'],
            'IBLOCK_ID' => $this->arParams['IBLOCK_ID'],
        ];
        $dbItem = CIBlockElement::GetList($arSort, $arFilter, false, false, $arSelect);
        if($obItem = $dbItem->GetNextElement()) {
            $this->arResult['ITEM'] = $obItem->GetFields();
            $this->arResult['ITEM']['PROPERTIES'] = $obItem->GetProperties();

        }

        if ($this->arResult['ITEM']['PREVIEW_PICTURE'] > 0) {
            $this->arResult['ITEM']['PREVIEW_PICTURE'] = CFile::GetPath($this->arResult['ITEM']['PREVIEW_PICTURE']);
        }
        $this->includeComponentTemplate();
    }

    /**
     * Получить список прочтенных сообщений
     * @return array
     */
    function getViewedMsg(): array
    {
        $result = [];
        if ($_COOKIE[self::COOKIE_NAME]) {
            $result = unserialize($_COOKIE[self::COOKIE_NAME]);
        }
        return $result;
    }

    /**
     * Пометить сообщение как прочтенное
     * @param $id
     */
    function markReadAction($id)
    {
        $viewedMsg = $this->getViewedMsg();
        if (!in_array($id, $viewedMsg)) {
            $viewedMsg[] = $id;
            $cookieTime = time() + 60 * 60 * 24 * 365 * 10;
            $value = serialize($viewedMsg);
            $_COOKIE[self::COOKIE_NAME] = $value;
            setcookie(self::COOKIE_NAME, $value, $cookieTime, '/');
        }
    }
}