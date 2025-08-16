<?php

namespace WL\OnecLoyalty\Service;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Error;
use Bitrix\Main\Web\HttpClient;
use WL\OnecLoyalty\Entity\Result;
use WL\OnecLoyalty\Enums\MethodEnum;
use WL\OnecLoyalty\Tools\Log;

class Loyalty
{
    private string $serverUrl;
    private string $userName;
    private string $userPassword;

    public function __construct()
    {
        $this->serverUrl = Option::get('wl.onec_loyalty', 'server_url');
        $this->userName = Option::get('wl.onec_loyalty', 'user_name');
        $this->userPassword = Option::get('wl.onec_loyalty', 'user_password');
    }

    /**
     * @return bool
     */
    public function ping(): bool
    {
        $response = $this->sendRequest(method: MethodEnum::ping);

        return $response['Connection'];
    }

    /**
     * @param MethodEnum $method
     * @param array      $params
     *
     * @return Result
     */
    public function sendRequest(MethodEnum $method, array $params = []): Result
    {
        $url = $this->serverUrl . '/' . $method->value . '?' . http_build_query($params);
        $httpClient = new HttpClient();
        $httpClient->setTimeout(1);
        $cookie = $httpClient->getCookies()->toArray();
        $httpClient->setAuthorization($this->userName, $this->userPassword);
        $httpClient->setCookies($cookie);
        $httpClient->query($method->getHttpMethod(), $url, $params);
        $response = $httpClient->getResult();

        $response = json_decode($response, true);

        $result = new Result();
        $result->setData($response);

        if (!empty($response['ErrorMessage']) && $method->getHttpMethod() === 'POST') {
            $result->addError(new Error($response['ErrorMessage']));
            Log::getInstance()->error(
                message: $response['ErrorMessage'],
                context: [
                    'method' => $method->value,
                    'params' => $params,
                ]
            );
        }

        return $result;
    }

    /**
     * Получить бонусы по номеру телефона
     *
     * @param string $phone
     *
     * @return Result
     */
    public function getClientBonuses(string $phone): Result
    {
        $this->sendRequest(
            method: MethodEnum::unlockclientbonuses3_0,
            params: ['Phone' => $phone]
        );
        $result = $this->sendRequest(
            method: MethodEnum::getclientbonuses,
            params: ['Phone' => $phone]
        );
        $this->sendRequest(
            method: MethodEnum::unlockclientbonuses3_0,
            params: ['Phone' => $phone]
        );

        return $result;
    }


    /**
     * Начислить бонусы по номеру телефона
     *
     * @param string $phone
     * @param float  $sum
     *
     * @return Result
     */
    public function accrueClientBonuses(string $phone, float $sum): Result
    {
        return $this->sendRequest(
            method: MethodEnum::accrueclientbonuses,
            params: ['Phone' => $phone, 'BonusesCount' => $sum]
        );
    }

    /**
     * Списать бонусы по номеру телефона
     *
     * @param string $phone
     * @param float  $sum
     *
     * @return Result
     */
    public function writeOffClientBonuses(string $phone, float $sum): Result
    {
        return $this->sendRequest(
            method: MethodEnum::writeoffclientbonuses3_0,
            params: ['Phone' => $phone, 'BonusesCount' => $sum]
        );
    }
}