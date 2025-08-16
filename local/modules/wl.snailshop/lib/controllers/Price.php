<?php


namespace SnailShop\Controller;


use Bitrix\Catalog\PriceTable;
use Bitrix\Main\Engine\ActionFilter\Authentication;
use Bitrix\Main\Engine\ActionFilter\Csrf;
use Bitrix\Main\Engine\Controller;
use Exception;
use WL\SnailShop;


class Price extends Controller
{
    public function configureActions(): array
    {
        return [
            'updatePrice' => ['prefilters' => [
                new Csrf(),
                new Authentication()
            ]],
        ];
    }

    function updatePriceAction(float $value, int $product_id)
    {
        if (!SnailShop::userIsStaff()) {
            throw new Exception('Доступ запрещен');
        }

        $price = PriceTable::getList([
            'filter' => [
                'PRODUCT_ID' => $product_id,
                'CATALOG_GROUP_ID' => 1
            ],
            'select' => ['PRODUCT_ID', 'PRICE', 'ID']
        ])->fetch();


        if (empty($price)) {
            $result = \Bitrix\Catalog\Model\Price::add(["PRODUCT_ID" => $product_id, "CATALOG_GROUP_ID" => 1, "PRICE" => $value, "CURRENCY" => "RUB"]);
        } else {
            $result = \Bitrix\Catalog\Model\Price::update($price['ID'], ['PRICE' => $value]);
        }
        
        dump($result); die();

        return null;
    }
}