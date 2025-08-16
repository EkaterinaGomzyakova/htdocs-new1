<?php

namespace Sprint\Migration;

use CSaleDiscount;
use CUser;


class BX5809referralprogram20230404150318 extends Version
{
    protected $description = "";

    protected $moduleVersion = "4.1.2";

    public function up()
    {
        //получение id правила корзины по его xml_id
        $dbResultBasketRules = CSaleDiscount::GetList(
            ["SORT" => "ASC"],
            [
                "XML_ID" => "referal_discount"
            ],
            false,
            false,
            ["ID"]
        );

        if ($arResultBasketRule = $dbResultBasketRules->Fetch()) {
            $idReferalBasketRule = $arResultBasketRule["ID"];
        }

        try {
            if ($idReferalBasketRule > 0) {
                //получаем список ID активных пользователей
                $dbResultUsers = \Bitrix\Main\UserTable::getList(
                    [
                        'select' => ['ID'],
                    ]
                );

                $resultListUsersId = [];

                while ($arResultUser = $dbResultUsers->Fetch()) {
                    $resultListUsersId[] = $arResultUser["ID"];
                }

                //создаем купоны для всех активных пользователей
                foreach ($resultListUsersId as $userId) {
                    try {
                        $fields['COUNT'] = 1;
                        $coupon = 'REF-' . $userId;

                        $fields['COUPON'] = [
                            'DISCOUNT_ID' => $idReferalBasketRule,
                            'ACTIVE_FROM' => null,
                            'ACTIVE_TO' => null,
                            'TYPE' => \Bitrix\Sale\Internals\DiscountCouponTable::TYPE_MULTI_ORDER,
                            'MAX_USE' => 0,
                            "COUPON" => $coupon,
                        ];
                        $couponsResult = \Bitrix\Sale\Internals\DiscountCouponTable::add(
                            $fields['COUPON'],
                            $fields['COUNT']
                        );
                    } catch (\Exception $exception) {
                    }
                }
            }
        } catch (\Exception $exception) {
        }
    }

    public function down()
    {

    }
}
