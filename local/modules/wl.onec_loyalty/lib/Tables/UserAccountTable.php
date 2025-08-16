<?php

namespace WL\OnecLoyalty\Tables;

use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\FloatField;
use Bitrix\Main\ORM\Fields\IntegerField;

class UserAccountTable extends DataManager
{
    /**
     * @return string
     */
    public static function getTableName(): string
    {
        return 'b_sale_user_account';
    }

    /**
     * @return array
     */
    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('USER_ID'))
                ->configureRequired(),

            (new FloatField('CURRENT_BUDGET')),
        ];
    }
}