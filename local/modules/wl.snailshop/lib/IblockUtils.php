<?php


namespace WL;


use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use CIBlock;
use CIBlockProperty;
use Exception;

class IblockUtils
{
    private static $_instance = null;

    public $iblocks = [];
    public $properties = [];

    const CACHE_ID = 'wl_iblock_utils';
    const CACHE_ID_PROPERTIES = 'wl_utils_iblock_default_properties';

    private function __construct()
    {
    }

    public static function getInstance(): ?IblockUtils
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function import()
    {

    }

    public function get()
    {

    }

    /**
     * @throws \Bitrix\Main\LoaderException
     */
    public static function getIdByCode(string $code)
    {
        $iblock = self::getByCode($code);
        return $iblock['ID'];
    }

    /**
     * Получить инфоблок по коду
     * @param string $code
     * @return array
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getByCode(string $code): array
    {
        $instance = self::getInstance();
        if(!isset($instance->iblocks[$code])){
            $cache = \Bitrix\Main\Application::getInstance()->getManagedCache();
            if ($cache->read(864000, self::CACHE_ID)) {
                $instance->iblocks = $cache->get(self::CACHE_ID);
            }

            if(!isset($instance->iblocks[$code])){
                if (!Loader::includeModule('iblock')) {
                    throw new Exception(Loc::getMessage("ERROR_MODULE_IBLOCK_NOT_FOUND"));
                }
                $rows = CIBlock::GetList([], ['CHECK_PERMISSIONS' => 'N']);
                while ($row = $rows->fetch()){
                    if($row['CODE']){
                        $instance->iblocks[$row['CODE']] = $row;
                    }
                }
                $cache->set(self::CACHE_ID, $instance->iblocks);
            }
        }
        return $instance->iblocks[$code];
    }

    public static function getByID($id){
        $instance = self::getInstance();
        if(empty($instance->iblocks)){
            $cache = \Bitrix\Main\Application::getInstance()->getManagedCache();
            $instance->iblocks = $cache->get(self::CACHE_ID);
        }

        if(empty($instance->iblocks)){
            $rows = CIBlock::GetList();
            while ($row = $rows->fetch()){
                $instance->iblocks[$row['CODE']] = $row;
            }
            $cache->set(self::CACHE_ID, $instance->iblocks);
        }

        foreach ($instance->iblocks as $iblock){
            if($id == $iblock['ID']){
                return $iblock;
            }
        }

        return false;
    }

    /**
     * @throws \Bitrix\Main\LoaderException
     * @throws \Exception
     */
    public static function getDefaultProperties($iblockID)
    {
        $instance = self::getInstance();
        if (!isset($instance->properties[$iblockID])) {
            $cache = Application::getInstance()->getManagedCache();
            if ($cache->read(86400, self::CACHE_ID_PROPERTIES)) {
                $instance->properties = $cache->get(self::CACHE_ID_PROPERTIES);
            }
            if (!isset($instance->properties[$iblockID])) {
                if (!Loader::includeModule('iblock')) {
                    throw new Exception(Loc::getMessage("ERROR_MODULE_IBLOCK_NOT_FOUND"));
                }
                $rows = CIBlockProperty::GetList([], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $iblockID]);
                while ($row = $rows->fetch()) {
                    if (!empty($row['DEFAULT_VALUE'])) {
                        $row['VALUE'] = $row['DEFAULT_VALUE'];
                        $row['~VALUE'] = $row['DEFAULT_VALUE'];
                    }

                    if ($row['PROPERTY_TYPE'] == 'L') {
                        $enums = Enums::getIblockEnums($iblockID, $row['CODE']);
                        foreach ($enums as $enum) {
                            if ($enum['DEF'] == 'Y') {
                                $row['VALUE'] = $enum['VALUE'];
                                $row['VALUE_ENUM'] = $enum['VALUE'];
                                $row['VALUE_XML_ID'] = $enum['XML_ID'];
                                $row['VALUE_ENUM_ID'] = $enum['ID'];
                            }
                        }
                    }

                    $instance->properties[$iblockID][$row['CODE']] = $row;
                }
                $cache->set(self::CACHE_ID_PROPERTIES, $instance->properties);
            }
        }

        return $instance->properties[$iblockID];
    }

    protected function __clone()
    {
    }
}