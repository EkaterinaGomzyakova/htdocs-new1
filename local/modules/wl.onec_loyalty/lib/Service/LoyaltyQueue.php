<?php

namespace WL\OnecLoyalty\Service;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use Bitrix\Main\UserTable;
use RuntimeException;
use WL\OnecLoyalty\Enums\MethodEnum;
use WL\OnecLoyalty\Tables\BonusSyncTable;

class LoyaltyQueue
{
    /**
     * Синхронизация бонусных баллов, возвращает false если бонусный счет в актуальном состоянии
     *
     * @param int $userId
     *
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function syncClientBonuses(int $userId): bool
    {
        $date = new DateTime();
        $relevanceMinutes = Option::get(moduleId: 'wl.onec_loyalty', name: 'sync_relevance_period', default: "5");
        $date->add("-$relevanceMinutes minutes");
        $row = BonusSyncTable::getActualValue(
            userId  : $userId,
            dateTime: $date
        );

        //Если есть запись и стоит пометка Выполнено, то считаем что бонусы синхронизированы
        if ($row && $row->getIsCompleted()) {
            return true;
        }

        //Если есть уже запись, то новую не создаем
        if ($row) {
            return false;
        }

        $user = UserTable::query()
            ->addSelect('PERSONAL_PHONE')
            ->addFilter('ID', $userId)
            ->fetchObject();

        $syncTask = BonusSyncTable::createObject();
        $syncTask->setUserId($userId);

        $phone = Parser::getInstance()?->parse($user->getPersonalPhone())->format(formatType: Format::E164);
        $syncTask->setParams(['Phone' => $phone]);
        $syncTask->setTimestamp(new DateTime());
        $syncTask->setMethod(MethodEnum::getclientbonuses->value);
        $r = $syncTask->save();
        if (!$r->isSuccess()) {
            throw new RuntimeException(implode(PHP_EOL, $r->getErrorMessages()));
        }
        return false;
    }

    /**
     * @param int   $userId
     * @param float $sum
     *
     * @return void
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function accrueClientBonuses(int $userId, float $sum): void
    {
        $user = UserTable::query()
            ->addSelect('PERSONAL_PHONE')
            ->addFilter('ID', $userId)
            ->fetchObject();

        $syncTask = BonusSyncTable::createObject();
        $syncTask->setUserId($userId);
        $phone = Parser::getInstance()?->parse($user->getPersonalPhone())->format(formatType: Format::E164);
        $syncTask->setParams(['Phone' => $phone, 'BonusesCount' => $sum]);
        $syncTask->setTimestamp(new DateTime());
        $syncTask->setMethod(MethodEnum::accrueclientbonuses->value);
        $syncTask->setAttempt(Option::get('wl.onec_loyalty', 'count_attempt', 60));
        $r = $syncTask->save();
        if (!$r->isSuccess()) {
            throw new RuntimeException(implode(PHP_EOL, $r->getErrorMessages()));
        }
    }

    public function addTask(int $userId, MethodEnum $method, array $params = [])
    {
        $syncTask = BonusSyncTable::createObject();
        $syncTask->setUserId($userId);
        $syncTask->setParams($params);
        $syncTask->setTimestamp(new DateTime());
        $syncTask->setMethod($method->value);
        if ($method->getHttpMethod() === 'POST') {
            $syncTask->setAttempt(Option::get('wl.onec_loyalty', 'count_attempt', 60));
        } else {
            $syncTask->setAttempt(Option::get('wl.onec_loyalty', 'count_attempt', 1));
        }
        $r = $syncTask->save();
        if (!$r->isSuccess()) {
            throw new RuntimeException(implode(PHP_EOL, $r->getErrorMessages()));
        }
    }
}