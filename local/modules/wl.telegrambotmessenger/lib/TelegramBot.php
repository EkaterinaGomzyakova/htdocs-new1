<?php

namespace WL\Telegrambotmessenger;
use Bitrix\Main\Config\Option;

class TelegramBot
{
    protected static $botApiKey;
    protected static $botUsername;
    protected static $botChatId;

    static function getHookUrl()
    {
        return 'https://'.$_SERVER['HTTP_HOST'];
    }

    static function getBotApiKey()
    {
        if (is_null(self::$botApiKey)) {
            self::$botApiKey = Option::get('wl.telegrambotmessenger', 'api_key');
        }
        return self::$botApiKey;
    }

    static function getBotUserName()
    {
        if (is_null(self::$botUsername)) {
            self::$botUsername = Option::get('wl.telegrambotmessenger', 'name');
        }
        return self::$botUsername;
    }

    static function getBotChatId()
    {
        if (is_null(self::$botChatId)) {
            self::$botChatId = Option::get('wl.telegrambotmessenger', 'chat_id');
        }
        return self::$botChatId;
    }

    function articleForTelegramBot($articleText)
    {
        require $_SERVER['DOCUMENT_ROOT'] . '/local/vendor/autoload.php';
        $hook_url = self::getHookUrl();
        $chat_id = self::getBotChatId();
        try {
            // Create Telegram API object
            $telegramObj = new \Longman\TelegramBot\Telegram(self::getBotApiKey(), self::getBotUserName());

            \Longman\TelegramBot\Request::sendMessage([
                'chat_id' => $chat_id,
                'parse_mode' => 'markdown',
                'text' => html_entity_decode($articleText, ENT_COMPAT),
            ]);
            // Set webhook
            //$result = $telegramObj->setWebhook($hook_url);
            //if ($result->isOk()) {
            //  echo $result->getDescription();
            //}
        } catch (\Longman\TelegramBot\Exception\TelegramException $e) {
            // log telegram errors
            // echo $e->getMessage();
        }
    }
}