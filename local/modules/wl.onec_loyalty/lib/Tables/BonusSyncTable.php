<?php

namespace WL\OnecLoyalty\Tables;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\StringField;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Data\DataManager;
use Bitrix\Main\ORM\Fields\ArrayField;
use Bitrix\Main\ORM\Fields\BooleanField;
use Bitrix\Main\ORM\Fields\DatetimeField;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;

class BonusSyncTable extends DataManager
{
    /**
     * Returns DB table name for entity
     *
     * @return string
     */
    public static function getTableName(): string
    {
        return 'wl_onec_loyalty_queue';
    }


    /**
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public static function getMap(): array
    {
        return [
            (new IntegerField('ID'))
                ->configurePrimary()
                ->configureAutocomplete(),

            (new IntegerField('USER_ID'))
                ->configureRequired(),

            (new StringField('METHOD')),

            (new ArrayField('PARAMS')),

            (new DatetimeField('TIMESTAMP'))
                ->configureRequired(),

            (new BooleanField('IS_COMPLETED')),

            (new DatetimeField('DATE_EXEC')),

            (new ArrayField('RESULT')),

            (new IntegerField('ATTEMPT')),

            (new StringField('ERROR')),

            (new Reference(
                name           : 'USER',
                referenceEntity: UserTable::class,
                referenceFilter: Join::on('this.USER_ID', 'ref.ID')
            )),

            (new Reference(
                name           : 'ACCOUNT',
                referenceEntity: UserAccountTable::class,
                referenceFilter: Join::on('this.USER_ID', 'ref.USER_ID')
            )),
        ];
    }

    /**
     * Получает актуальную запись
     *
     * @param int      $userId
     * @param DateTime $dateTime
     *
     * @return EO_BonusSync|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getActualValue(int $userId, DateTime $dateTime): ?EO_BonusSync
    {
        return self::query()
            ->addSelect('*')
            ->addFilter('USER_ID', $userId)
            ->addFilter('>=TIMESTAMP', $dateTime)
            ->fetchObject();
    }

    /**
     * Возвращает список не синхронизированных записей
     *
     * @return EO_BonusSync_Collection
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getNotSyncedList(): EO_BonusSync_Collection
    {
        return self::query()
            ->addSelect('*')
            ->addSelect('USER')
            ->addSelect('ACCOUNT')
            ->addFilter('IS_COMPLETED', false)
            ->fetchCollection();
    }
}