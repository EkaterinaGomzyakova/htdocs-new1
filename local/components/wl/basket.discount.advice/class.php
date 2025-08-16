<?php

use Bitrix\Sale;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class BasketDiscountAdviceComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $basket = Sale\Basket::loadItemsForFUser(Sale\Fuser::getId(), Bitrix\Main\Context::getCurrent()->getSite());
        $basketPrice = $basket->getPrice();

        foreach($this->arParams['TARGETS'] as $target => $price) {
            if($basketPrice < $price) {
                $this->arResult['TARGETS'][] = [
                    'CODE' => $target,
                    'PRICE_DIFF' => $price - $basketPrice,
                    'PRICE' => $price,
                ];
            }
        }

        $this->includeComponentTemplate();
        return $this->arResult;
    }
}