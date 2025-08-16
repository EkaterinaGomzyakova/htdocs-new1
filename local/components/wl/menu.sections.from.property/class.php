<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;
use CIBlockElement;
use Exception;


class MenuSectionsFromProperty extends CBitrixComponent
{

    public function executeComponent()
    {
        $this->arResult = [];
        $arPropVariants = [];
        $propertyCode = $this->arParams['PROPERTY_CODE'];


        $cache = Application::getInstance()->getManagedCache();
        $cacheId = md5($this->getName() . serialize([$this->arParams['IBLOCK_ID'], $this->arParams['PROPERTY_CODE']]));

        if ($cache->read($this->arParams['CACHE_TIME'], $cacheId)) {
            $this->arResult = $cache->get($cacheId);
        } else {

            $dbPropertyVariants = CIBlockPropertyEnum::GetList(['VALUE' => 'ASC'], ['CODE' => $propertyCode, $this->arParams['IBLOCK_ID']]);
            while ($arVariant = $dbPropertyVariants->Fetch()) {
                $arPropVariants[$arVariant['ID']] = $arVariant;
            }

            $dbItems = CIBlockElement::GetList(
                [],
                ['IBLOCK_ID' => $this->arParams['IBLOCK_ID'], 'ACTIVE' => 'Y', 'GLOBAL_ACTIVE' => 'Y', '!PROPERTY_' . $propertyCode => false],
                ['PROPERTY_' . $propertyCode],
                false,
                ['ID', 'IBLOCK_ID', $propertyCode]
            );
            while ($arItem = $dbItems->Fetch()) {
                $detailPageUrl = $this->arParams['URL_TEMPLATE'] . $arPropVariants[$arItem['PROPERTY_SCOPE_ENUM_ID']]['XML_ID'] . '/';
                $this->arResult[] = [
                    $arPropVariants[$arItem['PROPERTY_' . $propertyCode . '_ENUM_ID']]['VALUE'],
                    $detailPageUrl,
                    [],
                    [],
                    ''
                ];
            }
            $cache->set($cacheId, $this->arResult);
        }

        $this->includeComponentTemplate();
        return $this->arResult;
    }
}
