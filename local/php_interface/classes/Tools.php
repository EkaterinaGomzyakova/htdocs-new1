<?php

namespace Clanbeauty;

use Bitrix\Main\Loader;
use Bitrix\Main\UserTable;

class Tools
{
    public static function getUsersDiscounts($usersID)
    {
        Loader::includeModule('catalog');
        $rowUsers = UserTable::getList([
            'filter' => ['ID' => $usersID],
            'select' => ['ID', 'GROUPS']
        ]);

        $users = [];
        while ($row = $rowUsers->fetch()) {
            if (!isset($users[$row['ID']])) {
                $users[$row['ID']] = [
                    'ID' => $row['ID'],
                    'GROUPS' => []
                ];
            }
            $users[$row['ID']]['GROUPS'][] = $row['MAIN_USER_GROUPS_GROUP_ID'];
        }
        foreach ($users as &$user) {
            $user['DISCOUNT'] = \CCatalogDiscountSave::GetDiscount([
                "USER_ID" => $user['ID'],
                "USER_GROUPS" => $user['GROUPS'],
                "SITE_ID" => "s1"
            ])[0];
        }
        unset($user);
        return array_values($users);
    }
}