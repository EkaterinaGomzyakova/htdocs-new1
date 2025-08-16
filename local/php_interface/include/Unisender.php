<?php

namespace WL;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use ErrorException;
use RuntimeException;
use Unisender\ApiWrapper\UnisenderApi;
use WL\UnisenderErrorEnum;

require_once __DIR__ . '/UnisenderErrorEnum.php';

Loc::loadMessages(__FILE__);

/**
 * Класс для взаимодействия с сервисом Unisender
 */
class Unisender
{
    private static ?UnisenderApi $unisenderApi = null;
    private static int $listId;
    private array $contactsData;

    private const VALID_CONTACT_FIELD_MAP = [
        'email' => 'email',
        'available' => 'email_availability',
        'status' => 'email_status',
        'tags' => 'tags',
        'firstName' => 'Name',
        'lastName' => 'last_name',
    ];

    public const VALID_STATUSES = ['active', 'inactive', 'new', 'unsubscribed'];
    private const DEFAULT_TAGS = [];
    private const DEFAULT_RETRY_COUNT = 5;

    private const OVERWRITE_TAGS = false;

    /**
     * @param int|null $retryCount число попыток взаимодействия с АПИ
     * @throws ErrorException
     * @throws LoaderException
     */
    public function __construct(int $retryCount = null)
    {
        if (!self::$unisenderApi) {
            if (!Loader::includeModule('acrit.unisender')) {
                throw new ErrorException (Loc::getMessage('UNISENDER_ERROR_MODULE_NOT_FOUND'), UnisenderErrorEnum::MODULE_NOT_FOUND->value);
            }
            if (!class_exists('\\Unisender\\ApiWrapper\\UnisenderApi')) {
                throw new ErrorException(Loc::getMessage('UNISENDER_ERROR_API_NOT_FOUND'), UnisenderErrorEnum::API_NOT_FOUND->value);
            }
            $apiKey = Option::get('acrit.unisender', 'ACRIT_UNISENDER_API_KEY');
            $charset = Option::get('acrit.unisender', 'ACRIT_UNISENDER_CHARSET');
            self::$listId = (int) Option::get('acrit.unisender', 'ACRIT_UNISENDER_LIST');
            $retryCount = $retryCount > 0 ? $retryCount : self::DEFAULT_RETRY_COUNT;
            self::$unisenderApi = new UnisenderApi($apiKey, $charset, $retryCount);
            $result = self::$unisenderApi->getLists();
            if (!$result) {
                throw new RuntimeException(Loc::getMessage('UNISENDER_ERROR_INVALID_API_KEY'), UnisenderErrorEnum::INVALID_API_KEY->value);
            }
            try {
                $result = Json::decode($result);
            }
            catch (ArgumentException $e) {
                throw new RuntimeException(Loc::getMessage('UNISENDER_ERROR_INVALID_RESPONSE'), UnisenderErrorEnum::INVALID_RESPONSE->value);
            }

            if ($result['error']) {
                $message = $this->getApiErrorMessage ($result);
                throw new RuntimeException(...$message);
            }

            if (!in_array(self::$listId, array_column($result['result'], 'id'), true)) {
                throw new RuntimeException(Loc::getMessage('UNISENDER_ERROR_WRONG_LIST_ID'), UnisenderErrorEnum::WRONG_LIST_ID->value);
            }
        }
        $this->contactsData = [];
    }

    /**
     * Формирование сообщений об ошибках из АПИ
     *
     * @param array $result
     * @return array
     */
    private function getApiErrorMessage (array $result): array
    {
        return match ($result['code']) {
            'invalid_api_key' => [Loc::getMessage('UNISENDER_ERROR_UNAUTHORIZED'), UnisenderErrorEnum::UNAUTHORIZED->value],
            'access_denied' => [Loc::getMessage('UNISENDER_ERROR_ACCESS_DENIED'), UnisenderErrorEnum::ACCESS_DENIED->value],
            'not_enough_money' => [Loc::getMessage('UNISENDER_ERROR_NOT_ENOUGH_MONEY'), UnisenderErrorEnum::NOT_ENOUGH_MONEY->value],
            'retry_later' => [Loc::getMessage('UNISENDER_ERROR_RETRY'), UnisenderErrorEnum::RETRY->value],
            'api_call_limit_exceeded_for_api_key', 'api_call_limit_exceeded_for_ip' => [Loc::getMessage('UNISENDER_ERROR_API_CALL_LIMIT'), UnisenderErrorEnum::API_CALL_LIMIT->value],
            default => [Loc::getMessage('UNISENDER_ERROR_UNSPECIFIED', ['#ERROR#' => $result['error']]), UnisenderErrorEnum::UNSPECIFIED->value],
        };
    }

    /**
     * Получить АПИ
     *
     * @return UnisenderApi
     */
    public function getApi(): UnisenderApi
    {
        return self::$unisenderApi;
    }

    /**
     * Получить ID списка рассылки
     *
     * @return string
     */
    public function getListId(): string
    {
        return self::$listId;
    }

    /**
     * Добавить контакт
     *
     * @param array $fields
     * @param array $arListIds
     * @return array
     */
    public function addContact(array $fields, array $arListIds = []): array
    {
        $fields['status'] = in_array($fields['status'], self::VALID_STATUSES, true)
            ? $fields['status']
            : current(self::VALID_STATUSES);
        $fields['tags'] = implode(',', $fields['tags'] ?? self::DEFAULT_TAGS);
        if ($unsetFieldNames = array_diff(array_keys(self::VALID_CONTACT_FIELD_MAP), array_keys($fields))) {
            throw new RuntimeException(Loc::getMessage('UNISENDER_ERROR_USER_MANDATORY_FIELDS_NOT_SET', ['#FIELDS#' => implode(', ', $unsetFieldNames)]), UnisenderErrorEnum::USER_MANDATORY_FIELDS_NOT_SET->value);
        }
        $fields['available'] = $fields['available'] ? 1 : 0;
        $userData = [
            $arListIds ?: [self::$listId],
        ];
        foreach (array_keys(self::VALID_CONTACT_FIELD_MAP) as $fieldName) {
            $userData[] = $fields[$fieldName];
        }
        $this->contactsData[] = $userData;
        return $fields;
    }

    /**
     * Отправить список контактов через АПИ
     *
     * @return array|null
     */
    public function pushContacts(): ?array
    {
        if (empty($this->contactsData)) {
            return [];
        }
        $fieldNames = array_merge(
            ['email_list_ids'],
            array_values(self::VALID_CONTACT_FIELD_MAP)
        );
        $result = self::$unisenderApi->importContacts([
            'field_names' => $fieldNames,
            'data' => $this->contactsData,
            'overwrite_tags' => self::OVERWRITE_TAGS ? 1 : 0,
        ]);
        $this->contactsData = [];
        try {
            return $result ? Json::decode($result)['result'] : null;
        }
        catch (ArgumentException $e) {
            return null;
        }
    }
}
