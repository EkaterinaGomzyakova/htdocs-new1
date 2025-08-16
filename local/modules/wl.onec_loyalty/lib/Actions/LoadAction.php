<?php

namespace WL\OnecLoyalty\Actions;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\SystemException;
use CCurrencyLang;
use CSaleUserAccount;
use WL\OnecLoyalty\Service\LoyaltyQueue;

class LoadAction
{
    /**
     * @throws LoaderException
     */
    public function __construct()
    {
        Loader::requireModule('sale');
    }

    /**
     * @param int    $userId
     * @param string $currency
     *
     * @return array
     * @throws ArgumentException
     * @throws SystemException
     */
    public function run(int $userId, string $currency = 'RUB'): array
    {
        //Получаем текущий баланс с сайта.
        $account = CSaleUserAccount::GetByUserId(
            userID  : $userId,
            currency: $currency
        );

        //Кол-во бонусных баллов
        $bonusPoints = 0;

        if ($account) {
            //Если счет есть, то выставляем текущий баланс бонусов
            $bonusPoints = (float)$account['CURRENT_BUDGET'];
        }

        $isActual = (new LoyaltyQueue())->syncClientBonuses($userId);

        return [
            'isActual' => $isActual,
            'value'    => CCurrencyLang::CurrencyFormat(
                price   : $bonusPoints,
                currency: $currency
            ),
        ];
    }
}