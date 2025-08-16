<?

namespace WL;

class SnailShop
{
    public static function userIsStaff()
    {
        $arStaffGroups = [1, 9, 10];
        global $USER;
        return \array_intersect(\CUser::GetUserGroup($USER->GetId()), $arStaffGroups) ? true : false;
    }

    public static function userIsShopAdmin()
    {
        $arGroups = [1, 9];
        global $USER;
        return \array_intersect(\CUser::GetUserGroup($USER->GetId()), $arGroups) ? true : false;
    }

    public static function getUserStoreId()
    {
        global $USER;
        $arUser = \Bitrix\Main\UserTable::getList([
            'filter' => ['ID' => $USER->getId()],
            'select' => ['UF_SHOP']
        ])->Fetch();

        return $arUser['UF_SHOP'] ? $arUser['UF_SHOP'] : false;
    }

    public static function getUserShopId()
    {
        $userStoreId = self::getUserStoreId();
        $dbShops = \Bitrix\Sale\Internals\CompanyTable::getList([
            'filter' => ['UF_LINKED_STORE' => $userStoreId],
        ]);
        if ($arShop = $dbShops->fetch()) {
            return $arShop['ID'];
        }

        return false;
    }

    public static function getUserShopName()
    {
        $userStoreId = self::getUserStoreId();
        $dbShops = \Bitrix\Sale\Internals\CompanyTable::getList([
            'filter' => ['UF_LINKED_STORE' => $userStoreId],
        ]);
        if ($arShop = $dbShops->fetch()) {
            return $arShop['NAME'];
        }
    }

    public static function getDefaultStore()
    {
        return \CCatalogStore::GetList([], ['IS_DEFAULT' => 'Y'])->Fetch();
    }

    public static function getOptionsValuesJS(array $optionNames) {
        $arOptions = [];
        foreach($optionNames as $optionName) {
            $arOptions[$optionName] = \Bitrix\Main\Config\Option::get('wl.snailshop', $optionName);
        }
        echo '<script>var ClanbeautyFrontOptions = ' . \Bitrix\Main\Web\Json::encode($arOptions) . '</script>';
    }
}
