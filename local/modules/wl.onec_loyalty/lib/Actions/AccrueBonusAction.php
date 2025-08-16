<?php

namespace WL\OnecLoyalty\Actions;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserTable;
use Bitrix\Sale\Internals\BuyerStatisticTable;
use RuntimeException;
use WL\OnecLoyalty\Enums\MethodEnum;
use WL\OnecLoyalty\Service\LoyaltyQueue;

/**
 * Начисление бонусных баллов
 */
class AccrueBonusAction
{
    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('sale');
    }

    /**
     *
     * @param int $userId
     * @param float $sum
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function run(int $userId, float $sum): void
    {
        //Проверяем, что у пользователя есть хотя бы 1 заказ
        $row = BuyerStatisticTable::query()
            ->addFilter('USER_ID', $userId)
            ->addSelect('COUNT_PART_PAID_ORDER')
            ->fetchObject();

        if (!$row || $row->getSumPaid() === 0) {
            throw new RuntimeException('У пользователя нет оплаченных заказов');
        }

        //Добавляем запись в очередь
        $user = UserTable::query()
            ->addSelect('PERSONAL_PHONE')
            ->addFilter('ID', $userId)
            ->fetchObject();

        $phone = Parser::getInstance()?->parse($user->getPersonalPhone())->format(formatType: Format::E164);

        (new LoyaltyQueue())->addTask(
            userId: $userId,
            method: MethodEnum::accrueclientbonuses,
            params: ['Phone' => $phone, 'BonusesCount' => $sum]
        );
    }
}