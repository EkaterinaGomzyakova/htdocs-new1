<?php

namespace WL\OnecLoyalty\Actions;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\Type\DateTime;
use CSaleUserAccount;
use RuntimeException;
use Throwable;
use WL\OnecLoyalty\Enums\MethodEnum;
use WL\OnecLoyalty\Service\Loyalty;
use WL\OnecLoyalty\Tables\BonusSyncTable;
use WL\OnecLoyalty\Tools\Log;

class QueueProcessingAction
{
    private Loyalty $loyalty;

    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('sale');
        $this->loyalty = new Loyalty();
    }

    /**
     * @return Result
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function run(): Result
    {
        $result = new Result();

        //Получаем записи на синхронизацию
        $rows = BonusSyncTable::getNotSyncedList();
        foreach ($rows as $row) {
            $resultRow = null;
            try {
                //Отправка в 1С
                $method = MethodEnum::tryFrom($row->getMethod());
                $params = $row->getParams();
                if ($method) {
                    $this->loyalty->sendRequest(
                        method: MethodEnum::unlockclientbonuses3_0,
                        params: $params
                    );
                    $resultRow = $this->loyalty->sendRequest(method: $method, params: $params);
                    $this->loyalty->sendRequest(
                        method: MethodEnum::unlockclientbonuses3_0,
                        params: $params
                    );
                }

                $row->setDateExec(new DateTime());
                $row->setResult($resultRow->getData());

                if ($resultRow->isSuccess()) {
                    $row->setIsCompleted($resultRow->isSuccess());
                } else {
                    $row->setError(implode(PHP_EOL, $resultRow->getErrorMessages()));
                    $attempt = ($row->getAttempt() ?? 1) - 1;
                    $row->setAttempt($attempt);

                    if ($attempt <= 0) {
                        $row->setIsCompleted(true);
                    }
                }
                $r = $row->save();
                if (!$r->isSuccess()) {
                    throw new RuntimeException(implode(PHP_EOL, $r->getErrorMessages()));
                }

                switch ($method) {
                    case MethodEnum::getclientbonuses:
                        $data = $resultRow->getData();
                        if ($data['ClientNotFound'] === false && empty($data['ErrorMessage'])) {
                            $this->updateBonuses(
                                userId        : $row->getUserId(),
                                count         : $data['BonusCount'],
                                currentBalance: $row->getAccount()->getCurrentBudget()
                            );
                        }
                        break;
                }
            } catch (Throwable $exception) {
                Log::getInstance()->exception($exception);
                $result->addError(
                    new Error(
                        message   : $exception->getMessage(),
                        customData: $exception->getTrace()
                    )
                );
            }
        }
        return $result;
    }

    /**
     * Обновление бонусного счета
     *
     * @param int   $userId
     * @param float $count
     * @param float $currentBalance
     *
     * @return void
     */
    private function updateBonuses(int $userId, float $count, float $currentBalance): void
    {
        $diff = $count - $currentBalance;
        if ($diff > 0 || $diff < 0) {
            CSaleUserAccount::UpdateAccount(
                userID     : $userId,
                sum        : $diff,
                currency   : 'RUB',
                description: 'Синхронизация с 1С',
            );
        }
    }
}