<?php

namespace WL\Handlers;

use Bitrix\Main\Application;
use Bitrix\Main\Web\Cookie;
use Bitrix\Main\PhoneNumber\Format;
use Bitrix\Main\PhoneNumber\Parser;

class Main
{
    public static function OnBeforeProlog()
    {
        $request = Application::getInstance()->getContext()->getRequest();
        if (!empty($request->get('utm_source'))) {
            $cookie = new Cookie("utm_source", $request->get('utm_source'), time() + 86400 * 14);
            Application::getInstance()->getContext()->getResponse()->addCookie($cookie);
        }
    }

    public static function OnBeforeUserUpdateHandler(&$arFields)
	{
		$arUser = \CUser::GetByID($arFields['ID'])->fetch();
        if(strlen($arUser['PERSONAL_PHONE']) > 0) {
            $parsedPhone = Parser::getInstance()->parse($arUser['PERSONAL_PHONE']);
            $arFields['PERSONAL_MOBILE'] = $parsedPhone->format(Format::E164);
        }
	}
}