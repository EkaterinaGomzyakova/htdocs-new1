<?php

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", ["ArticlesReplace", "replaceArticleWrongCharacters"]);
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", ["ArticlesReplace", "replaceArticleWrongCharacters"]);

AddEventHandler("iblock", "OnBeforeIBlockElementUpdate", ["ArticlesReplace", "replaceEmoji"]);
AddEventHandler("iblock", "OnBeforeIBlockElementAdd", ["ArticlesReplace", "replaceEmoji"]);

class ArticlesReplace
{
    public static function replaceArticleWrongCharacters(&$arFields)
    {
        if (in_array($arFields['IBLOCK_ID'], [ARTICLES_IBLOCK_ID, BLOGGER_ARTICLES_IBLOCK_ID])) {
            if (!empty($arFields['PREVIEW_TEXT'])) {
                $arFields['PREVIEW_TEXT'] = self::replaceWrongChar($arFields['PREVIEW_TEXT']);
            }

            if (!empty($arFields['DETAIL_TEXT'])) {
                $arFields['DETAIL_TEXT'] = self::replaceWrongChar($arFields['DETAIL_TEXT']);
            }
        }
    }

    public static function replaceEmoji(&$arFields)
    {
        if (in_array($arFields['IBLOCK_ID'], [ARTICLES_IBLOCK_ID, BLOGGER_ARTICLES_IBLOCK_ID])) {
            if (!empty($arFields['PREVIEW_TEXT'])) {
                $arFields['PREVIEW_TEXT'] = Bitrix\Main\Text\Emoji::encode($arFields['PREVIEW_TEXT']);
            }

            if (!empty($arFields['DETAIL_TEXT'])) {
                $arFields['DETAIL_TEXT'] = Bitrix\Main\Text\Emoji::encode($arFields['DETAIL_TEXT']);
            }
        }
    }

    public static function replaceWrongChar($string)
    {
        static $replaceTable = [
        'и&#774;' => "й",
        'е&#776;' => "ё",
        '&#10240;' => ""
        ];
        return str_replace(array_keys($replaceTable), array_values($replaceTable), $string);
    }
}