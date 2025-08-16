<?php

namespace WL;

use Bitrix\Main\Loader;

class Agents
{
    /**
     * Пересчет флага HIT - Скидка в товарах
     * @return string
     */
    public static function RecalculateDiscounts()
    {
        try
        {
            CalculateDiscountProperty();
        } catch(Exception $e) {
            AddMessage2Log($exception->getMessage());
        }
        return "\WL\Agents::RecalculateDiscounts();";
    }

    public static function TelegramMessengerBot()
    {
        try
        {
            Loader::includeModule("wl.telegrambotmessenger");
            $arSelect = ["ID", "IBLOCK_ID", "NAME", 'DETAIL_TEXT', 'PREVIEW_TEXT', 'DETAIL_PAGE_URL'];
            $arFilter = ["IBLOCK_ID" => ARTICLES_IBLOCK_ID, "ACTIVE" => "Y", "PROPERTY_IS_PUBLISHED_IN_TELEGRAM_VALUE" => false];
            $res = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
            if ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $articleText = strip_tags("**" . $arFields['NAME'] . "**\n\n" . $arFields['PREVIEW_TEXT'] . "\n\n" . "Подробнее читайте в нашей статье: " . "https://clanbeauty.ru" . $arFields['DETAIL_PAGE_URL']);
                \WL\Telegrambotmessenger\TelegramBot::articleForTelegramBot($articleText);
                \CIBlockElement::SetPropertyValuesEx($arFields['ID'], ARTICLES_IBLOCK_ID, ["IS_PUBLISHED_IN_TELEGRAM" => 119]); //119 = "Y"
            }
        } catch(Exception $e) {
            AddMessage2Log($exception->getMessage());
        }
        return "\WL\Agents::TelegramMessengerBot();";
    }


    /*public static function RecalculateNoveltyFlag() {
        $arFilter = [
            "IBLOCK_ID" => GOODS_IBLOCK_ID,
            "<DATE_CREATE" => date('d.m.Y H:i:s', strtotime('-3 weeks')),
            "PROPERTY_HIT_VALUE" => "Новинка",
        ];
        $arSelect = ["ID", "IBLOCK_ID", "DATE_CREATE"];
        $dbElements = \CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
        while($arElement = $dbElements->Fetch()) {
            $dbProperties = \CIBlockElement::GetProperty(GOODS_IBLOCK_ID, $arElement['ID'], [], ['CODE' => "HIT"]);
            $arProperties = [];
            while ($arProperty = $dbProperties->Fetch()) {
                if (!empty($arProperty['VALUE'])) {
                    $arProperties['HIT'][] = $arProperty['VALUE'];
                }
            }

            if (in_array(CATALOG_NOVELTY_ACTION_VALUE_ID, $arProperties['HIT'])) {
                if (($key = array_search(CATALOG_NOVELTY_ACTION_VALUE_ID, $arProperties['HIT'])) !== false) {
                    unset($arProperties['HIT'][$key]);
                    if (empty($arProperties['HIT'])) {
                        $arProperties['HIT'] = false;
                    }
                    \CIBlockElement::SetPropertyValuesEx($arElement['ID'], GOODS_IBLOCK_ID, $arProperties);
                    \Bitrix\Iblock\PropertyIndex\Manager::updateElementIndex(GOODS_IBLOCK_ID, $arElement['ID']);
                }
            }
        }
        return "\WL\Agents::RecalculateNoveltyFlag();";
    }*/
}