<?php

use Bitrix\Main\Application;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class SectionListFilterByPropertyComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        global $APPLICATION;
        $request = Application::getInstance()->getContext()->getRequest();

        $cache = Application::getInstance()->getManagedCache();
        $cacheId = md5($this->getName() . serialize([$this->arParams['PROPERTY_CODE'], $this->arParams['PROPERTY_VALUE']]));

        if ($cache->read($this->arParams['CACHE_TIME'], $cacheId)) {
            $this->arResult = $cache->get($cacheId);
        } else {
            $sectionsIDs = [];
            $rows = CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $this->arParams['CATALOG_IBLOCK_ID'], 'PROPERTY_' . $this->arParams['PROPERTY_CODE'] => $this->arParams['PROPERTY_VALUE'], 'ACTIVE' => 'Y'],
                ['IBLOCK_SECTION_ID'],
            );
            while ($row = $rows->fetch()) {
                $sectionsIDs[] = $row['IBLOCK_SECTION_ID'];
            }


            $this->arResult['ITEMS'] = [];
            $rows = CIBlockSection::GetList(
                $this->arParams['SORT'],
                ['IBLOCK_ID' => $this->arParams['CATALOG_IBLOCK_ID'], 'ID' => $sectionsIDs, 'ACTIVE' => 'Y'],
                false,
                ['ID', 'NAME']
            );
            $uri = new \Bitrix\Main\Web\Uri($request->getRequestUri());
            while ($row = $rows->fetch()) {
                $uri->addParams(['section' => $row['ID']]);
                $row['ACTIVE'] = false;
                $row['LINK'] = $uri->getUri();
                $this->arResult['ITEMS'][] = $row;
            }
            $cache->set($cacheId, $this->arResult);
        }

        foreach ($this->arResult['ITEMS'] as &$arItem) {
            if ($request['section'] == $arItem['ID']) {
                $arItem['LINK'] = $APPLICATION->GetCurPage();
                $arItem['ACTIVE'] = true;
            }
        }
        unset($arItem);

        if(isset($request['section']) && !empty($this->arParams['FILTER_NAME'])){
            $GLOBALS[$this->arParams['FILTER_NAME']]['SECTION_ID'] = $request['section'];
        }

        $this->includeComponentTemplate();
        return $this->arResult;
    }
}