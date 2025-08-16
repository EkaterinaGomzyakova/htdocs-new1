<?php


namespace WL;


use Bitrix\Main\Application;
use CIBlock;

class Iblock
{
    const CACHE_ID = 'iblock_tools_'.SITE_ID;
    private static $iblocks = [];

    private function __construct()
    {
    }

    /**
     * Получить ID инфоблока по коду
     * @param string $code
     * @return array
     */
    public static function getIblockIDByCode(string $code, string $type = ''): ?string
    {
        $iblock = self::getIblockByCode($code, $type);
        return $iblock['ID'];
    }

    /**
     * Получить инфоблок по коду
     * @param $code
     * @return mixed
     */
    public static function getIblockByCode(string $code, string $type = ''): ?array
    {
        $result = null;
        $iblocks = self::getIblocks();
        foreach ($iblocks as $iblock){
            if($iblock['CODE'] == $code){
                if(empty($type)){
                    $result = $iblock;
                }else{
                    if($type == $iblock['IBLOCK_TYPE_ID']){
                        $result = $iblock;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * Список всех инфоблоков
     * @return array
     */
    public static function getIblocks(): array
    {
        if (empty(self::$iblocks)) {
            $cache = Application::getInstance()->getManagedCache();
            if ($cache->read(8640000, self::CACHE_ID)) {
                self::$iblocks = $cache->get(self::CACHE_ID);
            }
            if(empty(self::$iblocks)){
                $rows = CIBlock::GetList([], ['CHECK_PERMISSIONS' => 'N']);
                while ($row = $rows->fetch()) {
                    self::$iblocks[$row['ID']] = $row;
                }
                $cache->set(self::CACHE_ID, self::$iblocks);
            }
        }
        return self::$iblocks;
    }
}