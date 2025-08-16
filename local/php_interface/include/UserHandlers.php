<?php

namespace WL;

use Bitrix\Main\Config\Option;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Web\Json;
use CEventLog;
use CSaleDiscount;

Loc::loadMessages(__FILE__);

class UserHandlers
{
    public static function OnBeforeUserRegister(&$arFields)
    {
        global $APPLICATION;
        $phone = '+' . preg_replace("/[^0-9]/", '', $arFields['PERSONAL_PHONE']);
        $arFields['LOGIN'] = $phone;
        // проверяем корректность номера телефона
        $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()?->parse($phone);
        if (!$phoneNumber?->isValid()) {
            $APPLICATION->ThrowException(Loc::getMessage('USER_BEFORE_REGISTER_INVALID_PHONE'));
            return false;
        }

        // проверяем есть ли запись в таблице авторизации по номеру телефона и если запись есть, то запрещаем регистрацию
        $userPhoneAuth = \Bitrix\Main\UserPhoneAuthTable::getList([
            'filter' => ['PHONE_NUMBER' => '+' . preg_replace("/[^0-9]/", '', $arFields['PERSONAL_PHONE'])]
        ])->fetch();
        if (!empty($userPhoneAuth)) {
            $APPLICATION->ThrowException(Loc::getMessage('USER_BEFORE_REGISTER_ALREADY_REGISTERED'));
            return false;
        }
    }

    public static function OnAfterUserRegister(&$arFields)
    {
        if ((int) $arFields['USER_ID'] > 0) {
            // проверяем есть ли запись в таблице авторизации по номеру телефона и если записи нет, то добавляем ее
            $phone = '+' . preg_replace("/[^0-9]/", '', $arFields['PERSONAL_PHONE']);
            $userPhoneAuth = \Bitrix\Main\UserPhoneAuthTable::getList([
                'filter' => ['PHONE_NUMBER' => $phone]
            ])->fetch();
            if (empty($userPhoneAuth)) {
                // проверяем создан ли пользователь из публички, если да, то сразу подтверждаем телефон
                $confirmed = 'N';
                if ($arFields['LOGIN'] == $phone) {
                    $confirmed = 'Y';
                }
                \Bitrix\Main\UserPhoneAuthTable::add(array(
                    "USER_ID" => $arFields['USER_ID'],
                    "PHONE_NUMBER" => $phone,
                    "CONFIRMED" => $confirmed,
                    "OTP_SECRET" => uniqid()
                ));
            }

            [$lastName, $firstName] = preg_split('~\s+~', trim($arFields['NAME']));
            $contact = [
                'email' => trim($arFields['EMAIL']),
                'available' => 1,
                'firstName' => $firstName,
                'lastName' => $lastName,
            ];

            // регистрируем запись о пользователе в списке рассылки Unisender

            $apiKey = Option::get('acrit.unisender', 'ACRIT_UNISENDER_API_KEY');
            if (!empty($apiKey)) {
                try {
                    $addedContact = null;
                    $sender = new Unisender();
                    $addedContact = $sender->addContact($contact);
                    $result = $sender->pushContacts();

                    if ($result['inserted']) {
                        CEventLog::Add([
                            'SEVERITY' => 'INFO',
                            'AUDIT_TYPE_ID' => 'UNISENDER',
                            'MODULE_ID' => Unisender::class,
                            'ITEM_ID' => -1,
                            'DESCRIPTION' => Loc::getMessage('USER_BEFORE_REGISTER_UNISENDER_ADDED', [
                                '#LOGIN#' => trim($arFields['LOGIN']),
                                '#NAME#' => trim($arFields['NAME']),
                                '#EMAIL#' => trim($arFields['EMAIL']),
                            ]),
                        ]);
                    }
                    if ($result['updated']) {
                        CEventLog::Add([
                            'SEVERITY' => 'INFO',
                            'AUDIT_TYPE_ID' => 'UNISENDER',
                            'MODULE_ID' => Unisender::class,
                            'ITEM_ID' => -2,
                            'DESCRIPTION' => Loc::getMessage('USER_BEFORE_REGISTER_UNISENDER_UPDATED', [
                                '#LOGIN#' => trim($arFields['LOGIN']),
                                '#NAME#' => trim($arFields['NAME']),
                                '#EMAIL#' => trim($arFields['EMAIL']),
                            ]),
                        ]);
                    }
                    if ($result['invalid']) {
                        CEventLog::Add([
                            'SEVERITY' => 'INFO',
                            'AUDIT_TYPE_ID' => 'UNISENDER',
                            'MODULE_ID' => Unisender::class,
                            'ITEM_ID' => -3,
                            'DESCRIPTION' => Loc::getMessage('USER_BEFORE_REGISTER_UNISENDER_INVALID', [
                                '#LOGIN#' => trim($arFields['LOGIN']),
                                '#NAME#' => trim($arFields['NAME']),
                                '#EMAIL#' => trim($arFields['EMAIL']),
                            ]),
                        ]);
                        mail(
                            'director@weblipka.ru',
                            'Clanbeauty::Unisender user registration fail',
                            "Ошибка регистрации пользователя в Unisender\nСмотри лог: https://clanbeauty.ru/bitrix/admin/perfmon_table.php?lang=ru&table_name=b_event_log&f_AUDIT_TYPE_ID=UNISENDER&f_SEVERITY=INFO&f_ITEM_ID=-3&apply_filter=Y"
                        );
                    }
                } catch (\ErrorException $e) {
                    $description = [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ];
                    if ($addedContact) {
                        $description['contact'] = $addedContact;
                    }
                    CEventLog::Add([
                        'SEVERITY' => 'ERROR',
                        'AUDIT_TYPE_ID' => 'UNISENDER',
                        'MODULE_ID' => Unisender::class,
                        'ITEM_ID' => $e->getCode(),
                        'DESCRIPTION' => Json::encode($description),
                    ]);
                    mail(
                        'director@weblipka.ru',
                        'Clanbeauty::Unisender critical error',
                        "Критическая ошибка Unisender\nСмотри лог: https://clanbeauty.ru/bitrix/admin/perfmon_table.php?lang=ru&table_name=b_event_log&f_AUDIT_TYPE_ID=UNISENDER&f_SEVERITY=ERROR&apply_filter=Y"
                    );
                } catch (\RuntimeException $e) {
                    $description = [
                        'message' => $e->getMessage(),
                        'code' => $e->getCode(),
                    ];
                    if ($addedContact) {
                        $description['contact'] = $addedContact;
                    }
                    CEventLog::Add([
                        'SEVERITY' => 'WARNING',
                        'AUDIT_TYPE_ID' => 'UNISENDER',
                        'MODULE_ID' => Unisender::class,
                        'ITEM_ID' => $e->getCode(),
                        'DESCRIPTION' => Json::encode($description),
                    ]);
                    mail(
                        'director@weblipka.ru',
                        'Clanbeauty::Unisender runtime error',
                        "Ошибка API Unisender\nСмотри лог: https://clanbeauty.ru/bitrix/admin/perfmon_table.php?lang=ru&table_name=b_event_log&f_AUDIT_TYPE_ID=UNISENDER&f_SEVERITY=WARNING&apply_filter=Y"
                    );
                }
            }
        }
    }

    public static function OnAfterUserAdd(&$arFields)
    {
        $referralActive = Option::get('wl.snailshop', 'referal_is_active');
        $referralXmlIDBasketRule = Option::get('wl.snailshop', 'referal_xml_id_basket_rule');

        if ($referralActive === "Y" && !empty($referralXmlIDBasketRule)) {
            //получение id правила корзины по его xml_id

            $dbResultBasketRules = CSaleDiscount::GetList(
                ["SORT" => "ASC"],
                [
                    "XML_ID" => $referralXmlIDBasketRule,
                    "ACTIVE" => "Y",
                ],
                false,
                false,
                ["ID"]
            );

            if ($arResultBasketRule = $dbResultBasketRules->Fetch()) {
                $idReferralBasketRule = $arResultBasketRule["ID"];
            }

            //создаем купон для нового зарегистрированного пользователя
            if ($idReferralBasketRule) {
                try {
                    $fields = [];
                    $fields['COUNT'] = 1;
                    $coupon = 'REF-' . $arFields["ID"];

                    $fields['COUPON'] = [
                        'DISCOUNT_ID' => $idReferralBasketRule,
                        'ACTIVE_FROM' => null,
                        'ACTIVE_TO' => null,
                        'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_MULTI_ORDER,
                        'MAX_USE' => 0,
                        "COUPON" => $coupon,
                    ];
                    \Bitrix\Sale\Internals\DiscountCouponTable::add(
                        $fields['COUPON'],
                        $fields['COUNT']
                    );
                } catch (\Exception $exception) {
                }
            }
        }
    }
}
