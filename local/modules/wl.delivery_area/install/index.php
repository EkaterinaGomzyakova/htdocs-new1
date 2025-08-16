<?php

use Bitrix\Main\Loader;

class wl_delivery_area extends CModule
{

    var $MODULE_ID = "wl.delivery_area";
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $errors;

    function __construct()
    {
        $this->MODULE_VERSION = "1.0.2";
        $this->MODULE_VERSION_DATE = "2022-05-16";
        $this->MODULE_NAME = "WL. Зоны доставки";
        $this->MODULE_DESCRIPTION = "WL. Зоны доставки";
    }

    function DoInstall(): bool
    {
        $this->InstallEvents();
        $this->installProperties();
        RegisterModule($this->MODULE_ID);
        return true;
    }

    function DoUninstall(): bool
    {
        $this->UnInstallEvents();
        UnRegisterModule($this->MODULE_ID);
        return true;
    }

    function InstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler('sale', 'OnSaleComponentOrderResultPrepared', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'OnSaleComponentOrderResultPrepared');
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler('sale', 'OnSaleOrderBeforeSaved', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'OnSaleOrderBeforeSaved');
        \Bitrix\Main\EventManager::getInstance()->registerEventHandler('sale', 'onSaleDeliveryHandlersClassNamesBuildList', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'onSaleDeliveryHandlersClassNamesBuildList');
    }

    function UnInstallEvents()
    {
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleComponentOrderResultPrepared', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'OnSaleComponentOrderResultPrepared');
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('sale', 'OnSaleOrderBeforeSaved', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'OnSaleOrderBeforeSaved');
        \Bitrix\Main\EventManager::getInstance()->unRegisterEventHandler('sale', 'onSaleDeliveryHandlersClassNamesBuildList', $this->MODULE_ID, '\\WLDeliveryArea\\Handlers', 'onSaleDeliveryHandlersClassNamesBuildList');
    }

    function installProperties(){
        Loader::includeModule('sale');
        $row = CSaleOrderProps::GetList([], ['CODE' => 'WL_DELIVERY_ADDRESS'])->fetch();
        $arFields = [
            'PERSON_TYPE_ID' => 1,
            'NAME' => 'Адрес доставки по Липецку',
            'TYPE' => 'TEXT',
            'REQUIED' => 'N',
            'USER_PROPS' => 'Y',
            'IS_LOCATION' => 'N',
            'PROPS_GROUP_ID' => 2,
            'IS_EMAIL' => 'N',
            'IS_PROFILE_NAME' => 'N',
            'IS_PAYER' => 'N',
            'IS_LOCATION4TAX' => 'N',
            'CODE' => 'WL_DELIVERY_ADDRESS',
            'IS_FILTERED' => 'N',
            'IS_ZIP' => 'N',
            'UTIL' => 'Y',
        ];
        if (empty($row)) {
            CSaleOrderProps::Add($arFields);
        } else {
            CSaleOrderProps::Update($row['ID'], $arFields);
        }

        $deliveryArea = CSaleOrderProps::GetList([], ['CODE' => 'DELIVERY_AREA'])->fetch();
        $arFields = [
            'PERSON_TYPE_ID' => 1,
            'NAME' => 'Зона доставки',
            'TYPE' => 'SELECT',
            'REQUIED' => 'N',
            'USER_PROPS' => 'Y',
            'IS_LOCATION' => 'N',
            'PROPS_GROUP_ID' => 2,
            'IS_EMAIL' => 'N',
            'IS_PROFILE_NAME' => 'N',
            'IS_PAYER' => 'N',
            'IS_LOCATION4TAX' => 'N',
            'CODE' => 'DELIVERY_AREA',
            'IS_FILTERED' => 'N',
            'IS_ZIP' => 'N',
            'UTIL' => 'Y',
        ];
        if (empty($deliveryArea)) {
            $deliveryArea['ID']  = CSaleOrderProps::Add($arFields);
        } else {
            CSaleOrderProps::Update($deliveryArea['ID'], $arFields);
        }

        $row = CSaleOrderPropsVariant::GetList([], ['ORDER_PROPS_ID' => $deliveryArea['ID'], 'VALUE' => 'other'])->fetch();
        if(empty($row)){
            $arFieldsV = array(
                "ORDER_PROPS_ID" => $deliveryArea['ID'],
                "VALUE" => "other",
                "NAME" => "Другое",
                "SORT" => 100,
            );
            CSaleOrderPropsVariant::Add($arFieldsV);
        }

        $row = CSaleOrderPropsVariant::GetList([], ['ORDER_PROPS_ID' => $deliveryArea['ID'], 'VALUE' => 'lipetsk'])->fetch();
        if(empty($row)){
            $arFieldsV = array(
                "ORDER_PROPS_ID" => $deliveryArea['ID'],
                "VALUE" => "lipetsk",
                "NAME" => "Липецк",
                "SORT" => 100,
            );
            CSaleOrderPropsVariant::Add($arFieldsV);
        }
    }
}